<?php

namespace App\Http\Controllers;

use App\Enums\DeviceStatusEnum;
use App\Enums\LogEntityEnum;
use App\Enums\LogTypeEnum;
use App\Models\Device;
use App\Models\DeviceTime;
use App\Models\DeviceTimeTransactions;
use App\Models\RptDeviceTimeTransactions;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DeviceMangementController extends Controller
{
    public function GetDevices(Request $request)
    {
        try {
            $devices = Device::with(['deviceStatus', 'increments' => function ($query) {
                $query->where('Active', true);
            }])
                ->leftJoin('DeviceTimeTransactions', function ($join) {
                    $join->on('DeviceTimeTransactions.DeviceID', '=', 'Devices.DeviceID')
                        ->where('DeviceTimeTransactions.Active', true)
                        ->whereIn('DeviceTimeTransactions.TransactionType', [\App\Enums\TimeTransactionTypeEnum::START, \App\Enums\TimeTransactionTypeEnum::EXTEND]);
                })
                ->select('Devices.*') // Keep the distinct device columns
                ->distinct()  // Ensure unique devices
                ->orderByRaw('CASE WHEN DeviceTimeTransactions.TransactionID IS NOT NULL THEN 0 ELSE 1 END') // Running devices first
                ->orderBy('Devices.ExternalDeviceName') // Then sort by name
                ->get();

            return view('devicemanagement', compact('devices'));
        } catch (\Exception $e) {
            Log::error('Error fetching devices', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to retrieve devices.'], 500);
        }
    }

    public function GetDeviceDetails($id)
    {
        try {
            $device = Device::with('deviceStatus')->findOrFail($id);
            $baseTime = DeviceTime::where('DeviceID', $id)->where('TimeTypeID', DeviceTime::TIME_TYPE_BASE)->first();
            $openTime = DeviceTime::where('DeviceID', $id)->where('TimeTypeID', DeviceTime::TIME_TYPE_OPEN)->first();
            $deviceTimes = DeviceTime::where('DeviceID', $id)->where('TimeTypeID', DeviceTime::TIME_TYPE_INCREMENT)->get();

            $deviceTimeTransactions = DeviceTimeTransactions::where('DeviceID', $id)->where('Active', true)->get();

            $totalTime = $deviceTimeTransactions->sum('Duration');
            $totalRate = $deviceTimeTransactions->sum('Rate');

            $rptDeviceTimeTransactions = RptDeviceTimeTransactions::whereDate('Time', '>=', Carbon::today()->subDays(1))
                ->whereDate('Time', '<=', Carbon::today())
                ->where('DeviceID', $id)
                ->with('creator', 'device') // Make sure 'device' is loaded
                ->get();

            return view('device-detail', compact('device', 'baseTime', 'openTime', 'deviceTimes', 'deviceTimeTransactions', 'totalTime', 'totalRate', 'rptDeviceTimeTransactions'));
        } catch (\Exception $e) {
            Log::error('Error fetching device details for DeviceID: ' . $id, ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to retrieve device details.'], 500);
        }
    }

    public function UpdateDeviceOperationDate($id)
    {
        $device = Device::findOrFail($id);
        $device->DeviceStatusID = DeviceStatusEnum::INACTIVE_ID;
        $device->OperationDate = Carbon::now();
        $device->save();

        LoggingController::InsertLog(LogEntityEnum::DEVICE, $device->DeviceID, $device->DeviceName . ': Deployment', LogTypeEnum::INFO, auth()->id());

        return redirect()->route('device.detail', $id)->with('status', 'Device deployed successfully');
    }

    public function InsertDeviceDetails(Request $request)
    {
        $validatedData = $request->validate([
            'DeviceName' => 'required|string|max:255',
            'IPAddress' => 'required|ip',
            'DeviceStatusID' => 'required|integer',
        ]);

        $device = new Device();
        try {
            $device->DeviceName = $validatedData['DeviceName'];
            $device->ExternalDeviceName = $validatedData['DeviceName'];
            $device->IPAddress = $validatedData['IPAddress'];
            $device->WatchdogInterval = 30;
            $device->RemainingTimeNotification = 0;
            $device->DeviceStatusID = $validatedData['DeviceStatusID'];
            $device->save();

            LoggingController::InsertLog(LogEntityEnum::DEVICE, $device->DeviceID, 'Device ' . $device->ExternalDeviceName . ' registered.', LogTypeEnum::INFO, auth()->id());

            return response()->json(['success' => true, 'message' => 'Device registered successfully.', 'device_id' => $device->DeviceID], 201);
        } catch (\Exception $e) {
            Log::error('Error inserting device: ' . $device->DeviceName, ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to register device.', 'error' => $e->getMessage()], 500);
        }
    }

    public function UpdateDeviceDetails(Request $request)
    {
        $validatedData = $request->validate([
            'DeviceID' => 'required|integer',
            'DeviceName' => 'required|string|max:255',
            'IPAddress' => 'required|ip',
            'DeviceStatusID' => 'required|integer',
        ]);

        $device = Device::with('deviceStatus')->findOrFail($validatedData['DeviceID']);
        try {
            $device->DeviceName = $validatedData['DeviceName'];
            $device->ExternalDeviceName = $validatedData['DeviceName'];
            $device->DeviceStatusID = DeviceStatusEnum::PENDING_ID;
            $device->IPAddress = $validatedData['IPAddress'];
            $device->save();

            LoggingController::InsertLog(LogEntityEnum::DEVICE, $device->DeviceID, $device->DeviceName . ': Device info update through AP', LogTypeEnum::INFO, null);

            return response()->json(['success' => true, 'message' => 'Device updated successfully.', 'device_id' => $device->DeviceID], 201);
        } catch (\Exception $e) {
            Log::error('Error updating device: ' . $device->DeviceID, ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to update device.', 'error' => $e->getMessage()], 500);
        }
    }

    public function DeleteDevice($id)
    {
        $device = Device::find($id);

        if (!$device) {
            Log::error('Device not found for deletion', ['DeviceID' => $id]);
            return response()->json(['success' => false, 'message' => 'Device not found.'], 404);
        }

        $deviceIpAddress = $device->IPAddress;

        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->delete("http://$deviceIpAddress/api/reset");

            if ($response->getStatusCode() == 200) {
                LoggingController::InsertLog(LogEntityEnum::DEVICE, $device->DeviceID, 'Device ' . $device->ExternalDeviceName . ' deleted.', LogTypeEnum::INFO, auth()->id());
                $device->delete();
                return response()->json(['success' => true, 'message' => 'Device deleted successfully.']);
            }
            return response()->json(['success' => false, 'message' => 'Failed to reset the device.'], $response->getStatusCode());
        } catch (\Exception $e) {
            Log::error('Error deleting device: ' . $device->DeviceID, ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function DeviceTest($id)
    {
        $device = Device::find($id);

        if (!$device) {
            return response()->json(['success' => false, 'message' => 'Device not found.'], 404);
        }

        $deviceIpAddress = $device->IPAddress;

        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->get("http://$deviceIpAddress/api/test");

            if ($response->getStatusCode() == 200) {
                return response()->json(['success' => true, 'message' => 'Device tested successfully.']);
            }
            return response()->json(['success' => false, 'message' => 'Failed to test the device.'], $response->getStatusCode());
        } catch (\Exception $e) {
            Log::error('Error fetching devices', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function DisableDevice($id)
    {
        $device = Device::find($id);

        if (!$device) {
            return response()->json(['success' => false, 'message' => 'Device not found.'], 404);
        }

        $deviceIpAddress = $device->IPAddress;

        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->get("http://$deviceIpAddress/api/disable");

            if ($response->getStatusCode() == 200) {
                $device->DeviceStatusID = DeviceStatusEnum::DISABLED_ID;
                $device->save();

                LoggingController::InsertLog(LogEntityEnum::DEVICE, $device->DeviceID, 'Device ' . $device->ExternalDeviceName . ' disabled.', LogTypeEnum::INFO, auth()->id());

                return response()->json(['success' => true, 'message' => 'Device disabled successfully']);
            }

            return response()->json(['success' => false, 'message' => 'Failed to disable the device'], $response->getStatusCode());
        } catch (\Exception $e) {
            Log::error('Error fetching devices', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function EnableDevice($id)
    {
        $device = Device::find($id);

        if (!$device) {
            return response()->json(['success' => false, 'message' => 'Device not found.'], 404);
        }

        $deviceIpAddress = $device->IPAddress;

        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->get("http://$deviceIpAddress/api/enable");

            if ($response->getStatusCode() == 200) {
                $device->DeviceStatusID = DeviceStatusEnum::INACTIVE_ID;
                $device->save();

                LoggingController::InsertLog(LogEntityEnum::DEVICE, $device->DeviceID, 'Device ' . $device->ExternalDeviceName . ' enabled.', LogTypeEnum::INFO, auth()->id());

                return response()->json(['success' => true, 'message' => 'Device enabled successfully']);
            }

            return response()->json(['success' => false, 'message' => 'Failed to enable the device'], $response->getStatusCode());
        } catch (\Exception $e) {
            Log::error('Error fetching devices', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function UpdateDeviceName(Request $request)
    {
        $request->validate([
            'external_device_id' => 'required|integer|exists:devices,DeviceID',
            'external_device_name' => 'required|string|max:255',
        ]);

        $device = Device::findOrFail($request->external_device_id);
        $deviceIpAddress = $device->IPAddress;

        $orginalName = $device->ExternalDeviceName;
        $newDeviceName = $request->external_device_name;

        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->post("http://{$deviceIpAddress}/api/updateDeviceName", [
                'body' => $newDeviceName,
                'headers' => [
                    'Content-Type' => 'text/plain',
                ],
            ]);

            if ($response->getStatusCode() == 200) {


                $device->ExternalDeviceName = $newDeviceName;
                $device->save();

                LoggingController::InsertLog(LogEntityEnum::DEVICE, $device->DeviceID, 'Changed device name from ' . $orginalName . ' to ' . $device->ExternalDeviceName, LogTypeEnum::INFO, auth()->id());

                return response()->json(['success' => true, 'message' => 'Device name updated successfully.']);
            }
            return response()->json(['success' => false, 'message' => 'Failed to update the device name.'], $response->getStatusCode());
        } catch (\Exception $e) {
            Log::error('Error fetching devices', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function UpdateWatchdogInterval(Request $request)
    {
        $request->validate([
            'deviceId' => 'required|integer',
            'watchdogInterval' => 'required|integer|min:1'
        ]);

        $device = Device::findOrFail($request->deviceId);
        $deviceIpAddress = $device->IPAddress;
        $originalWatchdogInterval = $device->WatchdogInterval;
        $newWatchdogInterval = $request->watchdogInterval;

        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->post("http://{$deviceIpAddress}/api/setWatchdogInterval", [
                'body' => $newWatchdogInterval,
                'headers' => [
                    'Content-Type' => 'text/plain',
                ],
            ]);

            if ($response->getStatusCode() == 200) {
                // Update the interval only if the device update was successful
                $device->WatchdogInterval = $newWatchdogInterval;
                $device->save();

                LoggingController::InsertLog(LogEntityEnum::DEVICE, $device->DeviceID, $device->DeviceName . ': Changed device watchdog interval from ' . $originalWatchdogInterval . ' to ' . $newWatchdogInterval, LogTypeEnum::INFO, auth()->id());

                return response()->json(['success' => true, 'message' => 'Device watchdog interval updated successfully.']);
            }
            return response()->json(['success' => false, 'message' => 'Failed to update the device watchdog interval.'], $response->getStatusCode());
        } catch (\Exception $e) {
            Log::error('Error fetching devices', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function UpdateRemainingTimeNotification(Request $request)
    {
        $request->validate([
            'deviceId' => 'required|integer',
            'remainingTime' => 'required|integer|min:1'
        ]);

        $device = Device::findOrFail($request->deviceId);
        $originalRemainingTime = $device->RemainingTimeNotification;
        $newRemainingTime = $request->remainingTime;

        try {
            // Update the interval only if the device update was successful
            $device->RemainingTimeNotification = $newRemainingTime;
            $device->save();

            LoggingController::InsertLog(LogEntityEnum::DEVICE, $device->DeviceID, $device->DeviceName . ': Changed device remaining time notification from ' . $originalRemainingTime . ' to ' . $newRemainingTime, LogTypeEnum::INFO, auth()->id());

            return response()->json(['success' => true, 'message' => 'Device remaining time notification updated successfully.']);
        } catch (\Exception $e) {
            Log::error('Error fetching devices', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
