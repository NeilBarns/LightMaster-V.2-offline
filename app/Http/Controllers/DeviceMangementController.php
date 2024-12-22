<?php

namespace App\Http\Controllers;

use App\Enums\DeviceStatusEnum;
use App\Enums\LogEntityEnum;
use App\Enums\LogTypeEnum;
use App\Models\Device;
use App\Models\DeviceTime;
use App\Models\DeviceTimeTransactions;
use App\Models\RptDeviceTimeTransactions;
use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DeviceMangementController extends Controller
{
    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

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
                // ->orderByRaw('CASE WHEN DeviceTimeTransactions.TransactionID IS NOT NULL THEN 0 ELSE 1 END') // Running devices first
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
            'IPAddress' => 'required|string|max:255',
            'ClientName' => 'required|string|max:255',
            'DeviceStatusID' => 'required|integer',
        ]);

        $device = new Device();
        try {
            $deviceCount = Device::count();

            $deviceName = $validatedData['ClientName'] . '-' . $deviceCount + 1;

            $device->DeviceName = $deviceName;
            $device->ExternalDeviceName = $deviceName;
            $device->IPAddress = $validatedData['IPAddress'];
            $device->ClientName = $validatedData['ClientName'];
            $device->WatchdogInterval = 30;
            $device->RemainingTimeNotification = 0;
            $device->DeviceStatusID = $validatedData['DeviceStatusID'];
            $device->IsOnline = true;
            $device->last_heartbeat = Carbon::now();
            $device->save();

            LoggingController::InsertLog(LogEntityEnum::DEVICE, $device->DeviceID, 'Device ' . $device->ExternalDeviceName . ' registered.', LogTypeEnum::INFO, auth()->id());

            $firebasePath = '/' . $device->ClientName . '/devices/' . $device->DeviceID;
            $firebaseData = [
                'command' => 'initialization',
                'span' => 0,
            ];

            // Check if data was successfully set in Firebase
            $firebaseAcknowledged = $this->firebase->setData($firebasePath, $firebaseData);
            
            if (!$firebaseAcknowledged) {
                Log::error('Device registered, but failed to set data in Firebase.');
                return response()->json(['success' => false, 'message' => 'Failed to register device.'], 500);
            }

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
            'IPAddress' => 'required|string|max:255',
            'DeviceStatusID' => 'required|integer',
        ]);

        $device = Device::with('deviceStatus')->findOrFail($validatedData['DeviceID']);
        try {
            $device->IPAddress = $validatedData['IPAddress'];
            $device->DeviceStatusID = DeviceStatusEnum::PENDING_ID;
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

        try {
            $firebasePath = '/' . $device->ClientName . '/devices/' . $device->DeviceID;
            $firebaseData = [
                'command' => 'delete',
                'span' => 0
            ];

            // Check if data was successfully set in Firebase
            $firebaseAcknowledged = $this->firebase->setData($firebasePath, $firebaseData);

            if ($firebaseAcknowledged) {
                LoggingController::InsertLog(LogEntityEnum::DEVICE, $device->DeviceID, 'Device ' . $device->ExternalDeviceName . ' deleted.', LogTypeEnum::INFO, auth()->id());
                $device->delete();
                return response()->json(['success' => true, 'message' => 'Device deleted successfully.']);
            }

            return response()->json(['success' => false, 'message' => 'Failed to delete device.'], 500);
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

        try {

            // Add to Firebase
            $firebasePath = '/' . $device->ClientName . '/devices/' . $device->DeviceID;
            $firebaseData = [
                'command' => 'test',
                'span' => 0
            ];

            // Check if data was successfully set in Firebase
            $firebaseAcknowledged = $this->firebase->setData($firebasePath, $firebaseData);
            
            if (!$firebaseAcknowledged) {
                return response()->json(['success' => false, 'message' => 'Failed to test the device.'], 500);
            }

            return response()->json(['success' => true, 'message' => 'Device tested successfully.']);
        } 
        catch (\Exception $e)
        {
            Log::error('Error testing device', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function DisableDevice($id)
    {
        $device = Device::find($id);

        if (!$device) {
            return response()->json(['success' => false, 'message' => 'Device not found.'], 404);
        }

        try {
            $firebasePath = '/' . $device->ClientName . '/devices/' . $device->DeviceID;
            $firebaseData = [
                'command' => 'disable',
                'span' => 0
            ];

            // Check if data was successfully set in Firebase
            $firebaseAcknowledged = $this->firebase->setData($firebasePath, $firebaseData);

            if ($firebaseAcknowledged) {
                $device->DeviceStatusID = DeviceStatusEnum::DISABLED_ID;
                $device->save();

                LoggingController::InsertLog(LogEntityEnum::DEVICE, $device->DeviceID, 'Device ' . $device->ExternalDeviceName . ' disabled.', LogTypeEnum::INFO, auth()->id());

                return response()->json(['success' => true, 'message' => 'Device disabled successfully']);
            }

            return response()->json(['success' => false, 'message' => 'Failed to disable the device.'], 500);
        } catch (\Exception $e) {
            Log::error('Failed to disable the device', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function EnableDevice($id)
    {
        $device = Device::find($id);

        if (!$device) {
            return response()->json(['success' => false, 'message' => 'Device not found.'], 404);
        }

        try {
            $firebasePath = '/' . $device->ClientName . '/devices/' . $device->DeviceID;
            $firebaseData = [
                'command' => 'enable',
                'span' => 0
            ];

            // Check if data was successfully set in Firebase
            $firebaseAcknowledged = $this->firebase->setData($firebasePath, $firebaseData);

            if ($firebaseAcknowledged) {
                $device->DeviceStatusID = DeviceStatusEnum::INACTIVE_ID;
                $device->save();

                LoggingController::InsertLog(LogEntityEnum::DEVICE, $device->DeviceID, 'Device ' . $device->ExternalDeviceName . ' enabled.', LogTypeEnum::INFO, auth()->id());

                return response()->json(['success' => true, 'message' => 'Device enabled successfully']);
            }

            return response()->json(['success' => false, 'message' => 'Failed to enable the device.'], 500);
        } catch (\Exception $e) {
            Log::error('Failed to enable the device', ['error' => $e->getMessage()]);
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
        
        $orginalName = $device->ExternalDeviceName;
        $newDeviceName = $request->external_device_name;

        try {
            $device->ExternalDeviceName = $newDeviceName;
            $device->save();

            LoggingController::InsertLog(LogEntityEnum::DEVICE, $device->DeviceID, 'Changed device name from ' . $orginalName . ' to ' . $device->ExternalDeviceName, LogTypeEnum::INFO, auth()->id());

            return response()->json(['success' => true, 'message' => 'Device name updated successfully.']);
        } catch (\Exception $e) {
            Log::error('Failed to update the device name', ['error' => $e->getMessage()]);
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
        $originalWatchdogInterval = $device->WatchdogInterval;
        $newWatchdogInterval = $request->watchdogInterval;

        try {
            $firebasePath = '/' . $device->ClientName . '/devices/' . $device->DeviceID;
            $firebaseData = [
                'command' => 'setWatchdogInterval',
                'span' => $newWatchdogInterval
            ];

            // Check if data was successfully set in Firebase
            $firebaseAcknowledged = $this->firebase->setData($firebasePath, $firebaseData);

            if ($firebaseAcknowledged) {
                $device->WatchdogInterval = $newWatchdogInterval;
                $device->save();

                LoggingController::InsertLog(LogEntityEnum::DEVICE, $device->DeviceID, $device->DeviceName . ': Changed device watchdog interval from ' . $originalWatchdogInterval . ' to ' . $newWatchdogInterval, LogTypeEnum::INFO, auth()->id());

                return response()->json(['success' => true, 'message' => 'Device watchdog interval updated successfully.']);
            }

            return response()->json(['success' => false, 'message' => 'Failed to enable the device.'], 500);
        } catch (\Exception $e) {
            Log::error('Failed to update the device watchdog interval', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to update the device watchdog interval.'], 500);
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

    public function UpdateHeartbeat(Request $request)
    {
        $device = Device::find($request->device_id);

        try {
            if ($device) {
                $device->last_heartbeat = Carbon::now();
                $device->IsOnline = true;
                $device->save();
        
                return response()->json(['success' => true, 'message' => 'Heartbeat received']);
            }
        }
        catch (\Exception $e) {
            Log::error('Error updating device heartbeat', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
