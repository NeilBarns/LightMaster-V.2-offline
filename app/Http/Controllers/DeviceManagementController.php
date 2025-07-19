<?php

namespace App\Http\Controllers;

use App\Enums\DeviceHeartbeatStatusEnum;
use App\Enums\DeviceStatusEnum;
use App\Enums\ExchangeStatusEnum;
use App\Enums\LogEntityEnum;
use App\Enums\LogTypeEnum;
use App\Enums\NotificationLevelEnum;
use App\Enums\NotificationSourceEnum;
use App\Models\Device;
use App\Models\DeviceTime;
use App\Models\DeviceTimeTransactions;
use App\Models\RptDeviceTimeTransactions;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Events\DeviceAddRemoveUpdates;
use App\Events\DeviceHeartbeatUpdates;
use App\Events\DeviceTransactionUpdates;
use App\Events\NotificationUpdates;
use App\Models\DeviceDisplay;
use App\Models\DeviceHeartbeatStatusResponse;
use App\Models\DeviceTimeTransactionsResponse;
use App\Models\Exchange;
use App\Models\NotificationResponse;
use App\Models\Notifications;
use App\Models\TimeTransactionQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\DB;

use function PHPSTORM_META\elementType;

class DeviceManagementController extends Controller
{
    public function GetDevices(Request $request)
    {
        try {
            $devices = Device::with([
                'deviceStatus',
                'increments' => function ($query) {
                    $query->where('Active', true);
                },
                'device_times' // Ensure this relationship is included
            ])
            ->leftJoin('DeviceTimeTransactions', function ($join) {
                $join->on('DeviceTimeTransactions.DeviceID', '=', 'Devices.DeviceID')
                    ->where('DeviceTimeTransactions.Active', true)
                    ->whereIn('DeviceTimeTransactions.TransactionType', [
                        \App\Enums\TimeTransactionTypeEnum::START,
                        \App\Enums\TimeTransactionTypeEnum::EXTEND,
                    ]);
            })
            ->select('Devices.*')
            ->distinct()
            // ->whereNotIn('Devices.DeviceStatusID', [\App\Enums\DeviceStatusEnum::DELETED_ID, \App\Enums\DeviceStatusEnum::PENDINGDELETE_ID]) // Exclude deleted devices
            ->whereNotIn('Devices.DeviceStatusID', [\App\Enums\DeviceStatusEnum::DELETED_ID]) // Exclude deleted devices
            ->orderBy('Devices.created_at')
            ->get();

            $notificationsController = new NotificationsController();
            $notifications = $notificationsController->GetNotifications();
            
            return view('devicemanagement', compact('devices', 'notifications'));
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
            $freeTimeLimit = DeviceTime::where('DeviceID', $id)->where('TimeTypeID', DeviceTime::TIME_TYPE_FREE)->first();

            $notificationsController = new NotificationsController();
            $notifications = $notificationsController->GetNotifications();

            return view('device-detail', compact('device', 'baseTime', 'openTime', 'freeTimeLimit', 'deviceTimes', 'notifications'));
        } catch (\Exception $e) {
            Log::error('Error fetching device details for DeviceID: ' . $id, ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to retrieve device details.'], 500);
        }
    }

    public function DeployDevice($id)
    {
        $device = Device::findOrFail($id);

        if (!$device) {
            return response()->json(['success' => false, 'message' => 'Device not found.'], 404);
        }

        try
        {
            $device->DeviceStatusID = DeviceStatusEnum::INACTIVE_ID;
            $device->OperationDate = Carbon::now();
            $device->save();

            LoggingController::InsertLog(LogEntityEnum::DEVICE, $device->DeviceID, $device->DeviceName . ': Deployment', LogTypeEnum::INFO, auth()->id());

            // return response()->json([
            //     'success' => 'Device deployed successfully'
            // ]);
            return redirect()->route('device.detail', $id)->with('status', 'Device deployed successfully');
        }
        catch (\Exception $e) {
            // Log unexpected errors
            Log::error('Unexpected error deploying device ' . $id, ['error' => $e->getMessage()]);
            return response()->json(['error' => 'An unexpected error occurred. Please try again later.'], 500);
        }
    }

    public function DisableDevice($id)
    {
        $device = Device::find($id);

        if (!$device) {
            return response()->json(['success' => false, 'message' => 'Device not found.'], 404);
        }

        try {
            $device->DeviceStatusID = DeviceStatusEnum::DISABLED_ID;
            $device->save();

            LoggingController::InsertLog(LogEntityEnum::DEVICE, $device->DeviceID, 'Device ' . $device->DeviceName . ' disabled.', LogTypeEnum::INFO, auth()->id());

            return redirect()->route('device.detail', $id)->with('status', 'Device disabled successfully');
        }
        catch (\Exception $e) {
            // Log unexpected errors
            Log::error('Unexpected error disabling device ' . $id, ['error' => $e->getMessage()]);
            return response()->json(['error' => 'An unexpected error occurred. Please try again later.'], 500);
        }
    }

    public function EnableDevice($id)
    {
        $device = Device::find($id);

        if (!$device) {
            return response()->json(['success' => false, 'message' => 'Device not found.'], 404);
        }

        try {
            $device->DeviceStatusID = DeviceStatusEnum::INACTIVE_ID;
            $device->save();

            LoggingController::InsertLog(LogEntityEnum::DEVICE, $device->DeviceID, 'Device ' . $device->DeviceName . ' enabled.', LogTypeEnum::INFO, auth()->id());

            // return response()->json([
            //     'success' => 'Device enabled successfully'
            // ]);
            return redirect()->route('device.detail', $id)->with('status', 'Device enabled successfully');
        }
        catch (\Exception $e) {
            // Log unexpected errors
            Log::error('Unexpected error disabling device ' . $id, ['error' => $e->getMessage()]);
            return response()->json(['error' => 'An unexpected error occurred. Please try again later.'], 500);
        }
    }

    public function InsertDeviceDetails(Request $request)
    {
        $validatedData = $request->validate([
            'SerialNumber' => 'required|string|max:255',
            'IPAddress' => 'required|string|max:255',
        ]);

        $device = new Device();
        
        try {
            // Check if SerialNumber already exists
            $existingDevice = Device::where('SerialNumber', $validatedData['SerialNumber'])->first();

            if ($existingDevice) {
                // Forward to update flow
                $request->merge([
                    'DeviceID' => $existingDevice->DeviceID,
                ]);
                return $this->UpdateDeviceDetails($request);
            }

            $deviceCount = Device::count();

            $deviceName = config('app.default_device_name_prefix') . '-' . ($deviceCount + 1);

            $device->DeviceName = $deviceName;
            $device->IPAddress = $validatedData['IPAddress'];
            $device->SerialNumber = $validatedData['SerialNumber'];
            $device->WatchdogInterval = env('DEFAULT_WATCHDOG_INTERVAL');
            $device->RemainingTimeNotification = env('DEFAULT_REMAINING_TIME_INTERVAL');
            $device->DeviceStatusID = DeviceStatusEnum::PENDING_ID;
            $device->IsOnline = true;
            $device->last_heartbeat = Carbon::now();
            $device->save();

            LoggingController::InsertLog(LogEntityEnum::DEVICE, $device->DeviceID, 'Device ' . $device->DeviceName . ' registered.', LogTypeEnum::INFO, 999999);

            event(new DeviceAddRemoveUpdates());

            return response()->json(['success' => true, 'message' => 'Device registered successfully.', 
                                     'device_id' => $device->DeviceID, 
                                     'default_watchdog_interval' => env('DEFAULT_WATCHDOG_INTERVAL'),], 201);
        } catch (\Exception $e) {
            Log::error('Error inserting device: ' . $device->DeviceName, ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to register device.', 'error' => $e->getMessage()], 500);
        }
    }

    public function ExchangeDeviceDisplay($fromDeviceID, $toDeviceID)
    {
        DeviceDisplay::where('DeviceID', $fromDeviceID)
        ->update(['DeviceID' => $toDeviceID]);
    }

    public function ExchangeDeviceTime($fromDeviceID, $toDeviceID)
    {
        DeviceTime::where('DeviceID', $fromDeviceID)
        ->update(['DeviceID' => $toDeviceID]);
    }

    public function ExchangeDeviceTimeTransactions($fromDeviceID, $toDeviceID)
    {
        DB::transaction(function () use ($fromDeviceID, $toDeviceID) {
            if (DeviceTimeTransactions::where('DeviceID', $fromDeviceID)->exists()) {
                DeviceTimeTransactions::where('DeviceID', $fromDeviceID)
                    ->update(['DeviceID' => $toDeviceID]);
            }
        });
    }

    public function UpdateDeviceDetails(Request $request)
    {
        // explicitly decode the JSON payload
        $data = $request->json()->all();

        $validatedData = validator($data, [
            'DeviceID' => 'required|integer',
            'IPAddress' => 'required|string|max:255',
        ])->validate();

        $device = Device::with('deviceStatus')->findOrFail($validatedData['DeviceID']);

        try {
            $oldIP = $device->IPAddress;

            // $device->DeviceStatusID = DeviceStatusEnum::PENDING_ID;
            $device->IPAddress = $validatedData['IPAddress'];
            $device->save();

            if ($oldIP != $validatedData['IPAddress'])
            {
                LoggingController::InsertLog(LogEntityEnum::DEVICE, $device->DeviceID, $device->DeviceName . ': Device info update through AP. Old IP: ' . $oldIP . ' to New IP: ' . $validatedData['IPAddress'], LogTypeEnum::INFO, 999999);
            }

            //event(new DeviceAddRemoveUpdates());

            return response()->json(['success' => true, 'message' => 'Device updated successfully.', 
                                     'device_id' => $device->DeviceID, 
                                     'device_IP_address' => env('DEFAULT_IP'), 'device_gateway' => env('DEFAULT_IP'), 'device_subnet' => env('DEFAULT_IP'),
                                     'default_watchdog_interval' => '0',], 201);
        } catch (\Exception $e) {
            Log::error('Error updating device: ' . $device->DeviceID, ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to update device.', 'error' => $e->getMessage()], 500);
        }
    }

    public function RequestDeleteDevice($id)
    {
        $device = Device::find($id);

        if (!$device) {
            Log::error('Device not found for deletion', ['DeviceID' => $id]);
            return response()->json(['success' => false, 'message' => 'Device not found.'], 404);
        }

        $deviceIpAddress = $device->IPAddress;

        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('POST', "http://$deviceIpAddress/api/delete", [
                'json' => [
                    'device_id' => $id
                ]
            ]);

            if ($response->getStatusCode() == 200) {
                $device->DeviceStatusID = DeviceStatusEnum::PENDINGDELETE_ID;
                $device->save();

                LoggingController::InsertLog(LogEntityEnum::DEVICE, $device->DeviceID, 'Sent Device (' . $device->DeviceName . ') delete request.', LogTypeEnum::INFO, auth()->id());
                return response()->json(['success' => true, 'message' => 'Device delete request sent successfully.']);
            }
            return $this->handleErrorResponse($response, $id);
        }
        catch (\GuzzleHttp\Exception\RequestException $e) 
        {
            $errorResponse = $e->getResponse();

            if ($errorResponse) {
                $responseBody = json_decode($errorResponse->getBody(), true);
                $responseBodyResponse = $responseBody['response'] ?? "";

                if (str_contains($responseBodyResponse, "Device does not match the configuration")) {
                    $device->DeviceStatusID = DeviceStatusEnum::DELETED_ID;
                    $device->DeletionDate = Carbon::now();
                    $device->save();
                    
                    LoggingController::InsertLog(LogEntityEnum::DEVICE, $device->DeviceID, 'Device (' . $device->DeviceName . ') deleted successfully.', LogTypeEnum::INFO, auth()->id());

                    event(new DeviceAddRemoveUpdates()); 

                    return response()->json(['success' => true, 'message' => 'Device deletion successful.'], 200);
                }                
            }
            else
            {
                return $this->handleGuzzleException($e, $id);   
            }
        } catch (\Exception $e) {
            Log::error('Unexpected error requesting to delete device ' . $id, ['error' => $e->getMessage()]);
            // return response()->json(['success' => false, 'message' => 'Device ' . $device->DeviceName . ' is unresponsive. Checking status...']);

            $device->DeviceStatusID = DeviceStatusEnum::DELETED_ID;
            $device->DeletionDate = Carbon::now();
            $device->save();
            
            LoggingController::InsertLog(LogEntityEnum::DEVICE, $device->DeviceID, 'Device (' . $device->DeviceName . ') deleted successfully.', LogTypeEnum::INFO, auth()->id());

            event(new DeviceAddRemoveUpdates()); 

            return response()->json(['success' => true, 'message' => 'Device deletion successful.'], 200);
        }
    }

    public function ResponseDeleteDevice(Request $request)
    {
        $validatedData = $request->validate([
            'DeviceID' => 'required|integer',
            'Success' => 'required|boolean',
            'ErrorMessage' => 'required_if:Success,false|string|nullable'
        ]);
        
        $device = Device::find($validatedData['DeviceID']);

        try {
            if ($validatedData['Success']) {
                // Handle successful deletion
                $device->DeviceStatusID = DeviceStatusEnum::DELETED_ID;
                $device->DeletionDate = Carbon::now();
                $device->save();
                
                LoggingController::InsertLog(LogEntityEnum::DEVICE, $device->DeviceID, 'Device (' . $device->DeviceName . ') deleted successfully.', LogTypeEnum::INFO, 999999);

                event(new DeviceAddRemoveUpdates()); 

                return response()->json(['success' => true, 'message' => 'Device deletion successful.'], 200);
            } else {
                // Handle error case
                Log::error('Error deleting device. Device error message: ' . $device->DeviceID, ['error' => $validatedData['ErrorMessage']]);
                LoggingController::InsertLog(LogEntityEnum::DEVICE, $device->DeviceID, 'Device (' . $device->DeviceName . ') received delete error.', LogTypeEnum::ERROR, 999999);
                return response()->json(['success' => false, 'message' => 'Device deletion unsuccessful.'], 400);
            }
        }
        catch (\Exception $e) {
            Log::error('Error deleting device: ' . $device->DeviceID, ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Device deletion unsuccessful.'], 500);
        }
    }

    public function UpdateDeviceName(Request $request)
    {
        $validatedData = $request->validate([
            'external_device_id' => 'required|integer|exists:devices,DeviceID',
            'external_device_name' => 'required|string|max:255',
        ]);

        $device = Device::findOrFail($request->external_device_id);
        $newDeviceName = $request->external_device_name;

        try {
            $orginalName = $device->DeviceName;
            
            $device->DeviceName = $newDeviceName;
                $device->save();

            LoggingController::InsertLog(LogEntityEnum::DEVICE, $device->DeviceID, 'Changed device name from ' . $orginalName . ' to ' . $device->DeviceName, LogTypeEnum::INFO, auth()->id());

            return response()->json(['success' => true, 'message' => 'Device name updated successfully.']);
            
        } catch (\Exception $e) {
            Log::error('Error updating device name', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function TestDevice($id)
    {
        $device = Device::find($id);

        if (!$device) {
            return response()->json(['success' => false, 'message' => 'Device not found.'], 404);
        }

        $deviceIpAddress = $device->IPAddress;

        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('POST', "http://$deviceIpAddress/api/test", [
                'json' => [
                    'device_id' => $id
                ]
            ]);

            if ($response->getStatusCode() == 200) {
                return response()->json(['success' => true, 'message' => 'Device tested successfully.']);
            }
            return $this->handleErrorResponse($response, $id);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return $this->handleGuzzleException($e, $id);
       } catch (\Exception $e) {
           // Log unexpected errors
           Log::error('Unexpected error testing the device ' . $id, ['error' => $e->getTraceAsString()]);
           // return response()->json(['error' => 'An unexpected error occurred. Please try again later.'], 500);
           return response()->json(['success' => false, 'message' => 'Device ' . $device->DeviceName . ' is unresponsive. Please checking status.']);
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
            'remainingTime' => 'required|numeric',
            'device_id' => 'required|integer|exists:Devices,DeviceID',
        ]);

        $device = Device::findOrFail($request->device_id);

        if (!$device) {
            return response()->json(['success' => false, 'message' => 'Device not found.'], 404);
        }
        
        $originalRemainingTime = $device->RemainingTimeNotification;
        $newRemainingTime = $request->remainingTime;

        try {
            $device->RemainingTimeNotification = $newRemainingTime;
            $device->save();

            LoggingController::InsertLog(LogEntityEnum::DEVICE, $device->DeviceID, $device->DeviceName . ': Changed device remaining time notification from ' . $originalRemainingTime . ' to ' . $newRemainingTime, LogTypeEnum::INFO, auth()->id());

            return response()->json(['success' => true, 'message' => 'Device remaining time notification updated successfully.']);
        } 
        catch (\Exception $e) 
        {
            Log::error('Error updating remaining time on device ' . $request->device_id, ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to update device remaining time', 'error' => $e->getMessage()], 500);
        }
    }

    public function UpdateHeartbeatDeviceStatusToOffline()
    {
        $now = Carbon::now();

        try {
            $updatedCount = Device::where('IsOnline', true)
                ->whereRaw('TIMESTAMPDIFF(SECOND, last_heartbeat, ?) >= ?', [$now, 90]) // 1 minute 30 seconds = 90 seconds
                ->update(['IsOnline' => false]);

            $offlineDevices = Device::where('IsOnline', false)
                ->whereIn('DeviceStatusID', [
                    DeviceStatusEnum::PENDING_ID,
                    DeviceStatusEnum::RUNNING_ID,
                    DeviceStatusEnum::INACTIVE_ID,
                    DeviceStatusEnum::PAUSE_ID,
                    DeviceStatusEnum::RESUME_ID,
                    DeviceStatusEnum::STARTFREE_ID
                ])
                // ->select('DeviceID', 'DeviceStatusID')
                ->distinct()
                ->get();

            if ($offlineDevices->isEmpty()) {
                return response()->json(['success' => true, 'message' => 'No offline devices found.']);
            }

            // Dispatch events for offline devices
            foreach ($offlineDevices as $device) 
            {
                $deviceTimeController = new DeviceTimeController();

                if ($device->DeviceStatusID == DeviceStatusEnum::RUNNING_ID)
                {
                    $notification = Notifications::create([
                        'Notification' => 'Node ' . $device->DeviceName . ' was offline while a timer was active. The timer has been paused to prevent any interruption.',
                        'NotificationLevelID' => NotificationLevelEnum::WARNING_ID,
                        'NotificationSourceID' => NotificationSourceEnum::DEVICE_ID,
                        'DeviceID' => $device->DeviceID
                    ]);

                    $notificationResponse = new NotificationResponse([
                        'NotificationID' => $notification->NotificationID,
                        'Notification' => $notification->Notification,
                        'NotificationLevelID' => $notification->NotificationLevelID,
                        'NotificationSourceID' => $notification->NotificationSourceID,
                        'DeviceID' => $device->DeviceID,
                        'CreatedDate' => $notification->created_at
                    ]);
    
                    event(new NotificationUpdates($notificationResponse));

                    $latestDeviceThreadsNumber = DeviceTimeTransactions::where('DeviceID', $device->DeviceID)
                    ->max('Thread');

                    $calculabletransactions = DeviceTimeTransactions::where('DeviceID', $device->DeviceID)
                            ->where('Thread', $latestDeviceThreadsNumber)
                            ->where('Active', true)
                            ->get(['Duration', 'Rate', 'IsOpenTime', 'TransactionType', 'TransactionDateTime']);

                    $openTimeTransaction = $calculabletransactions->firstWhere('IsOpenTime', 1);

                    if ($openTimeTransaction)
                    {
                        $deviceTimeController->PauseOpenTimeIntervention($device->DeviceID, $latestDeviceThreadsNumber, true);
                    }
                    else
                    {
                        $deviceTimeController->PauseRatedTimeIntervention($device->DeviceID, true);
                    }
                   
                    //return response()->json(['success' => true, 'message' => 'Device has running time but appears to be offline. Pausing the time.']);   
                }
                else if ($device->DeviceStatusID == DeviceStatusEnum::STARTFREE_ID)
                {
                    $notification = Notifications::create([
                        'Notification' => 'Node ' . $device->DeviceName . ' was offline while free light was active. Ending free light to prevent any interruption.',
                        'NotificationLevelID' => NotificationLevelEnum::WARNING_ID,
                        'NotificationSourceID' => NotificationSourceEnum::DEVICE_ID,
                        'DeviceID' => $device->DeviceID
                    ]);

                    $notificationResponse = new NotificationResponse([
                        'NotificationID' => $notification->NotificationID,
                        'Notification' => $notification->Notification,
                        'NotificationLevelID' => $notification->NotificationLevelID,
                        'NotificationSourceID' => $notification->NotificationSourceID,
                        'DeviceID' => $device->DeviceID,
                        'CreatedDate' => $notification->created_at
                    ]);
    
                    event(new NotificationUpdates($notificationResponse));

                    $deviceTimeController->StopFreeLightInvervention($device->DeviceID, true);
                }
                
                $deviceHeartbeatStatusResponse = new DeviceHeartbeatStatusResponse([
                    'DeviceID' => $device->DeviceID,
                    'HeartbeatStatus' => DeviceHeartbeatStatusEnum::OFFLINE,
                    'DeviceStatus' => $device->DeviceStatusID
                ]);


                event(new DeviceHeartbeatUpdates($deviceHeartbeatStatusResponse));
            }

            return response()->json(['success' => true, 'message' => 'Device heartbeat status updated successfully.']);

        } catch (\Exception $e) {
            Log::error('Error updating devices offline status', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update device online status.'
            ], 500);
        }
    }


    public function UpdateHeartbeatDeviceStatusToOnline(Request $request)
    {
        $validatedData = $request->validate([
            'DeviceID' => 'required|integer',
            'SerialNumber' => 'required|string|max:255',
            'OnRestart' => 'required|boolean'
        ]);

        $device = Device::find($validatedData['DeviceID']);

        if (!$device) {
            return response()->json(['success' => false, 'message' => 'Device not found.'], 404);
        }

        $now = Carbon::now();

        try {
            Device::where('DeviceID', $validatedData['DeviceID'])
                ->update([
                    'IsOnline' => true, 
                    'last_heartbeat' => $now
                ]);

            if ($validatedData['OnRestart'] == true)
            {
                $deviceTimeController = new DeviceTimeController();

                if ($device->DeviceStatusID == DeviceStatusEnum::RUNNING_ID)
                {
                    $notification = Notifications::create([
                        'Notification' => 'Node ' . $device->DeviceName . ' was restarted while a timer was active. The timer has been paused to prevent any interruption.',
                        'NotificationLevelID' => NotificationLevelEnum::WARNING_ID,
                        'NotificationSourceID' => NotificationSourceEnum::DEVICE_ID,
                        'DeviceID' => $device->DeviceID
                    ]);

                    $notificationResponse = new NotificationResponse([
                        'NotificationID' => $notification->NotificationID,
                        'Notification' => $notification->Notification,
                        'NotificationLevelID' => $notification->NotificationLevelID,
                        'NotificationSourceID' => $notification->NotificationSourceID,
                        'DeviceID' => $device->DeviceID,
                        'CreatedDate' => $notification->created_at
                    ]);
    
                    event(new NotificationUpdates($notificationResponse));
    
                    $latestDeviceThreadsNumber = DeviceTimeTransactions::where('DeviceID', $validatedData['DeviceID'])
                    ->max('Thread');
    
                    $calculabletransactions = DeviceTimeTransactions::where('DeviceID', $validatedData['DeviceID'])
                            ->where('Thread', $latestDeviceThreadsNumber)
                            ->where('Active', true)
                            ->get(['Duration', 'Rate', 'IsOpenTime', 'TransactionType', 'TransactionDateTime']);
    
                    $openTimeTransaction = $calculabletransactions->firstWhere('IsOpenTime', 1);
    
                    
    
                    if ($openTimeTransaction)
                    {
                        $deviceTimeController->PauseOpenTimeIntervention($device->DeviceID, $latestDeviceThreadsNumber, false);
                    }
                    else
                    {
                        $deviceTimeController->PauseRatedTimeIntervention($device->DeviceID, false);
                    }
                }
                else if ($device->DeviceStatusID == DeviceStatusEnum::STARTFREE_ID)
                {
                    $notification = Notifications::create([
                        'Notification' => 'Node ' . $device->DeviceName . ' was restarted while free light was active. Ending free light to prevent any interruption.',
                        'NotificationLevelID' => NotificationLevelEnum::WARNING_ID,
                        'NotificationSourceID' => NotificationSourceEnum::DEVICE_ID,
                        'DeviceID' => $device->DeviceID
                    ]);

                    $notificationResponse = new NotificationResponse([
                        'NotificationID' => $notification->NotificationID,
                        'Notification' => $notification->Notification,
                        'NotificationLevelID' => $notification->NotificationLevelID,
                        'NotificationSourceID' => $notification->NotificationSourceID,
                        'DeviceID' => $device->DeviceID,
                        'CreatedDate' => $notification->created_at
                    ]);
    
                    event(new NotificationUpdates($notificationResponse));

                    $deviceTimeController->StopFreeLightInvervention($device->DeviceID, false);
                }
                else
                {
                    $notification = Notifications::create([
                        'Notification' => 'Node ' . $device->DeviceName . ' has been restarted.',
                        'NotificationLevelID' => NotificationLevelEnum::NORMAL_ID,
                        'NotificationSourceID' => NotificationSourceEnum::DEVICE_ID,
                        'DeviceID' => $device->DeviceID
                    ]);

                    $notificationResponse = new NotificationResponse([
                        'NotificationID' => $notification->NotificationID,
                        'Notification' => $notification->Notification,
                        'NotificationLevelID' => $notification->NotificationLevelID,
                        'NotificationSourceID' => $notification->NotificationSourceID,
                        'DeviceID' => $device->DeviceID,
                        'CreatedDate' => $notification->created_at
                    ]);
    
                    event(new NotificationUpdates($notificationResponse));
                }
                //return response()->json(['success' => true, 'message' => 'Device has running time but appears to be offline. Pausing the time.']);   
            }

            $deviceHeartbeatStatusResponse = new DeviceHeartbeatStatusResponse([
                'DeviceID' => $device->DeviceID,
                'HeartbeatStatus' => DeviceHeartbeatStatusEnum::ONLINE,
                'DeviceStatus' => $device->DeviceStatusID
            ]);

            event(new DeviceHeartbeatUpdates($deviceHeartbeatStatusResponse));

            return response()->json(['success' => true, 'message' => 'Device online status updated successfully.'], 200);
        } catch (\Exception $e) {
            Log::error('Error updating device online status', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to update device online status.'], 500);
        }
    }

    public function GetDeviceHeartbeatStatuses()
    {
        $deviceHeartbeatStatusResponses = [];
        try {
            $devices = Device::whereIn('DeviceStatusID', [
                DeviceStatusEnum::PENDING_ID,
                DeviceStatusEnum::RUNNING_ID,
                DeviceStatusEnum::INACTIVE_ID,
                DeviceStatusEnum::PAUSE_ID,
                DeviceStatusEnum::RESUME_ID,
                DeviceStatusEnum::STARTFREE_ID,
            ])->get();


            foreach ($devices as $device)
            {
                $heartbeatStatus = $device->IsOnline;

                $deviceHeartbeatStatusResponse = new DeviceHeartbeatStatusResponse([
                    'DeviceID' => $device->DeviceID,
                    'HeartbeatStatus' => $heartbeatStatus == 1 ? DeviceHeartbeatStatusEnum::ONLINE : DeviceHeartbeatStatusEnum::OFFLINE,
                    'DeviceStatus' => $device->DeviceStatusID
                ]);

                $deviceHeartbeatStatusResponses[] = $deviceHeartbeatStatusResponse;
            }

            foreach($deviceHeartbeatStatusResponses as $response) 
            {
                event(new DeviceHeartbeatUpdates($response));
            }
            
    
            return response()->json([
                'success' => true,
                'devices' => $deviceHeartbeatStatusResponses
            ]);
    
        } catch (\Exception $e) {
            Log::error('Error fetching running devices', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve running devices.'
            ], 500);
        }
    }

    public function GetDeviceHeartbeatStatus($id)
    {
        $now = Carbon::now();

        $device = Device::find($id);

        if (!$device) {
            return response()->json(['success' => false, 'message' => 'Device not found.'], 404);
        }

        $deviceIpAddress = $device->IPAddress;

        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('POST', "http://$deviceIpAddress/api/ondemand/heartbeat");

            if ($response->getStatusCode() == 200) 
            {
                Device::where('DeviceID', $id)
                ->update([
                    'IsOnline' => true, 
                    'last_heartbeat' => $now
                ]);

                $deviceHeartbeatStatusResponse = new DeviceHeartbeatStatusResponse([
                    'DeviceID' => $device->DeviceID,
                    'HeartbeatStatus' => DeviceHeartbeatStatusEnum::ONLINE,
                    'DeviceStatus' => $device->DeviceStatusID
                ]);

                event(new DeviceHeartbeatUpdates($deviceHeartbeatStatusResponse));
                return response()->json(['success' => true, 'message' => 'Device ' . $device->DeviceName . ' is online!']);
            }

            return response()->json(['success' => false, 'message' => 'Failed to test the device.'], $response->getStatusCode());
        } catch (\Exception $e) {
            Log::error('Error fetching devices', ['error' => $e->getMessage()]);
            
            $deviceTimeController = new DeviceTimeController();

            if ($device->DeviceStatusID == DeviceStatusEnum::RUNNING_ID)
            {
                $notification = Notifications::create([
                    'Notification' => 'Node ' . $device->DeviceName . ' was offline while a timer was active. The timer has been paused to prevent any interruption.',
                    'NotificationLevelID' => NotificationLevelEnum::WARNING_ID,
                    'NotificationSourceID' => NotificationSourceEnum::DEVICE_ID,
                    'DeviceID' => $device->DeviceID
                ]);

                $notificationResponse = new NotificationResponse([
                    'NotificationID' => $notification->NotificationID,
                    'Notification' => $notification->Notification,
                    'NotificationLevelID' => $notification->NotificationLevelID,
                    'NotificationSourceID' => $notification->NotificationSourceID,
                    'DeviceID' => $device->DeviceID,
                    'CreatedDate' => $notification->created_at
                ]);

                event(new NotificationUpdates($notificationResponse));

                $latestDeviceThreadsNumber = DeviceTimeTransactions::where('DeviceID', $device->DeviceID)
                    ->max('Thread');

                $calculabletransactions = DeviceTimeTransactions::where('DeviceID', $device->DeviceID)
                        ->where('Thread', $latestDeviceThreadsNumber)
                        ->where('Active', true)
                        ->get(['Duration', 'Rate', 'IsOpenTime', 'TransactionType', 'TransactionDateTime']);

                $openTimeTransaction = $calculabletransactions->firstWhere('IsOpenTime', 1);

                if ($openTimeTransaction)
                {
                    $deviceTimeController->PauseOpenTimeIntervention($device->DeviceID, $latestDeviceThreadsNumber, true);
                }
                else
                {
                    $deviceTimeController->PauseRatedTimeIntervention($device->DeviceID, true);
                }

                //return response()->json(['success' => true, 'message' => 'Device has running time but appears to be offline. Pausing the time.']);   
            }
            else if ($device->DeviceStatusID == DeviceStatusEnum::STARTFREE_ID)
            {
                $notification = Notifications::create([
                    'Notification' => 'Node ' . $device->DeviceName . ' was offline while free light was active. Ending free light to prevent any interruption.',
                    'NotificationLevelID' => NotificationLevelEnum::WARNING_ID,
                    'NotificationSourceID' => NotificationSourceEnum::DEVICE_ID,
                    'DeviceID' => $device->DeviceID
                ]);

                $notificationResponse = new NotificationResponse([
                    'NotificationID' => $notification->NotificationID,
                    'Notification' => $notification->Notification,
                    'NotificationLevelID' => $notification->NotificationLevelID,
                    'NotificationSourceID' => $notification->NotificationSourceID,
                    'DeviceID' => $device->DeviceID,
                    'CreatedDate' => $notification->created_at
                ]);

                event(new NotificationUpdates($notificationResponse));

                $deviceTimeController->StopFreeLightInvervention($device->DeviceID, true);
            }

            Device::where('DeviceID', $id)
                ->update([
                    'IsOnline' => false, 
                    'last_heartbeat' => Carbon::minValue()
                ]);

            $deviceHeartbeatStatusResponse = new DeviceHeartbeatStatusResponse([
                'DeviceID' => $device->DeviceID,
                'HeartbeatStatus' => DeviceHeartbeatStatusEnum::OFFLINE,
                'DeviceStatus' => $device->DeviceStatusID
            ]);

            event(new DeviceHeartbeatUpdates($deviceHeartbeatStatusResponse));
            // return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            return response()->json(['success' => false, 'message' => 'Device ' . $device->DeviceName . ' is offline!']);
        }
    }

    public function UpdateNodeExchange(Request $request)
    {
        $now = Carbon::now();

        $request->validate([
            'device_id' => 'required|integer|exists:Devices,DeviceID',
            'serialNumber' => 'required|string|max:255',
            'reason' => 'required|string|max:255'
        ]);

        $id = $request['device_id'];

        $device = Device::find($id);

        if (!$device) {
            return response()->json(['success' => false, 'message' => 'Device not found.'], 404);
        }

        try {

            $nodeToExchange = Device::where('SerialNumber', $request['serialNumber'])->first();

            if (!$nodeToExchange)
            {
                return response()->json(['success' => false, 'message' => 'Node with the given serial number does not exist.']);
            }

            $latestThreadsNumber = Exchange::max('Thread') + 1 ?? 1;

            Exchange::create([
                'DeviceID' => $id,
                'Thread' => $latestThreadsNumber,
                'Active' => true,
                'SerialNumber' => $device->SerialNumber,
                'ExchangeSerialNumber' => $request['serialNumber'],
                'Reason' => $request['reason'],
                'ExchangeStatusID' => ExchangeStatusEnum::PENDING_ID
            ]);

            $device->DeviceStatusID = DeviceStatusEnum::PENDINGEXCHANGE_ID;
            $device->save();

            LoggingController::InsertLog(LogEntityEnum::DEVICE, $device->DeviceID, 'Request to exchange node ' . $device->DeviceName . ' to node with serial number of ' . $request['serialNumber'] , LogTypeEnum::INFO, auth()->id());

            $deviceManagementController = new DeviceManagementController();

            DeviceDisplay::where('DeviceID', $id)->delete();
            DeviceTime::where('DeviceID', $id)->delete();
            Notifications::where('DeviceID', $id)->delete();
            TimeTransactionQueue::where('DeviceID', $id)->delete();

            $deviceManagementController->ExchangeDeviceTimeTransactions($id, $nodeToExchange->DeviceID);
            
            Exchange::create([
                'DeviceID' => $id,
                'Thread' => $latestThreadsNumber,
                'Active' => false,
                'SerialNumber' => $device->SerialNumber,
                'ExchangeSerialNumber' => $nodeToExchange->ExchangeSerialNumber,
                'ExchangeStatusID' => ExchangeStatusEnum::COMPLETED_ID
            ]);

            Exchange::where('DeviceID', $id)
                    ->where('Thread', $latestThreadsNumber)
                    ->where('Active', true)
                    ->update(['Active' => false]);

            LoggingController::InsertLog(LogEntityEnum::DEVICE, $device->DeviceID, 
            'Successfully exchanged ' . $device->DeviceName . ' from serial number ' . $device->SerialNumber . ' to ' . $nodeToExchange->ExchangeSerialNumber, 
            LogTypeEnum::INFO, 999999);

            $deviceManagementController->RequestDeleteDevice($id);

            return response()->json(['success' => true, 'message' => 'Device exchanged successfully!']);
        } catch (\Exception $e) {
            Log::error('Error updating device to pending exchange: ', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Error updating device to pending exchange.']);
        }
    }

    public function UpdateCancelNodeExchange($id)
    {
        $now = Carbon::now();

        $device = Device::find($id);

        if (!$device) {
            return response()->json(['success' => false, 'message' => 'Device not found.'], 404);
        }

        try {

            $latestDeviceThreadsNumber = Exchange::where('DeviceID', $id)
            ->max('Thread');

            $pendingExchangeStatus = Exchange::where('DeviceID', $id)
                                             ->where('Thread', $latestDeviceThreadsNumber)
                                             ->where('ExchangeStatusID', ExchangeStatusEnum::PENDING_ID)
                                             ->first();

            Exchange::create([
                'DeviceID' => $id,
                'Thread' => $latestDeviceThreadsNumber,
                'Active' => false,
                'SerialNumber' => $device->SerialNumber,
                'ExchangeSerialNumber' => $pendingExchangeStatus->ExchangeSerialNumber,
                'ExchangeStatusID' => ExchangeStatusEnum::CANCELLED_ID
            ]);

            $device->DeviceStatusID = DeviceStatusEnum::INACTIVE_ID;
            $device->save();

            Exchange::where('DeviceID', $id)
                        ->where('Thread', $latestDeviceThreadsNumber)
                        ->where('Active', true)
                        ->update(['Active' => false]);

            LoggingController::InsertLog(LogEntityEnum::DEVICE, $device->DeviceID, 'Cancelled the exchange for node ' . $device->DeviceName . ' to node with serial number of ' . $pendingExchangeStatus->ExchangeSerialNumber , LogTypeEnum::INFO, auth()->id());

            return response()->json(['success' => true, 'message' => 'Device is in pending exchange status.']);
        } catch (\Exception $e) {
            Log::error('Error updating device to pending exchange: ', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Error updating device to pending exchange.']);
        }
    }

    protected function handleErrorResponse($response, $deviceId)
    {
        $responseBody = json_decode($response->getBody(), true);

        if (isset($responseBody['response'])) {
            Log::error("Unexpected response with device at $deviceId", ['error' => $responseBody['response']]);
            return response()->json(['success' => false, 'message' => $responseBody['response']]);
        }

        return response()->json(['success' => false, 'message' => 'Unexpected response from device.']);
    }

    protected function handleGuzzleException(\GuzzleHttp\Exception\RequestException $e, $deviceId)
    {
        $errorResponse = $e->getResponse();

        if ($errorResponse) {
            $responseBody = json_decode($errorResponse->getBody(), true);
            Log::error("GuzzleException: Failed to communicate with device at $deviceId", ['error' => $e->getMessage()]);

            if (isset($responseBody['response'])) {
                return response()->json(['success' => false, 'message' => $responseBody['response']]);
            }
        }

        return response()->json(['error' => 'Failed to communicate with the device. Please check the device and try again.'], 500);
    }
}
