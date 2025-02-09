<?php

namespace App\Http\Controllers;

use App\Enums\DeviceHeartbeatStatusEnum;
use App\Models\Device;
use App\Models\DeviceTime;
use App\Models\DeviceTimeTransactions;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Enums\DeviceStatusEnum;
use App\Enums\LogEntityEnum;
use App\Enums\LogTypeEnum;
use App\Enums\NotificationLevelEnum;
use App\Enums\NotificationSourceEnum;
use App\Enums\QueueStatusEnum;
use App\Enums\StoppageTypeEnum;
use App\Enums\TimeTransactionTypeEnum;
use App\Events\DeviceHeartbeatUpdates;
use App\Events\DeviceTransactionUpdates;
use App\Events\NotificationUpdates;
use App\Models\DeviceDisplay;
use App\Models\DeviceHeartbeatStatusResponse;
use App\Models\DeviceTimeTransactionsResponse;
use App\Models\NotificationResponse;
use App\Models\Notifications;
use App\Models\RptDeviceTimeTransactions;
use App\Models\TimeTransactionQueue;
use Illuminate\Support\Facades\Log;

class DeviceTimeController extends Controller
{
    public function GetCurrentlyRunningDevices()
    {
        try 
        {    
            $runningDevices = DeviceTimeTransactions::where('Active', true)
                ->whereIn('TransactionType', [TimeTransactionTypeEnum::START, 
                                              TimeTransactionTypeEnum::EXTEND, 
                                              TimeTransactionTypeEnum::PAUSE, 
                                              TimeTransactionTypeEnum::RESUME,
                                              TimeTransactionTypeEnum::STARTFREE])
                ->with('device')
                ->get()
                ->groupBy('DeviceID');
    
            $deviceTimeTransactionResponses = [];
            $transactionType = TimeTransactionTypeEnum::START;
            $pauseTime = null;
    
            foreach ($runningDevices as $deviceId => $deviceTransactions) 
            {
                $totalUsedTimeBeforePause = 0;
                $deviceDisplay = DeviceDisplay::where('DeviceID', $deviceId)->first();
                $endTime = null;
                $transactionType = null;

                //Get the latest transaction type
                $transactionType = $deviceDisplay->TransactionType;
    
                $startTime = Carbon::parse($deviceDisplay->StartTime);
                $endTime = Carbon::parse($deviceDisplay->EndTime);
                $resumeTime = $deviceDisplay->ResumeTime ? Carbon::parse($deviceDisplay->ResumeTime) : null;
                $pauseTime = $deviceDisplay->PauseTime ? Carbon::parse($deviceDisplay->PauseTime) : null;
                $totalTime = $deviceTransactions->sum('Duration');
                $totalRate = $deviceTransactions->sum('Rate');

                $latestDeviceThreadsNumber = DeviceTimeTransactions::where('DeviceID', $deviceId)
                ->max('Thread');

                $latestPauseThreadsNumber = DeviceTimeTransactions::where('DeviceID', $deviceId)
                    ->where('Thread', $latestDeviceThreadsNumber)
                    ->max('PauseThread');

                if ($latestPauseThreadsNumber > 0)
                {
                    for ($pauseThread = 1; $pauseThread <= $latestPauseThreadsNumber; $pauseThread++)
                    {
                        // Fetch the pause transaction (latest transaction)
                        $lastPauseTime = DeviceTimeTransactions::where('DeviceID', $deviceId)
                            ->where('Thread', $latestDeviceThreadsNumber)
                            ->where('PauseThread', $pauseThread)
                            ->where('Active', true)
                            ->orderBy('TransactionID', 'desc')
                            ->first();

                        // Fetch the resume transaction (oldest transaction)
                        $lastResumeTime = DeviceTimeTransactions::where('DeviceID', $deviceId)
                            ->where('Thread', $latestDeviceThreadsNumber)
                            ->where('PauseThread', $pauseThread)
                            ->where('Active', true)
                            ->orderBy('TransactionID', 'asc')
                            ->first();

                        if ($lastPauseTime && $lastResumeTime) {

                            $latestPauseTime = $pauseTime;

                            $pauseDuration = Carbon::parse($lastResumeTime->TransactionDateTime)
                                            ->diffInSeconds(Carbon::parse($lastPauseTime->TransactionDateTime));

                            $totalUsedTimeBeforePause += $pauseDuration;
                        }
                    }
                }

                $deviceTimeTransactionResponse = new DeviceTimeTransactionsResponse([
                    'DeviceID' => $deviceId,
                    'TransactionType' => $transactionType, 
                    'IsOpenTime' => $deviceDisplay->IsOpenTime == 0 ? false : true,
                    'StartTime' => $startTime,
                    'PauseTime' => $pauseTime ? $pauseTime->toISOString() : null,
                    'ResumeTime' => $resumeTime ? $resumeTime->toISOString() : null,
                    'EndTime' => $endTime ? $endTime->toISOString() : null,
                    'TotalTime' => $totalTime / 60, 
                    'TotalRate' => $totalRate,
                    'TotalUsedTime' => $totalUsedTimeBeforePause 
                ]);
    
                $deviceTimeTransactionResponses[] = $deviceTimeTransactionResponse;
            }
    
            foreach ($deviceTimeTransactionResponses as $response) {
                event(new DeviceTransactionUpdates($response));
            }
    
            return response()->json([
                'success' => true,
                'devices' => $deviceTimeTransactionResponses
            ]);
    
        } catch (\Exception $e) {
            Log::error('Error fetching running devices', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve running devices.'
            ], 500);
        }
    }

    public function InsertDeviceIncrement(Request $request)
    {
        $request->validate([
            'time' => 'required|integer',
            'rate' => 'required|numeric',
            'device_id' => 'required|integer|exists:Devices,DeviceID',
        ]);

        try {
            $device = Device::findOrFail($request->device_id);

            DeviceTime::create([
                'DeviceID' => $request->device_id,
                'Time' => $request->time,
                'Rate' => $request->rate,
                'TimeTypeID' => DeviceTime::TIME_TYPE_INCREMENT,
                'Active' => true
            ]);

            LoggingController::InsertLog(
                LogEntityEnum::DEVICE_TIME,
                $request->device_id,
                'Added increment ' . $request->time . ' with rate ' . $request->rate .  ' for device: ' . $device->DeviceName,
                LogTypeEnum::INFO,
                auth()->id()
            );

            return redirect()->back()->with('success', 'Time increment added successfully.');
        } catch (\Exception $e) {
            Log::error('Error inserting device increment for DeviceID: ' . $request->device_id, ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to add time increment.', 'error' => $e->getMessage()], 500);
        }
    }
    
    public function InsertDeviceBase(Request $request)
    {
        $request->validate([
            'base_time' => 'required|integer',
            'base_rate' => 'required|numeric',
            'device_id' => 'required|integer|exists:Devices,DeviceID',
        ]);

        try {
            $device = Device::findOrFail($request->device_id);

            // Check if a base time already exists for the device
            $baseTime = DeviceTime::where('DeviceID', $request->device_id)
                ->where('TimeTypeID', DeviceTime::TIME_TYPE_BASE)
                ->first();

            if ($baseTime) {
                // Update the existing base time
                $baseTime->update([
                    'Time' => $request->base_time,
                    'Rate' => $request->base_rate,
                    'Active' => true
                ]);

                LoggingController::InsertLog(
                    LogEntityEnum::DEVICE_TIME,
                    $request->device_id,
                    'Updated base time ' . $baseTime->Time . ' to ' . $request->base_time . ' and base rate ' . $baseTime->Rate . ' to ' . $request->base_rate . ' for device: ' . $device->DeviceName,
                    LogTypeEnum::INFO,
                    auth()->id()
                );
            } else {
                // Create a new base time
                DeviceTime::create([
                    'DeviceID' => $request->device_id,
                    'Time' => $request->base_time,
                    'Rate' => $request->base_rate,
                    'TimeTypeID' => DeviceTime::TIME_TYPE_BASE,
                    'Active' => true
                ]);

                LoggingController::InsertLog(
                    LogEntityEnum::DEVICE_TIME,
                    $request->device_id,
                    'Added base time: ' . $request->base_time . ' and base rate: ' . $request->base_rate . ' for device: ' . $device->DeviceName,
                    LogTypeEnum::INFO,
                    auth()->id()
                );
            }

            return response()->json(['success' => true, 'message' => 'Base time and rate saved successfully.']);
        } catch (\Exception $e) {
            Log::error('Error inserting device base time for DeviceID: ' . $request->device_id, ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to insert base time and rate.', 'error' => $e->getMessage()], 500);
        }
    }

    public function InsertDeviceFreeTimeLimit(Request $request)
    {
        $request->validate([
            'free_light_time' => 'required|integer',
            'device_id' => 'required|integer|exists:Devices,DeviceID',
        ]);

        try {
            $device = Device::findOrFail($request->device_id);

            // Check if a base time already exists for the device
            $freeTime = DeviceTime::where('DeviceID', $request->device_id)
                ->where('TimeTypeID', DeviceTime::TIME_TYPE_FREE)
                ->first();

            if ($freeTime) {
                // Update the existing base time
                $freeTime->update([
                    'Time' => $request->free_light_time,
                    'Rate' => 0,
                    'Active' => true
                ]);

                LoggingController::InsertLog(
                    LogEntityEnum::DEVICE_TIME,
                    $request->device_id,
                    'Updated base time ' . $freeTime->Time . ' to ' . $freeTime->free_light_time . ' for device: ' . $device->DeviceName,
                    LogTypeEnum::INFO,
                    auth()->id()
                );
            } else {
                // Create a new base time
                DeviceTime::create([
                    'DeviceID' => $request->device_id,
                    'Time' => $request->free_light_time,
                    'Rate' => 0,
                    'TimeTypeID' => DeviceTime::TIME_TYPE_FREE,
                    'Active' => true
                ]);

                LoggingController::InsertLog(
                    LogEntityEnum::DEVICE_TIME,
                    $request->device_id,
                    'Added base time: ' . $request->free_light_time . ' for device: ' . $device->DeviceName,
                    LogTypeEnum::INFO,
                    auth()->id()
                );
            }

            return response()->json(['success' => true, 'message' => 'Free light time limit saved successfully.']);
        } catch (\Exception $e) {
            Log::error('Error inserting device free time limit for DeviceID: ' . $request->device_id, ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to insert free time limit.', 'error' => $e->getMessage()], 500);
        }
    }

    public function InsertDeviceOpen(Request $request)
    {
        $request->validate([
            'open_time' => 'required|integer',
            'open_rate' => 'required|numeric',
            'device_id' => 'required|integer|exists:Devices,DeviceID',
        ]);

        try {
            $device = Device::findOrFail($request->device_id);

            // Check if a base time already exists for the device
            $openTime = DeviceTime::where('DeviceID', $request->device_id)
                ->where('TimeTypeID', DeviceTime::TIME_TYPE_OPEN)
                ->first();

            if ($openTime) {
                // Update the existing base time
                $openTime->update([
                    'Time' => $request->open_time,
                    'Rate' => $request->open_rate,
                    'Active' => true
                ]);

                LoggingController::InsertLog(
                    LogEntityEnum::DEVICE_TIME,
                    $request->device_id,
                    'Updated open time ' . $openTime->Time . ' to ' . $request->open_time . ' and open time rate '
                        . $openTime->Rate . ' to ' . $request->open_rate . ' for device: ' . $device->DeviceName,
                    LogTypeEnum::INFO,
                    auth()->id()
                );
            } else {
                // Create a new base time
                DeviceTime::create([
                    'DeviceID' => $request->device_id,
                    'Time' => $request->open_time,
                    'Rate' => $request->open_rate,
                    'TimeTypeID' => DeviceTime::TIME_TYPE_OPEN,
                    'Active' => true
                ]);

                LoggingController::InsertLog(
                    LogEntityEnum::DEVICE_TIME,
                    $request->device_id,
                    'Added open time: ' . $request->open_time . ' and open time rate: ' . $request->open_rate . ' for device: ' . $device->DeviceName,
                    LogTypeEnum::INFO,
                    auth()->id()
                );
            }

            return response()->json(['success' => true, 'message' => 'Open time and rate saved successfully.']);
        } catch (\Exception $e) {
            Log::error('Error inserting device open time for DeviceID: ' . $request->device_id, ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to insert open time and rate.', 'error' => $e->getMessage()], 500);
        }
    }

    public function UpdateDeviceIncrement(Request $request, $id)
    {
        $request->validate([
            'time' => 'required|integer',
            'rate' => 'required|numeric',
            'device_id' => 'required|integer|exists:Devices,DeviceID',
        ]);

        try {
            $device = Device::findOrFail($request->device_id);
            $deviceTime = DeviceTime::findOrFail($id);

            LoggingController::InsertLog(
                LogEntityEnum::DEVICE_TIME,
                $id,
                'Updated increment time ' . $deviceTime->Time . ' to ' . $request->time . ' and base rate ' . $deviceTime->Rate . ' to ' . $request->rate . ' for device: ' . $device->DeviceName,
                LogTypeEnum::INFO,
                auth()->id()
            );

            $deviceTime->update([
                'Time' => $request->time,
                'Rate' => $request->rate,
            ]);

            return redirect()->back()->with('success', 'Time increment updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating device increment for DeviceID: ' . $request->device_id . ' and DeviceTimeID: ' . $id, ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to update time increment.', 'error' => $e->getMessage()], 500);
        }
    }

    public function UpdateDeviceIncrementStatus(Request $request, $id)
    {
        try {
            $device = Device::findOrFail($request->device_id);
            $deviceTime = DeviceTime::findOrFail($id);

            LoggingController::InsertLog(
                LogEntityEnum::DEVICE_TIME,
                $id,
                'Disabled increment with time ' . $deviceTime->Time . ' and base rate ' . $deviceTime->Rate . ' for device: ' . $device->DeviceName,
                LogTypeEnum::INFO,
                auth()->id()
            );

            $deviceTime->update([
                'Active' => $request->incrementStatus,
            ]);

            return response()->json(['success' => true, 'message' => 'Time increment status updated successfully.']);
        } catch (\Exception $e) {
            Log::error('Error updating device increment status for DeviceID: ' . $request->device_id . ' and DeviceTimeID: ' . $id, ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to update increment status.', 'error' => $e->getMessage()], 500);
        }
    }

    public function DeleteDeviceIncrement($id)
    {
        try {
            $deviceTime = DeviceTime::findOrFail($id);
            $device = Device::findOrFail($deviceTime->DeviceID);

            LoggingController::InsertLog(
                LogEntityEnum::DEVICE_TIME,
                $id,
                'Deleted increment time ' . $deviceTime->Time . ' with rate ' . $deviceTime->Rate . ' for device: ' . $device->DeviceName,
                LogTypeEnum::INFO,
                auth()->id()
            );

            $deviceTime->delete();

            return response()->json(['success' => 'Time increment deleted successfully.']);
        } catch (\Exception $e) {
            Log::error('Error deleting device increment for DeviceTimeID: ' . $id, ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to delete time increment.', 'error' => $e->getMessage()], 500);
        }
    }

    public function StartDeviceRatedTime($id)
    {
        $device = Device::findOrFail($id);
        $officialStartTime = Carbon::now();
        $transactionType = TimeTransactionTypeEnum::START;
        $deviceIpAddress = $device->IPAddress;

        // Fetch the base time
        $baseTime = DeviceTime::where('DeviceID', $id)
            ->where('TimeTypeID', DeviceTime::TIME_TYPE_BASE)
            ->first();

        if (!$baseTime) {
            return response()->json(['error' => 'Base time not configured for this device.'], 400);
        }

        $latestThreadsNumber = DeviceTimeTransactions::max('Thread') + 1 ?? 1;

        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('POST', "http://$deviceIpAddress/api/span", [
                'json' => [
                    'device_id' => $id,
                    'time' => $baseTime->Time * 60,
                    'thread' => $latestThreadsNumber,
                    'startdatetime' => $officialStartTime->format('Y-m-d H:i:s'),
                ]
            ]);

            if ($response->getStatusCode() == 200) {

                $transaction = DeviceTimeTransactions::create([
                    'DeviceID' => $device->DeviceID,
                    'TransactionType' => $transactionType,
                    'Thread' => $latestThreadsNumber,
                    'IsOpenTime' => false,
                    'TransactionDateTime' => $officialStartTime,
                    'Duration' => $baseTime->Time * 60,
                    'Rate' => $baseTime->Rate,
                    'Active' => true,
                    'CreatedByUserId' => auth()->id(),
                ]);

                $device->DeviceStatusID = DeviceStatusEnum::RUNNING_ID;
                $device->save();
            
                $startTime = Carbon::parse($transaction->StartTime);
                $endTime = $startTime->clone()->addMinutes($baseTime->Time);
                $totalTime = $baseTime->Time;
                $totalRate = $baseTime->Rate;

                $timeTransactionQueue = TimeTransactionQueue::create([
                    'DeviceID' => $id,
                    'DeviceStatusID' => DeviceStatusEnum::RUNNING_ID,
                    'Thread' => $latestThreadsNumber,
                    'EndTime' => $endTime,
                    'QueueStatusID' => QueueStatusEnum::ACTIVE_ID,
                    'ErrorMessage' => null
                ]);

                $deviceDisplay = DeviceDisplay::updateOrCreate(
                    // Check DeviceID existence
                    ['DeviceID' => $id],
                    // Values to update or create
                    [
                        'TransactionType' => $transactionType,
                        'IsOpenTime' => false,
                        'StartTime' => $startTime,
                        'EndTime' => $endTime,
                        'TotalTime' => $totalTime,
                        'TotalRate' => $totalRate
                    ]
                );

                $deviceTimeTransactionResponse = new DeviceTimeTransactionsResponse([
                    'DeviceID' => $id,
                    'TransactionType' => $transactionType,
                    'IsOpenTime' => false,
                    'StartTime' => $startTime,
                    'PauseTime' => null,
                    'ResumeTime' => null,
                    'EndTime' => $endTime,
                    'TotalTime' => $totalTime,
                    'TotalRate' => $totalRate
                ]);
            
                event(new DeviceTransactionUpdates($deviceTimeTransactionResponse)); 

                return response()->json([
                    'success' => 'Device time started successfully.',
                    'startTime' => $startTime->format('Y-m-d H:i:s'),
                    'endTime' => $endTime->format('Y-m-d H:i:s'),
                    'totalTime' => $totalTime,
                    'totalRate' => $totalRate
                ]);
            }

            return $this->handleErrorResponse($response, $id);

        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return $this->handleGuzzleException($e, $id);
        } catch (\Exception $e) {
            // Log unexpected errors
            Log::error('Unexpected error starting rated time for device ' . $id, ['error' => $e->getMessage()]);
            
            $deviceTimeTransactionResponse = new DeviceTimeTransactionsResponse([
                'DeviceID' => $id,
                'TransactionType' => $transactionType,
                'IsOpenTime' => false,
                'StartTime' => null,
                'PauseTime' => null,
                'ResumeTime' => null,
                'EndTime' => null,
                'TotalTime' => null,
                'TotalRate' => null,
                'DoHeartbeatCheck' => true
            ]);

            event(new DeviceTransactionUpdates($deviceTimeTransactionResponse)); 
            
            // return response()->json(['error' => 'An unexpected error occurred. Please try again later.'], 500);
            return response()->json(['success' => false, 'message' => 'Device ' . $device->DeviceName . ' is unresponsive. Checking status...']);
        }
    }

    public function StartDeviceOpenTime($id)
    {
        $device = Device::findOrFail($id);
        $officialStartTime = Carbon::now();
        $transactionType = TimeTransactionTypeEnum::START;
        $deviceIpAddress = $device->IPAddress;

            // Fetch the base time
        $baseTime = DeviceTime::where('DeviceID', $id)
            ->where('TimeTypeID', DeviceTime::TIME_TYPE_OPEN
            )->first();

        if (!$baseTime) {
            return response()->json('Open time not configured for this device.', 400);
        }

        $latestThreadsNumber = DeviceTimeTransactions::max('Thread') + 1 ?? 1;

        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('POST', "http://$deviceIpAddress/api/opentime/start", [
                'json' => [
                    'time' => null,
                    'thread' => $latestThreadsNumber,
                    'startdatetime' => $officialStartTime->format('Y-m-d H:i:s'),
                ]
            ]);

            if ($response->getStatusCode() == 200) {
                $responseBody = json_decode($response->getBody(), true);
                $transaction = DeviceTimeTransactions::create([
                    'DeviceID' => $device->DeviceID,
                    'TransactionType' => $transactionType,
                    'Thread' => $latestThreadsNumber,
                    'IsOpenTime' => true,
                    'TransactionDateTime' => $officialStartTime,
                    'Duration' => 0,
                    'Rate' => $baseTime->Rate,
                    'Active' => true,
                    'CreatedByUserId' => auth()->id()
                ]);

                $device->DeviceStatusID = DeviceStatusEnum::RUNNING_ID;
                $device->save();

                $startTime = Carbon::parse($transaction->StartTime);
                $endTime = null;
                $totalTime = null;
                $totalRate = $baseTime->Rate;

                $deviceDisplay = DeviceDisplay::updateOrCreate(
                    // Check DeviceID existence
                    ['DeviceID' => $id],
                    // Values to update or create
                    [
                        'TransactionType' => $transactionType,
                        'IsOpenTime' => true,
                        'StartTime' => $startTime,
                        'EndTime' => $endTime,
                        'TotalTime' => 0,
                        'TotalRate' => 0
                    ]
                );

                $deviceTimeTransactionResponse = new DeviceTimeTransactionsResponse([
                    'DeviceID' => $id,
                    'TransactionType' => $transactionType,
                    'IsOpenTime' => true,
                    'StartTime' => $startTime,
                    'PauseTime' => null,
                    'ResumeTime' => null,
                    'EndTime' => $endTime,
                    'TotalTime' => $totalTime,
                    'TotalRate' => $totalRate
                ]);

                event(new DeviceTransactionUpdates($deviceTimeTransactionResponse)); 

                return response()->json([
                    'success' => 'Device time started successfully.',
                    'startTime' => $startTime->format('Y-m-d H:i:s'),
                    'endTime' => null,
                    'totalTime' => $totalTime,
                    'totalRate' => $totalRate
                ]);
            }

            return $this->handleErrorResponse($response, $id);

        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return $this->handleGuzzleException($e, $id);
        } catch (\Exception $e) {
            // Log unexpected errors
            Log::error('Unexpected error starting open time for device ' . $id, ['error' => $e->getMessage()]);
            return response()->json(['error' => 'An unexpected error occurred. Please try again later.'], 500);
        }
    }

    public function ExtendDeviceTime(Request $request, $id)
    {
        $device = Device::findOrFail($id);
        $increment = $request->input('increment');
        $rate = $request->input('rate');
        $officialExtendTime = Carbon::now();
        $deviceIpAddress = $device->IPAddress;
        $transactionType = TimeTransactionTypeEnum::EXTEND;
        $resumeTransactions = null;
        $totalUsedTimeBeforePause = 0;

        try {

            $client = new \GuzzleHttp\Client();
            $response = $client->request('POST', "http://$deviceIpAddress/api/span", [
                'json' => [
                    'device_id' => $id,
                    'time' => $increment * 60,
                    'thread' => 0,
                    'startdatetime' => null
                ],
                //'timeout' => 5, // Optional: Set a timeout in seconds
            ]);

            if ($response->getStatusCode() == 200) {
                $responseData = json_decode($response->getBody()->getContents(), true);

                //Extract Thread number
                $thread = $responseData['thread'] ?? 0;
                $thread = (int) $thread;

                $latestPauseThreadsNumber = DeviceTimeTransactions::where('DeviceID', $id)
                ->where('Thread', $thread)
                ->max('PauseThread') ?? 0;

                if ($latestPauseThreadsNumber == 0)
                {
                    $resumeTransactions = DeviceTimeTransactions::where('DeviceID', $id)
                                ->where('TransactionType', TimeTransactionTypeEnum::START)
                                ->where('Thread', $thread)
                                ->where('Active', true)
                                ->first();
                }
                else
                {
                    $resumeTransactions = DeviceTimeTransactions::where('DeviceID', $id)
                                ->where('TransactionType', TimeTransactionTypeEnum::RESUME)
                                ->where('Thread', $thread)
                                ->where('Active', true)
                                ->where('PauseThread', 0)
                                ->first();
                }

                // Extend transaction
                $transaction = DeviceTimeTransactions::create([
                    'DeviceID' => $device->DeviceID,
                    'TransactionType' => $transactionType,
                    'Thread' => $thread,
                    'IsOpenTime' => false,
                    'TransactionDateTime' => $officialExtendTime,
                    'Duration' => $increment * 60,
                    'Rate' => $rate,
                    'Active' => true,
                    'CreatedByUserId' => auth()->id(),
                ]);

                $calculabletransactions = DeviceTimeTransactions::where('DeviceID', $id)
                        ->where('Thread', $thread)
                        ->where('Active', true)
                        ->get(['Duration', 'Rate', 'IsOpenTime', 'TransactionType', 'TransactionDateTime']);
                
                if ($latestPauseThreadsNumber > 0)
                {
                    for ($pauseThread = 1; $pauseThread <= $latestPauseThreadsNumber; $pauseThread++)
                    {
                        // Fetch the pause transaction (latest transaction)
                        $pauseTime = DeviceTimeTransactions::where('DeviceID', $id)
                                        ->where('Thread', $thread)
                                        ->where('PauseThread', $pauseThread)
                                        ->where('Active', true)
                                        ->orderBy('TransactionID', 'desc')
                                        ->first();
                
                        // Fetch the resume transaction (oldest transaction)
                        $resumeTime = DeviceTimeTransactions::where('DeviceID', $id)
                                        ->where('Thread', $thread)
                                        ->where('PauseThread', $pauseThread)
                                        ->where('Active', true)
                                        ->orderBy('TransactionID', 'asc')
                                        ->first();
                
                        if ($pauseTime && $resumeTime) 
                        {
                            $pauseDuration = Carbon::parse($resumeTime->TransactionDateTime)
                                                ->diffInSeconds(Carbon::parse($pauseTime->TransactionDateTime));
                
                            $totalUsedTimeBeforePause += $pauseDuration;
                        }
                    }
                }     

                $startTime = Carbon::parse($resumeTransactions->TransactionDateTime);
                $totalTime = $calculabletransactions->sum('Duration');
                $totalRate = $calculabletransactions->sum('Rate');

                //Compute the remaining time
                $remainingTime = $totalTime - $totalUsedTimeBeforePause;
                
                $endTime = $startTime ? Carbon::parse($startTime)->addSeconds($remainingTime) : null;

                $deviceDisplay = DeviceDisplay::updateOrCreate(
                    // Check DeviceID existence
                    ['DeviceID' => $id],
                    // Values to update or create
                    [
                        'TransactionType' => TimeTransactionTypeEnum::START,
                        'IsOpenTime' => false,
                        'StartTime' => $startTime,
                        'EndTime' => $endTime,
                        'TotalTime' => $totalTime / 60,
                        'TotalRate' => $totalRate
                    ]
                );

                $timeTransactionQueue = TimeTransactionQueue::where('DeviceID', $id)
                        ->where('Thread', $thread)
                        ->where('QueueStatusID', QueueStatusEnum::ACTIVE_ID)
                        ->first();

                if ($timeTransactionQueue) {
                    $timeTransactionQueue->EndTime = $endTime;
                    $timeTransactionQueue->save(); 
                }

                $deviceTimeTransactionResponse = new DeviceTimeTransactionsResponse([
                    'DeviceID' => $id,
                    'TransactionType' => $transactionType,
                    'IsOpenTime' => false,
                    'StartTime' => $startTime,
                    'PauseTime' => null,
                    'ResumeTime' => null,
                    'EndTime' => $endTime,
                    'TotalTime' => $totalTime / 60,
                    'TotalRate' => $totalRate
                ]);

                event(new DeviceTransactionUpdates($deviceTimeTransactionResponse)); 

                return response()->json([
                    'success' => 'Device time started successfully.',
                    'startTime' => $startTime,
                    'endTime' => $endTime,
                    'totalTime' => $totalTime,
                    'totalRate' => $totalRate
                ]);
            }
            return $this->handleErrorResponse($response, $id);

        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return $this->handleGuzzleException($e, $id);
        } catch (\Exception $e) {
            // Log unexpected errors
            Log::error('Unexpected error extending time for device ' . $id, ['error' => $e->getMessage()]);
            
            $deviceTimeTransactionResponse = new DeviceTimeTransactionsResponse([
                'DeviceID' => $id,
                'TransactionType' => $transactionType,
                'IsOpenTime' => false,
                'StartTime' => null,
                'PauseTime' => null,
                'ResumeTime' => null,
                'EndTime' => null,
                'TotalTime' => null,
                'TotalRate' => null,
                'DoHeartbeatCheck' => true
            ]);

            event(new DeviceTransactionUpdates($deviceTimeTransactionResponse)); 
            
            // return response()->json(['error' => 'An unexpected error occurred. Please try again later.'], 500);
            return response()->json(['success' => false, 'message' => 'Device ' . $device->DeviceName . ' is unresponsive. Checking status...']);
        }
    }

    public function EndDeviceTimeManual($id)
    {
        $device = Device::findOrFail($id);
        $officialEndTime = Carbon::now();

        $deviceIpAddress = $device->IPAddress;

        $transactionType = TimeTransactionTypeEnum::END;
        $startTime = null;
        $totalDuration = null;
        $totalRate= null;
        $totalUsedTimeBeforePause = 0;
        $latestDeviceThreadsNumber = DeviceTimeTransactions::where('DeviceID', $id)
            ->max('Thread');

        try 
        {
            $calculabletransactions = DeviceTimeTransactions::where('DeviceID', $id)
                ->where('Thread', $latestDeviceThreadsNumber)
                ->where('Active', true)
                ->get(['Duration', 'Rate', 'IsOpenTime', 'TransactionType', 'TransactionDateTime']);

            $openTimeTransaction = $calculabletransactions->firstWhere('IsOpenTime', 1);
            $startTime = Carbon::parse($calculabletransactions->min('TransactionDateTime'));
                
            if ($openTimeTransaction)
            {
                $latestPauseThreadsNumber = DeviceTimeTransactions::where('DeviceID', $id)
                    ->where('Thread', $latestDeviceThreadsNumber)
                    ->max('PauseThread');

                if ($latestPauseThreadsNumber > 0)
                {
                    for ($pauseThread = 1; $pauseThread <= $latestPauseThreadsNumber; $pauseThread++)
                    {
                        // Fetch the pause transaction (latest transaction)
                        $pauseTime = DeviceTimeTransactions::where('DeviceID', $id)
                            ->where('Thread', $latestDeviceThreadsNumber)
                            ->where('PauseThread', $pauseThread)
                            ->where('Active', true)
                            ->orderBy('TransactionID', 'desc')
                            ->first();
    
                        // Fetch the resume transaction (oldest transaction)
                        $resumeTime = DeviceTimeTransactions::where('DeviceID', $id)
                            ->where('Thread', $latestDeviceThreadsNumber)
                            ->where('PauseThread', $pauseThread)
                            ->where('Active', true)
                            ->orderBy('TransactionID', 'asc')
                            ->first();
    
                        if ($pauseTime && $resumeTime) 
                        {
                            $pauseDuration = Carbon::parse($resumeTime->TransactionDateTime)
                                            ->diffInSeconds(Carbon::parse($pauseTime->TransactionDateTime));
    
                            $totalUsedTimeBeforePause += $pauseDuration;
                        }
                    }

                    $latestResumeTime = DeviceTimeTransactions::where('DeviceID', $id)
                                    ->where('TransactionType', TimeTransactionTypeEnum::RESUME)
                                    ->where('Thread', $latestDeviceThreadsNumber)
                                    ->where('PauseThread', 0)
                                    ->where('Active', true)
                                    ->orderBy('TransactionID', 'asc')
                                    ->first();
                    
                    if ($latestResumeTime)
                    {
                        $endConsumptionDuration = Carbon::parse($officialEndTime)
                                  ->diffInSeconds(Carbon::parse($latestResumeTime->TransactionDateTime));

                        $totalUsedTimeBeforePause += $endConsumptionDuration;
                    }
                    


                    $openTimeInfo = DeviceTime::where('DeviceID', $id)
                        ->where('TimeTypeID', DeviceTime::TIME_TYPE_OPEN)->first();
                    if (($totalUsedTimeBeforePause / 60) < $openTimeInfo->Time) 
                    {
                        $totalRate = $openTimeInfo->Rate;
                    } 
                    else 
                    {
                        $totalRate = (($totalUsedTimeBeforePause / 60) / $openTimeInfo->Time) * $openTimeInfo->Rate;
                        $totalRate = number_format((float)$totalRate, 2, '.', '');
                    }
    
                    //Get the START transaction of the Thread
                    $startTransactions = DeviceTimeTransactions::where('DeviceID', $id)
                        ->where('TransactionType', TimeTransactionTypeEnum::START)
                        ->where('Thread', $latestDeviceThreadsNumber)
                        ->where('Active', true)
                        ->first();
                        
                    //Update the Duration and Rate of the START transaction
                    $startTransactions->update([
                        'Duration' => $totalUsedTimeBeforePause,
                        'Rate' => $totalRate
                    ]);
                }
                else
                {
                    $totalDuration = $officialEndTime->diffInSeconds($startTime, true);
                    $openTimeInfo = DeviceTime::where('DeviceID', $id)
                        ->where('TimeTypeID', DeviceTime::TIME_TYPE_OPEN)->first();
                    if (($totalDuration / 60) < $openTimeInfo->Time) 
                    {
                        $totalRate = $openTimeInfo->Rate;
                    } else 
                    {
                        $totalRate = (($totalDuration / 60) / $openTimeInfo->Time) * $openTimeInfo->Rate;
                        $totalRate = number_format((float)$totalRate, 2, '.', '');
                    }
    
                    //Get the START transaction of the Thread
                    $startTransactions = DeviceTimeTransactions::where('DeviceID', $id)
                        ->where('TransactionType', TimeTransactionTypeEnum::START)
                        ->where('Thread', $latestDeviceThreadsNumber)
                        ->where('Active', true)
                        ->first();
                        
                    //Update the Duration and Rate of the START transaction
                    $startTransactions->update([
                        'Duration' => $totalDuration,
                        'Rate' => $totalRate
                    ]);
                }
            }
            else 
            {
                $totalDuration = $calculabletransactions->sum('Duration');
                $totalRate = $calculabletransactions->sum('Rate');
            }
              
            $device->DeviceStatusID = DeviceStatusEnum::INACTIVE_ID;
            $device->save();

            $deviceDisplay = DeviceDisplay::updateOrCreate(
                // Check DeviceID existence
                ['DeviceID' => $id],
                // Values to update or create
                [
                    'TransactionType' => $transactionType,
                    'IsOpenTime' => false,
                    'StartTime' => $startTime,
                    'EndTime' => $officialEndTime,
                    'PauseTime' => null,
                    'TotalTime' => 0,
                    'TotalRate' => 0
                ]
            );

            $client = new \GuzzleHttp\Client();
            $response = $client->request('POST', "http://$deviceIpAddress/api/stop");

            if ($response->getStatusCode() == 200) 
            {
                $responseData = json_decode($response->getBody()->getContents(), true);

                $transaction = DeviceTimeTransactions::create([
                    'DeviceID' => $device->DeviceID,
                    'TransactionType' => $transactionType,
                    'Thread' => $latestDeviceThreadsNumber,
                    'IsOpenTime' => true,
                    'TransactionDateTime' => $officialEndTime,
                    'Duration' => 0,
                    'Rate' => 0,
                    'Active' => false,
                    'CreatedByUserId' => auth()->id(),
                ]);

                $deviceTimeTransactionResponse = new DeviceTimeTransactionsResponse([
                    'DeviceID' => $id,
                    'TransactionType' => $transactionType,
                    'IsOpenTime' => false,
                    'StartTime' => $startTime,
                    'PauseTime' => null,
                    'ResumeTime' => null,
                    'EndTime' => $officialEndTime,
                    'TotalTime' => $totalDuration,
                    'TotalRate' => $totalRate
                ]);

                event(new DeviceTransactionUpdates($deviceTimeTransactionResponse)); 

                $deviceTimeController = new DeviceTimeController();
                $deviceTimeController->UpdateEndTime($id, $latestDeviceThreadsNumber, $deviceTimeTransactionResponse, StoppageTypeEnum::MANUAL, null);

                return response()->json([
                    'success' => 'Device time started successfully.',
                    'startTime' => $startTime->format('Y-m-d H:i:s'),
                    'endTime' => $officialEndTime->format('Y-m-d H:i:s'),
                    'totalTime' => $totalDuration,
                    'totalRate' => $totalRate
                ]);
            }

            return $this->handleErrorResponse($response, $id);

        } catch (\GuzzleHttp\Exception\RequestException $e) {
             return $this->handleGuzzleException($e, $id);
        } catch (\Exception $e) {
            // Log unexpected errors
            Log::error('Unexpected error manually ending time for device ' . $id, ['error' => $e->getTraceAsString()]);
            
            $transaction = DeviceTimeTransactions::create([
                'DeviceID' => $device->DeviceID,
                'TransactionType' => $transactionType,
                'Thread' => $latestDeviceThreadsNumber,
                'IsOpenTime' => true,
                'TransactionDateTime' => $officialEndTime,
                'Duration' => 0,
                'Rate' => 0,
                'Active' => false,
                'Reason' => 'Node was unreachable while attempting to end the timer. The timer has been ended with an error status.',
                'CreatedByUserId' => auth()->id(),
            ]);

            $notification = Notifications::create([
                'Notification' => 'Node ' . $device->DeviceName . ' was unreachable while attempting to end the timer. The timer has been ended with an error status.',
                'NotificationLevelID' => NotificationLevelEnum::ERROR_ID,
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

            $deviceTimeTransactionResponse = new DeviceTimeTransactionsResponse([
                'DeviceID' => $id,
                'TransactionType' => $transactionType,
                'IsOpenTime' => false,
                'StartTime' => $startTime,
                'PauseTime' => null,
                'ResumeTime' => null,
                'EndTime' => $officialEndTime,
                'TotalTime' => $totalDuration,
                'TotalRate' => $totalRate,
                'DoHeartbeatCheck' => true
            ]);

            event(new DeviceTransactionUpdates($deviceTimeTransactionResponse)); 

            $deviceTimeController = new DeviceTimeController();
            $deviceTimeController->UpdateEndTime($id, $latestDeviceThreadsNumber, $deviceTimeTransactionResponse, StoppageTypeEnum::MANUAL, $e->getMessage());

            // return response()->json(['error' => 'An unexpected error occurred. Please try again later.'], 500);
            return response()->json(['success' => false, 'message' => 'Device ' . $device->DeviceName . ' is unresponsive. Checking status...']);
        }
    }

    public function EndDeviceTimeAuto($id)
    {
        $device = Device::findOrFail($id);
        $officialEndTime = Carbon::now();
        $response = null;
        $deviceIpAddress = $device->IPAddress;

        $transactionType = TimeTransactionTypeEnum::END;
        $startTime = null;
        $totalDuration = null;
        $totalRate= null;
        $latestDeviceThreadsNumber = DeviceTimeTransactions::where('DeviceID', $id)
            ->max('Thread');

        try 
        {
            $calculabletransactions = DeviceTimeTransactions::where('DeviceID', $id)
                        ->where('Thread', $latestDeviceThreadsNumber)
                        ->where('Active', true)
                        ->get(['Duration', 'Rate', 'IsOpenTime', 'TransactionDateTime']);
    
            $startTime = Carbon::parse($calculabletransactions->min('TransactionDateTime'));
            $totalDuration = $calculabletransactions->sum('Duration');
            $totalRate = $calculabletransactions->sum('Rate');
            
            $device->DeviceStatusID = DeviceStatusEnum::INACTIVE_ID;
            $device->save();

            $deviceDisplay = DeviceDisplay::updateOrCreate(
                // Check DeviceID existence
                ['DeviceID' => $id],
                // Values to update or create
                [
                    'TransactionType' => $transactionType,
                    'IsOpenTime' => false,
                    'StartTime' => $startTime,
                    'EndTime' => $officialEndTime,
                    'PauseTime' => null,
                    'TotalTime' => 0,
                    'TotalRate' => 0
                ]
            );

            $client = new \GuzzleHttp\Client();
            $response = $client->request('POST', "http://$deviceIpAddress/api/stop");

            if ($response->getStatusCode() == 200) 
            {
                $transaction = DeviceTimeTransactions::create([
                    'DeviceID' => $device->DeviceID,
                    'TransactionType' => $transactionType,
                    'Thread' => $latestDeviceThreadsNumber,
                    'IsOpenTime' => false,
                    'TransactionDateTime' => $officialEndTime,
                    'Duration' => 0,
                    'Rate' => 0,
                    'Active' => false,
                    'CreatedByUserId' => auth()->id(),
                ]);

                $notification = Notifications::create([
                    'Notification' => 'Node ' . $device->DeviceName . ' was ended successfully.',
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

                $deviceTimeTransactionResponse = new DeviceTimeTransactionsResponse([
                    'DeviceID' => $id,
                    'TransactionType' => $transactionType,
                    'IsOpenTime' => false,
                    'StartTime' => $startTime,
                    'PauseTime' => null,
                    'ResumeTime' => null,
                    'EndTime' => $officialEndTime,
                    'TotalTime' => $totalDuration,
                    'TotalRate' => $totalRate
                ]);

                event(new DeviceTransactionUpdates($deviceTimeTransactionResponse)); 
    
                $deviceTimeController = new DeviceTimeController();
                $deviceTimeController->UpdateEndTime($id, $latestDeviceThreadsNumber, $deviceTimeTransactionResponse, StoppageTypeEnum::AUTO, null);

                return response()->json([
                    'success' => 'Device time ended successfully.',
                    'startTime' => $startTime->format('Y-m-d H:i:s'),
                    'endTime' => $officialEndTime->format('Y-m-d H:i:s'),
                    'totalTime' => $totalDuration,
                    'totalRate' => $totalRate
                ]);
            }

            return $this->handleErrorResponse($response, $id);

        } catch (\GuzzleHttp\Exception\RequestException $e) {
             return $this->handleGuzzleException($e, $id);
        } catch (\Exception $e) {
            // Log unexpected errors
            Log::error('Unexpected error automatically ending time for device ' . $id, ['error' => $e->getMessage()]);

            $transaction = DeviceTimeTransactions::create([
                'DeviceID' => $device->DeviceID,
                'TransactionType' => $transactionType,
                'Thread' => $latestDeviceThreadsNumber,
                'IsOpenTime' => false,
                'TransactionDateTime' => $officialEndTime,
                'Duration' => 0,
                'Rate' => 0,
                'Active' => false,
                'Reason' => 'Node was unreachable while attempting to end the timer. The timer has been ended with an error status.',
                'CreatedByUserId' => auth()->id(),
            ]);

            $notification = Notifications::create([
                'Notification' => 'Node ' . $device->DeviceName . ' was unreachable while attempting to end the timer. The timer has been ended with an error status.',
                'NotificationLevelID' => NotificationLevelEnum::ERROR_ID,
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

            $deviceTimeTransactionResponse = new DeviceTimeTransactionsResponse([
                'DeviceID' => $id,
                'TransactionType' => $transactionType,
                'IsOpenTime' => false,
                'StartTime' => $startTime,
                'PauseTime' => null,
                'ResumeTime' => null,
                'EndTime' => $officialEndTime,
                'TotalTime' => $totalDuration,
                'TotalRate' => $totalRate,
                'DoHeartbeatCheck' => true
            ]);

            event(new DeviceTransactionUpdates($deviceTimeTransactionResponse)); 

            $deviceTimeController = new DeviceTimeController();
            $deviceTimeController->UpdateEndTime($id, $latestDeviceThreadsNumber, $deviceTimeTransactionResponse, StoppageTypeEnum::AUTO, $e->getMessage());


            return response()->json(['error' => 'An unexpected error occurred. Please try again later.'], 500);
        }
    }

    public function UpdateEndTime($deviceId, $latestDeviceThreadsNumber, $deviceTimeTransactionResponse, $stoppageType, $errorMessage, $freeLight = false)
    {
        $timeTransactionQueue = TimeTransactionQueue::where('DeviceID', $deviceId)
            ->where('Thread', $latestDeviceThreadsNumber)
            ->whereIn('QueueStatusID', [QueueStatusEnum::ACTIVE_ID, QueueStatusEnum::PENDING_ID])
            ->first();

        if ($errorMessage != null)
        {
            $timeTransactionQueueErrorMessage = null;

            if ($freeLight)
            {
                $timeTransactionQueueErrorMessage = 'Ending time with an error: ' . $errorMessage;
            }
            else
            {
                $timeTransactionQueueErrorMessage = 'Ending free time with an error: ' . $errorMessage;
            }

            if ($timeTransactionQueue) {
                $timeTransactionQueue->QueueStatusID = QueueStatusEnum::ERROR_ID;
                $timeTransactionQueue->StoppageType = $stoppageType;
                $timeTransactionQueue->ErrorMessage = $timeTransactionQueueErrorMessage;
                $timeTransactionQueue->save(); 
            }
        }
        else 
        {
            if ($timeTransactionQueue) {
                $timeTransactionQueue->QueueStatusID = QueueStatusEnum::COMPLETED_ID;
                $timeTransactionQueue->StoppageType = $stoppageType;
                $timeTransactionQueue->save(); 
            }
        }
        
        DeviceTimeTransactions::where('DeviceID', $deviceId)->update(['Active' => false]);

        return;
    }

    public function PauseDeviceTime($id)
    {
        $device = Device::findOrFail($id);
        $officialPauseTime = Carbon::now();
        $transactionType = TimeTransactionTypeEnum::PAUSE;
        $totalUsedTimeBeforePause = 0;
        $resumeTransactions = null;

        $deviceIpAddress = $device->IPAddress;

        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('POST', "http://$deviceIpAddress/api/pause");

            if ($response->getStatusCode() == 200) {

                $responseData = json_decode($response->getBody()->getContents(), true);

                //Extract Thread number
                $thread = $responseData['thread'] ?? 0;
                $thread = (int) $thread;

                $latestPauseThreadsNumber = DeviceTimeTransactions::where('DeviceID', $id)
                ->where('Thread', $thread)
                ->max('PauseThread') ?? 0;

                if ($latestPauseThreadsNumber == 0)
                {
                    $latestPauseThreadsNumber = 1;

                    $resumeTransactions = DeviceTimeTransactions::where('DeviceID', $id)
                                ->where('TransactionType', TimeTransactionTypeEnum::START)
                                ->where('Thread', $thread)
                                ->where('Active', true)
                                ->first();

                    //Update the PauseThread of the START transaction
                    $resumeTransactions->update([
                        'PauseThread' => $latestPauseThreadsNumber
                    ]);
                }
                else
                {
                    $latestPauseThreadsNumber++;

                    $resumeTransactions = DeviceTimeTransactions::where('DeviceID', $id)
                                ->where('TransactionType', TimeTransactionTypeEnum::RESUME)
                                ->where('Thread', $thread)
                                ->where('Active', true)
                                ->where('PauseThread', 0)
                                ->first();

                    //Update the PauseThread of the latest RESUME transaction
                    $resumeTransactions->update([
                        'PauseThread' => $latestPauseThreadsNumber
                    ]);
                }

                $transaction = DeviceTimeTransactions::create([
                    'DeviceID' => $device->DeviceID,
                    'TransactionType' => $transactionType,
                    'Thread' => $thread,
                    'PauseThread' => $latestPauseThreadsNumber,
                    'IsOpenTime' => false,
                    'TransactionDateTime' => $officialPauseTime,
                    'Duration' => 0,
                    'Rate' => 0,
                    'Active' => true,
                    'CreatedByUserId' => auth()->id()
                ]);

                $calculabletransactions = DeviceTimeTransactions::where('DeviceID', $id)
                        ->where('Thread', $thread)
                        ->where('Active', true)
                        ->get(['Duration', 'Rate', 'IsOpenTime', 'TransactionType', 'TransactionDateTime']);

                
                if ($latestPauseThreadsNumber > 0)
                {
                    for ($pauseThread = 1; $pauseThread <= $latestPauseThreadsNumber; $pauseThread++)
                    {
                        // Fetch the pause transaction (latest transaction)
                        $pauseTime = DeviceTimeTransactions::where('DeviceID', $id)
                                    ->where('Thread', $thread)
                                    ->where('PauseThread', $pauseThread)
                                    ->where('Active', true)
                                    ->orderBy('TransactionID', 'desc')
                                    ->first();
        
                         // Fetch the resume transaction (oldest transaction)
                        $resumeTime = DeviceTimeTransactions::where('DeviceID', $id)
                                    ->where('Thread', $thread)
                                    ->where('PauseThread', $pauseThread)
                                    ->where('Active', true)
                                    ->orderBy('TransactionID', 'asc')
                                    ->first();
        
                        if ($pauseTime && $resumeTime) {
                            $pauseDuration = Carbon::parse($resumeTime->TransactionDateTime)
                                                    ->diffInSeconds(Carbon::parse($pauseTime->TransactionDateTime));
        
                            $totalUsedTimeBeforePause += $pauseDuration;
                        }
                    }
                }              

                $startTime = Carbon::parse($resumeTransactions->TransactionDateTime);
                $totalTime = $calculabletransactions->sum('Duration');
                $totalRate = $calculabletransactions->sum('Rate');
                
                //Compute the remaining time
                $remainingTime = $totalTime - $totalUsedTimeBeforePause;

                //Get the new End Time
                $endTime = Carbon::parse($officialPauseTime)->addSeconds($remainingTime);

                // Update device status to pause
                $device->DeviceStatusID = DeviceStatusEnum::PAUSE_ID;
                $device->save();

                $deviceDisplay = DeviceDisplay::updateOrCreate(
                    // Check DeviceID existence
                    ['DeviceID' => $id],
                    // Values to update or create
                    [
                        'TransactionType' => $transactionType,
                        'IsOpenTime' => false,
                        'StartTime' => $startTime,
                        'EndTime' => $endTime,
                        'PauseTime' => $officialPauseTime,
                        'TotalTime' => $totalTime / 60,
                        'TotalRate' => $totalRate
                    ]
                );

                $timeTransactionQueue = TimeTransactionQueue::where('DeviceID', $id)
                        ->where('Thread', $thread)
                        ->where('QueueStatusID', QueueStatusEnum::ACTIVE_ID)
                        ->first();

                if ($timeTransactionQueue) {
                    $timeTransactionQueue->QueueStatusID = QueueStatusEnum::PENDING_ID;
                    $timeTransactionQueue->save(); 
                }

                $deviceTimeTransactionResponse = new DeviceTimeTransactionsResponse([
                    'DeviceID' => $id,
                    'TransactionType' => $transactionType,
                    'IsOpenTime' => false,
                    'StartTime' => $startTime,
                    'PauseTime' => $officialPauseTime,
                    'ResumeTime' => $resumeTransactions,
                    'EndTime' => $endTime,
                    'TotalTime' => $totalTime / 60,
                    'TotalRate' => $totalRate
                ]);

                event(new DeviceTransactionUpdates($deviceTimeTransactionResponse)); 

                return response()->json([
                    'success' => 'Device time started successfully.',
                    'startTime' => $startTime,
                    'endTime' => $endTime,
                    'totalTime' => $totalTime,
                    'totalRate' => $totalRate
                ]);
            }

            return $this->handleErrorResponse($response, $id);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return $this->handleGuzzleException($e, $id);
        } catch (\Exception $e) {
            // Log unexpected errors
            Log::error('Unexpected error pausing time for device ' . $id, ['error' => $e->getMessage()]);
            
            $deviceTimeTransactionResponse = new DeviceTimeTransactionsResponse([
                'DeviceID' => $id,
                'TransactionType' => $transactionType,
                'IsOpenTime' => false,
                'StartTime' => null,
                'PauseTime' => null,
                'ResumeTime' => null,
                'EndTime' => null,
                'TotalTime' => null,
                'TotalRate' => null,
                'DoHeartbeatCheck' => true
            ]);

            event(new DeviceTransactionUpdates($deviceTimeTransactionResponse)); 
            
            // return response()->json(['error' => 'An unexpected error occurred. Please try again later.'], 500);
            return response()->json(['success' => false, 'message' => 'Device ' . $device->DeviceName . ' is unresponsive. Checking status...']);
        }
    }

    public function PauseRatedTimeIntervention($id, $onlineToOffline)
    {
        $device = Device::findOrFail($id);
        $officialPauseTime = Carbon::now();
        $transactionType = TimeTransactionTypeEnum::PAUSE;
        $totalUsedTimeBeforePause = 0;
        $resumeTransactions = null;

        try {
            
            //Manually extract Thread number
            $latestDeviceThreadsNumber = DeviceTimeTransactions::where('DeviceID', $id)
            ->max('Thread');
            
            $latestPauseThreadsNumber = DeviceTimeTransactions::where('DeviceID', $id)
            ->where('Thread', $latestDeviceThreadsNumber)
            ->max('PauseThread') ?? 0;

            if ($latestPauseThreadsNumber == 0)
            {
                $latestPauseThreadsNumber = 1;

                $resumeTransactions = DeviceTimeTransactions::where('DeviceID', $id)
                            ->where('TransactionType', TimeTransactionTypeEnum::START)
                            ->where('Thread', $latestDeviceThreadsNumber)
                            ->where('Active', true)
                            ->first();

                //Update the PauseThread of the START transaction
                $resumeTransactions->update([
                    'PauseThread' => $latestPauseThreadsNumber
                ]);
            }
            else
            {
                $latestPauseThreadsNumber++;

                $resumeTransactions = DeviceTimeTransactions::where('DeviceID', $id)
                            ->where('TransactionType', TimeTransactionTypeEnum::RESUME)
                            ->where('Thread', $latestDeviceThreadsNumber)
                            ->where('Active', true)
                            ->where('PauseThread', 0)
                            ->first();

                //Update the PauseThread of the latest RESUME transaction
                $resumeTransactions->update([
                    'PauseThread' => $latestPauseThreadsNumber
                ]);
            }

            $reason = "";

            if ($onlineToOffline)
            {
                $reason = 'Node was offline while a timer was active. The timer has been paused to prevent any interruption.';
            }
            else
            {
                $reason = 'Node was restarted while a timer was active. The timer has been paused to prevent any interruption.';
            }

            $transaction = DeviceTimeTransactions::create([
                'DeviceID' => $device->DeviceID,
                'TransactionType' => $transactionType,
                'Thread' => $latestDeviceThreadsNumber,
                'PauseThread' => $latestPauseThreadsNumber,
                'IsOpenTime' => false,
                'TransactionDateTime' => $officialPauseTime,
                'Duration' => 0,
                'Rate' => 0,
                'Active' => true,
                'Reason' => $reason,
                'CreatedByUserId' => auth()->id()
            ]);

            if ($latestPauseThreadsNumber > 0)
            {
                for ($pauseThread = 1; $pauseThread <= $latestPauseThreadsNumber; $pauseThread++)
                {
                    // Fetch the pause transaction (latest transaction)
                    $pauseTime = DeviceTimeTransactions::where('DeviceID', $id)
                                ->where('Thread', $latestDeviceThreadsNumber)
                                ->where('PauseThread', $pauseThread)
                                ->where('Active', true)
                                ->orderBy('TransactionID', 'desc')
                                ->first();
    
                     // Fetch the resume transaction (oldest transaction)
                    $resumeTime = DeviceTimeTransactions::where('DeviceID', $id)
                                ->where('Thread', $latestDeviceThreadsNumber)
                                ->where('PauseThread', $pauseThread)
                                ->where('Active', true)
                                ->orderBy('TransactionID', 'asc')
                                ->first();
    
                    if ($pauseTime && $resumeTime) {
                        $pauseDuration = Carbon::parse($resumeTime->TransactionDateTime)
                                                ->diffInSeconds(Carbon::parse($pauseTime->TransactionDateTime));
    
                        $totalUsedTimeBeforePause += $pauseDuration;
                    }
                }
            }      
            
            $calculabletransactions = DeviceTimeTransactions::where('DeviceID', $id)
                        ->where('Thread', $latestDeviceThreadsNumber)
                        ->where('Active', true)
                        ->get(['Duration', 'Rate', 'IsOpenTime', 'TransactionType', 'TransactionDateTime']);

            $startTime = Carbon::parse($resumeTransactions->TransactionDateTime);
            $totalTime = $calculabletransactions->sum('Duration');
            $totalRate = $calculabletransactions->sum('Rate');
            
            //Compute the remaining time
            $remainingTime = $totalTime - $totalUsedTimeBeforePause;

            //Get the new End Time
            $endTime = Carbon::parse($officialPauseTime)->addSeconds($remainingTime);

            // Update device status to pause
            $device->DeviceStatusID = DeviceStatusEnum::PAUSE_ID;
            $device->save();

            $deviceDisplay = DeviceDisplay::updateOrCreate(
                // Check DeviceID existence
                ['DeviceID' => $id],
                // Values to update or create
                [
                    'TransactionType' => $transactionType,
                    'IsOpenTime' => false,
                    'StartTime' => $startTime,
                    'EndTime' => $endTime,
                    'PauseTime' => $officialPauseTime,
                    'TotalTime' => $totalTime / 60,
                    'TotalRate' => $totalRate
                ]
            );

            $timeTransactionQueue = TimeTransactionQueue::where('DeviceID', $id)
                        ->where('Thread', $latestDeviceThreadsNumber)
                        ->where('QueueStatusID', QueueStatusEnum::ACTIVE_ID)
                        ->first();

            if ($timeTransactionQueue) {
                $timeTransactionQueue->QueueStatusID = QueueStatusEnum::PENDING_ID;
                $timeTransactionQueue->save(); 
            }

            $deviceTimeTransactionResponse = new DeviceTimeTransactionsResponse([
                'DeviceID' => $id,
                'TransactionType' => $transactionType,
                'IsOpenTime' => false,
                'StartTime' => $startTime,
                'PauseTime' => $officialPauseTime,
                'ResumeTime' => $resumeTransactions,
                'EndTime' => $endTime,
                'TotalTime' => $totalTime / 60,
                'TotalRate' => $totalRate
            ]);

            event(new DeviceTransactionUpdates($deviceTimeTransactionResponse)); 

            return response()->json([
                'success' => 'Device time started successfully.',
                'startTime' => $startTime,
                'endTime' => $endTime,
                'totalTime' => $totalTime,
                'totalRate' => $totalRate
            ]);

            return $this->handleErrorResponse($response, $id);
        } catch (\Exception $e) {
            // Log unexpected errors
            Log::error('Unexpected error pausing time for device ' . $id, ['error' => $e->getMessage()]);
            return response()->json(['error' => 'An unexpected error occurred. Please try again later.'], 500);
        }
    }

    public function PauseOpenTimeIntervention($id, $latestDeviceThreadsNumber, $onlineToOffline)
    {
        $device = Device::findOrFail($id);
        $officialPauseTime = Carbon::now();
        $transactionType = TimeTransactionTypeEnum::PAUSE;
        $totalUsedTimeBeforePause = 0;
        $resumeTransactions = null;
        $latestResumeTime = null;
        $totalTime = 0;

        try {
            $latestPauseThreadsNumber = DeviceTimeTransactions::where('DeviceID', $id)
            ->where('Thread', $latestDeviceThreadsNumber)
            ->max('PauseThread') ?? 0;

            $startTime = DeviceTimeTransactions::where('DeviceID', $id)
                            ->where('TransactionType', TimeTransactionTypeEnum::START)
                            ->where('Thread', $latestDeviceThreadsNumber)
                            ->where('Active', true)
                            ->first();

            if ($latestPauseThreadsNumber == 0)
            {
                $latestPauseThreadsNumber = 1;

                $resumeTransactions = DeviceTimeTransactions::where('DeviceID', $id)
                            ->where('TransactionType', TimeTransactionTypeEnum::START)
                            ->where('Thread', $latestDeviceThreadsNumber)
                            ->where('Active', true)
                            ->first();

                //Update the PauseThread of the START transaction
                $resumeTransactions->update([
                    'PauseThread' => $latestPauseThreadsNumber
                ]);
            }
            else
            {
                $latestPauseThreadsNumber++;

                $resumeTransactions = DeviceTimeTransactions::where('DeviceID', $id)
                            ->where('TransactionType', TimeTransactionTypeEnum::RESUME)
                            ->where('Thread', $latestDeviceThreadsNumber)
                            ->where('Active', true)
                            ->where('PauseThread', 0)
                            ->first();

                //Update the PauseThread of the latest RESUME transaction
                $resumeTransactions->update([
                    'PauseThread' => $latestPauseThreadsNumber
                ]);
            }

            $reason = "";

            if ($onlineToOffline)
            {
                $reason = 'Node was offline while a timer was active. The timer has been paused to prevent any interruption.';
            }
            else
            {
                $reason = 'Node was restarted while a timer was active. The timer has been paused to prevent any interruption.';
            }

            $transaction = DeviceTimeTransactions::create([
                'DeviceID' => $device->DeviceID,
                'TransactionType' => $transactionType,
                'Thread' => $latestDeviceThreadsNumber,
                'PauseThread' => $latestPauseThreadsNumber,
                'IsOpenTime' => true,
                'TransactionDateTime' => $officialPauseTime,
                'Duration' => 0,
                'Rate' => 0,
                'Active' => true,
                'Reason' => $reason,
                'CreatedByUserId' => auth()->id()
            ]);

            $calculabletransactions = DeviceTimeTransactions::where('DeviceID', $id)
                        ->where('Thread', $latestDeviceThreadsNumber)
                        ->where('Active', true)
                        ->get(['Duration', 'Rate', 'IsOpenTime', 'TransactionType', 'TransactionDateTime']);

            if ($latestPauseThreadsNumber > 0)
            {
                for ($pauseThread = 1; $pauseThread <= $latestPauseThreadsNumber; $pauseThread++)
                {
                    // Fetch the pause transaction (latest transaction)
                    $pauseTime = DeviceTimeTransactions::where('DeviceID', $id)
                                ->where('Thread', $latestDeviceThreadsNumber)
                                ->where('PauseThread', $pauseThread)
                                ->where('Active', true)
                                ->orderBy('TransactionID', 'desc')
                                ->first();
    
                     // Fetch the resume transaction (oldest transaction)
                    $resumeTime = DeviceTimeTransactions::where('DeviceID', $id)
                                ->where('Thread', $latestDeviceThreadsNumber)
                                ->where('PauseThread', $pauseThread)
                                ->where('Active', true)
                                ->orderBy('TransactionID', 'asc')
                                ->first();
    
                    if ($pauseTime && $resumeTime) {

                        $latestResumeTime = $resumeTime;

                        $pauseDuration = Carbon::parse($resumeTime->TransactionDateTime)
                                                ->diffInSeconds(Carbon::parse($pauseTime->TransactionDateTime));
    
                        $totalUsedTimeBeforePause += $pauseDuration;
                    }
                }
            }      
            
            //$startTime = Carbon::parse($resumeTransactions->TransactionDateTime);

            $totalDuration = Carbon::parse($officialPauseTime)->diffInSeconds($startTime->TransactionDateTime, true);
            
            $openTimeInfo = DeviceTime::where('DeviceID', $id)
                ->where('TimeTypeID', DeviceTime::TIME_TYPE_OPEN)->first();
            if (($totalUsedTimeBeforePause / 60) < $openTimeInfo->Time) 
            {
                $totalRate = $openTimeInfo->Rate;
            } else 
            {
                $totalRate = (($totalUsedTimeBeforePause / 60) / $openTimeInfo->Time) * $openTimeInfo->Rate;
                $totalRate = number_format((float)$totalRate, 2, '.', '');
            }

            //Get the START transaction of the Thread
            $startTransactions = DeviceTimeTransactions::where('DeviceID', $id)
                ->where('TransactionType', TimeTransactionTypeEnum::START)
                ->where('Thread', $latestDeviceThreadsNumber)
                ->where('Active', true)
                ->first();
                
            //Update the Duration and Rate of the START transaction
            $startTransactions->update([
                'Duration' => $totalUsedTimeBeforePause,
                'Rate' => $totalRate
            ]);


            // Update device status to pause
            $device->DeviceStatusID = DeviceStatusEnum::PAUSE_ID;
            $device->save();

            $deviceDisplay = DeviceDisplay::updateOrCreate(
                // Check DeviceID existence
                ['DeviceID' => $id],
                // Values to update or create
                [
                    'TransactionType' => $transactionType,
                    'IsOpenTime' => true,
                    'StartTime' => $startTime->TransactionDateTime,
                    'PauseTime' => $officialPauseTime,
                    'ResumeTime' => $latestResumeTime->TransactionDateTime,
                    'TotalTime' => $totalUsedTimeBeforePause / 60,
                    'TotalRate' => $totalRate
                ]
            );

            // $timeTransactionQueue = TimeTransactionQueue::where('DeviceID', $id)
            //             ->where('Thread', $latestDeviceThreadsNumber)
            //             ->where('QueueStatusID', QueueStatusEnum::ACTIVE_ID)
            //             ->first();

            // if ($timeTransactionQueue) {
            //     $timeTransactionQueue->QueueStatusID = QueueStatusEnum::PENDING_ID;
            //     $timeTransactionQueue->save(); 
            // }

            $deviceTimeTransactionResponse = new DeviceTimeTransactionsResponse([
                'DeviceID' => $id,
                'TransactionType' => $transactionType,
                'IsOpenTime' => true,
                'StartTime' => $startTime->TransactionDateTime,
                'PauseTime' => $officialPauseTime,
                'ResumeTime' => $latestResumeTime->TransactionDateTime,
                'EndTime' => null,
                'TotalTime' => $totalUsedTimeBeforePause / 60,
                'TotalRate' => $totalRate,
                'TotalUsedTime' => $totalUsedTimeBeforePause 
            ]);

            event(new DeviceTransactionUpdates($deviceTimeTransactionResponse)); 

            return response()->json([
                'success' => 'Device time started successfully.',
                'startTime' => $startTime,
                // 'endTime' => $endTime,
                'endTime' => null,
                'totalTime' => $totalTime,
                'totalRate' => $totalRate
            ]);

            return $this->handleErrorResponse($response, $id);
        } catch (\Exception $e) {
            // Log unexpected errors
            Log::error('Unexpected error pausing time for device ' . $id, ['error' => $e->getMessage()]);
            return response()->json(['error' => 'An unexpected error occurred. Please try again later.'], 500);
        }
    }

    public function ResumeDeviceTime($id)
    {
        $device = Device::findOrFail($id);
        $officialResumeTime = Carbon::now();
        $transactionType = TimeTransactionTypeEnum::RESUME;
        $totalUsedTimeBeforePause = 0;
        $isOpenTime = false;
        $latestPauseTime = null;
        $totalTime = 0;

        $deviceIpAddress = $device->IPAddress;

        try {
            //Manually extract Thread number
            $thread = DeviceTimeTransactions::where('DeviceID', $id)
            ->max('Thread');

            $calculabletransactions = DeviceTimeTransactions::where('DeviceID', $id)
                    ->where('Thread', $thread)
                    ->where('Active', true)
                    ->get(['Duration', 'Rate', 'IsOpenTime', 'TransactionType', 'TransactionDateTime']);
            $openTimeTransaction = $calculabletransactions->firstWhere('IsOpenTime', 1);

            $latestPauseThreadsNumber = DeviceTimeTransactions::where('DeviceID', $id)
                    ->where('Thread', $thread)
                    ->max('PauseThread');

            if ($latestPauseThreadsNumber > 0)
            {
                for ($pauseThread = 1; $pauseThread <= $latestPauseThreadsNumber; $pauseThread++)
                {
                    // Fetch the pause transaction (latest transaction)
                    $pauseTime = DeviceTimeTransactions::where('DeviceID', $id)
                        ->where('Thread', $thread)
                        ->where('PauseThread', $pauseThread)
                        ->where('Active', true)
                        ->orderBy('TransactionID', 'desc')
                        ->first();

                    // Fetch the resume transaction (oldest transaction)
                    $resumeTime = DeviceTimeTransactions::where('DeviceID', $id)
                        ->where('Thread', $thread)
                        ->where('PauseThread', $pauseThread)
                        ->where('Active', true)
                        ->orderBy('TransactionID', 'asc')
                        ->first();

                    if ($pauseTime && $resumeTime) {

                        $latestPauseTime = $pauseTime;

                        $pauseDuration = Carbon::parse($resumeTime->TransactionDateTime)
                                        ->diffInSeconds(Carbon::parse($pauseTime->TransactionDateTime));

                        $totalUsedTimeBeforePause += $pauseDuration;
                    }
                }
            }

            $startTime = Carbon::parse($calculabletransactions->min('TransactionDateTime'));

            if ($openTimeTransaction)
            {
                $totalDuration = Carbon::parse($latestPauseTime->TransactionDateTime)->diffInSeconds($startTime, true);
                $openTimeInfo = DeviceTime::where('DeviceID', $id)
                    ->where('TimeTypeID', DeviceTime::TIME_TYPE_OPEN)->first();
                if (($totalDuration / 60) < $openTimeInfo->Time) 
                {
                    $totalRate = $openTimeInfo->Rate;
                } else 
                {
                    $totalRate = (($totalDuration / 60) / $openTimeInfo->Time) * $openTimeInfo->Rate;
                    $totalRate = number_format((float)$totalRate, 2, '.', '');
                }

                //Get the START transaction of the Thread
                $startTransactions = DeviceTimeTransactions::where('DeviceID', $id)
                    ->where('TransactionType', TimeTransactionTypeEnum::START)
                    ->where('Thread', $thread)
                    ->where('Active', true)
                    ->first();
                    
                //Update the Duration and Rate of the START transaction
                $startTransactions->update([
                    'Duration' => $totalDuration,
                    'Rate' => $totalRate
                ]);
            }
            else 
            {
                $totalTime = $calculabletransactions->sum('Duration');
                $totalRate = $calculabletransactions->sum('Rate');
            }

            //Compute the remaining time
            if ($openTimeTransaction)
            {
                $remainingTime = $totalUsedTimeBeforePause;
                $isOpenTime = true;
            }
            else
            {
                $remainingTime = $totalTime - $totalUsedTimeBeforePause;
            }
        
            //Get the new End Time
            $endTime = Carbon::parse($officialResumeTime)->addSeconds($remainingTime);

            $client = new \GuzzleHttp\Client();
            $response = $client->request('POST', "http://$deviceIpAddress/api/resume", [
                'json' => [
                    'remainingTime' => $remainingTime,
                    'openTime' => $isOpenTime
                ]
            ]);

            if ($response->getStatusCode() == 200) {

                $responseData = json_decode($response->getBody()->getContents(), true);

                $transaction = DeviceTimeTransactions::create([
                    'DeviceID' => $device->DeviceID,
                    'TransactionType' => $transactionType,
                    'Thread' => $thread,
                    'IsOpenTime' => $isOpenTime,
                    'TransactionDateTime' => $officialResumeTime,
                    'Duration' => 0,
                    'Rate' => 0,
                    'Active' => true,
                    'CreatedByUserId' => auth()->id()
                ]);
                
                // Update device status to running
                $device->DeviceStatusID = DeviceStatusEnum::RUNNING_ID;
                $device->save();

                $deviceDisplay = DeviceDisplay::updateOrCreate(
                    // Check DeviceID existence
                    ['DeviceID' => $id],
                    // Values to update or create
                    [
                        'TransactionType' => $transactionType,
                        'IsOpenTime' => $isOpenTime,
                        'StartTime' => $officialResumeTime,
                        'EndTime' => $isOpenTime ? null : $endTime,
                        'ResumeTime' => $officialResumeTime,
                        'TotalTime' => $totalTime / 60,
                        'TotalRate' => $totalRate
                    ]
                );

                $timeTransactionQueue = TimeTransactionQueue::where('DeviceID', $id)
                        ->where('Thread', $thread)
                        ->where('QueueStatusID', QueueStatusEnum::PENDING_ID)
                        ->first();

                if ($timeTransactionQueue) {
                    $timeTransactionQueue->EndTime = $endTime;
                    $timeTransactionQueue->QueueStatusID = QueueStatusEnum::ACTIVE_ID;
                    $timeTransactionQueue->save(); 
                }

                $deviceTimeTransactionResponse = new DeviceTimeTransactionsResponse([
                    'DeviceID' => $id,
                    'TransactionType' => $transactionType,
                    'IsOpenTime' => $isOpenTime,
                    'StartTime' => $startTime,
                    'PauseTime' => $latestPauseTime ? Carbon::parse($latestPauseTime->TransactionDateTime)->toISOString() : null,
                    'ResumeTime' => $officialResumeTime,
                    'EndTime' => $isOpenTime ? null : $endTime,
                    'TotalTime' => $isOpenTime ? null : $totalTime / 60,
                    'TotalRate' => $totalRate,
                    'Test' => $openTimeTransaction,
                    'TotalUsedTime' => $totalUsedTimeBeforePause 
                ]);

                event(new DeviceTransactionUpdates($deviceTimeTransactionResponse)); 

                return response()->json([
                    'success' => 'Device time started successfully.',
                    'startTime' => $startTime,
                    'endTime' => $endTime,
                    'totalTime' => $totalTime,
                    'totalRate' => $totalRate
                ]);
            }

            return response()->json(['success' => false, 'message' => 'Failed to resume time on the device.'], $response->getStatusCode());
        } catch (\Exception $e) {
            Log::error('Error resuming time for device ' . $id, ['error' => $e->getMessage()]);

            $deviceTimeTransactionResponse = new DeviceTimeTransactionsResponse([
                'DeviceID' => $id,
                'TransactionType' => $transactionType,
                'IsOpenTime' => false,
                'StartTime' => null,
                'PauseTime' => null,
                'ResumeTime' => null,
                'EndTime' => null,
                'TotalTime' => null,
                'TotalRate' => null,
                'DoHeartbeatCheck' => true
            ]);

            event(new DeviceTransactionUpdates($deviceTimeTransactionResponse)); 
            
            // return response()->json(['error' => 'An unexpected error occurred. Please try again later.'], 500);
            return response()->json(['success' => false, 'message' => 'Device ' . $device->DeviceName . ' is unresponsive. Checking status...']);
        }
    }

    public function StartFreeLight(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        $device = Device::findOrFail($id);
        $reason = $request->input('reason');
        $transactionType = TimeTransactionTypeEnum::STARTFREE;
        $officialStartFreeTime =   Carbon::now();
        $deviceIpAddress = $device->IPAddress;

        $latestThreadsNumber = DeviceTimeTransactions::max('Thread') + 1 ?? 1;

        $freeTime = DeviceTime::where('DeviceID', $request->device_id)
                ->where('TimeTypeID', DeviceTime::TIME_TYPE_FREE)
                ->first();

        if (!$freeTime)
        {
            return response()->json([
                'success' => false,
                'message' => 'Free time limit not configured.'
            ]);
        }

        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('POST', "http://$deviceIpAddress/api/startfree", [
                'json' => [
                    'thread' => $latestThreadsNumber,
                    'span' => $freeTime->Time * 60
                ]
            ]);

            if ($response->getStatusCode() == 200) {

                $transaction = DeviceTimeTransactions::create([
                    'DeviceID' => $device->DeviceID,
                    'TransactionType' => $transactionType,
                    'Thread' => $latestThreadsNumber,
                    'IsOpenTime' => false,
                    'TransactionDateTime' => $officialStartFreeTime,
                    'Duration' => $freeTime->Time * 60,
                    'Rate' => 0,
                    'Active' => true,
                    'Reason' => $reason,
                    'CreatedByUserId' => auth()->id(),
                ]);

                // Update device status to running
                $device->DeviceStatusID = DeviceStatusEnum::STARTFREE_ID;
                $device->save();

                $startTime = Carbon::parse($transaction->StartTime);
                $endTime = $startTime->clone()->addMinutes($freeTime->Time);
                $totalTime = $freeTime->Time;
                $totalRate = 0;

                $timeTransactionQueue = TimeTransactionQueue::create([
                    'DeviceID' => $id,
                    'DeviceStatusID' => DeviceStatusEnum::STARTFREE_ID,
                    'Thread' => $latestThreadsNumber,
                    'EndTime' => $endTime,
                    'QueueStatusID' => QueueStatusEnum::ACTIVE_ID,
                    'ErrorMessage' => null
                ]);

                $deviceDisplay = DeviceDisplay::updateOrCreate(
                    // Check DeviceID existence
                    ['DeviceID' => $id],
                    // Values to update or create
                    [
                        'TransactionType' => $transactionType,
                        'IsOpenTime' => false,
                        'StartTime' => $startTime,
                        'EndTime' => $endTime,
                        'TotalTime' => $totalTime,
                        'TotalRate' => $totalRate
                    ]
                );

                return response()->json([
                    'success' => 'Device free light started successfully.'
                ]);
            }

            return $this->handleErrorResponse($response, $id);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return $this->handleGuzzleException($e, $id);
        } catch (\Exception $e) {
            // Log unexpected errors
            Log::error('Unexpected error starting free light for device ' . $id, ['error' => $e->getMessage()]);
            return response()->json(['error' => 'An unexpected error occurred. Please try again later.'], 500);
        }
    }

    public function EndDeviceStartFreeTimeAuto($id)
    {
        $device = Device::findOrFail($id);
        $officialStopFreeTime = Carbon::now();
        $response = null;
        $deviceIpAddress = $device->IPAddress;

        $transactionType = TimeTransactionTypeEnum::ENDFREE;
        $startTime = null;
        $totalDuration = null;
        $totalRate = 0;
        $latestDeviceThreadsNumber = DeviceTimeTransactions::where('DeviceID', $id)
                    ->max('Thread');
                        
        try {

            $calculabletransactions = DeviceTimeTransactions::where('DeviceID', $id)
                        ->where('Thread', $latestDeviceThreadsNumber)
                        ->where('Active', true)
                        ->get(['Duration', 'Rate', 'IsOpenTime', 'TransactionDateTime']);
    
            $startTime = Carbon::parse($calculabletransactions->min('TransactionDateTime'));
            $totalDuration = $calculabletransactions->sum('Duration');
            $totalRate = $calculabletransactions->sum('Rate');

            $device->DeviceStatusID = DeviceStatusEnum::INACTIVE_ID;
            $device->save();
            
            $deviceDisplay = DeviceDisplay::updateOrCreate(
                // Check DeviceID existence
                ['DeviceID' => $id],
                // Values to update or create
                [
                    'TransactionType' => $transactionType,
                    'IsOpenTime' => false,
                    'StartTime' => $startTime,
                    'EndTime' => $officialStopFreeTime,
                    'PauseTime' => null,
                    'TotalTime' => 0,
                    'TotalRate' => 0
                ]
            );

            $client = new \GuzzleHttp\Client();
            $response = $client->request('POST', "http://$deviceIpAddress/api/stopfree");

            if ($response->getStatusCode() == 200) 
            {
                $transaction = DeviceTimeTransactions::create([
                    'DeviceID' => $device->DeviceID,
                    'TransactionType' => $transactionType,
                    'Thread' => $latestDeviceThreadsNumber,
                    'IsOpenTime' => false,
                    'TransactionDateTime' => $officialStopFreeTime,
                    'Duration' => 0,
                    'Rate' => 0,
                    'Active' => false,
                    'CreatedByUserId' => auth()->id(),
                ]);

                $notification = Notifications::create([
                    'Notification' => 'Node ' . $device->DeviceName . ' free light was ended successfully.',
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

                $deviceTimeTransactionResponse = new DeviceTimeTransactionsResponse([
                    'DeviceID' => $id,
                    'TransactionType' => $transactionType,
                    'IsOpenTime' => false,
                    'StartTime' => $startTime,
                    'PauseTime' => null,
                    'ResumeTime' => null,
                    'EndTime' => $officialStopFreeTime,
                    'TotalTime' => $totalDuration,
                    'TotalRate' => $totalRate
                ]);

                event(new DeviceTransactionUpdates($deviceTimeTransactionResponse));

                DeviceTimeTransactions::where('DeviceID', $id)->update(['Active' => false]);

                $deviceTimeController = new DeviceTimeController();
                $deviceTimeController->UpdateEndTime($id, $latestDeviceThreadsNumber, $deviceTimeTransactionResponse, StoppageTypeEnum::AUTO, null, true);

                return response()->json([
                    'success' => 'Device free time ended successfully.',
                    'startTime' => $startTime->format('Y-m-d H:i:s'),
                    'endTime' => $officialStopFreeTime->format('Y-m-d H:i:s'),
                    'totalTime' => $totalDuration,
                    'totalRate' => $totalRate
                ]);
            }

            return $this->handleErrorResponse($response, $id);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return $this->handleGuzzleException($e, $id);
        } catch (\Exception $e) {
            Log::error('Unexpected error stopping device time for device ' . $id, ['error' => $e->getMessage()]);
            
            $transaction = DeviceTimeTransactions::create([
                'DeviceID' => $device->DeviceID,
                'TransactionType' => $transactionType,
                'Thread' => $latestDeviceThreadsNumber,
                'IsOpenTime' => false,
                'TransactionDateTime' => $officialStopFreeTime,
                'Duration' => 0,
                'Rate' => 0,
                'Active' => false,
                'Reason' => 'Node was unreachable while attempting to end the free timer. The timer has been ended with an error status.',
                'CreatedByUserId' => auth()->id(),
            ]);

            $notification = Notifications::create([
                'Notification' => 'Node ' . $device->DeviceName . ' was unreachable while attempting to end the free timer. The timer has been ended with an error status.',
                'NotificationLevelID' => NotificationLevelEnum::ERROR_ID,
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

            $deviceTimeTransactionResponse = new DeviceTimeTransactionsResponse([
                'DeviceID' => $id,
                'TransactionType' => $transactionType,
                'IsOpenTime' => false,
                'StartTime' => $startTime,
                'PauseTime' => null,
                'ResumeTime' => null,
                'EndTime' => $officialStopFreeTime,
                'TotalTime' => $totalDuration,
                'TotalRate' => $totalRate,
                'DoHeartbeatCheck' => true
            ]);

            event(new DeviceTransactionUpdates($deviceTimeTransactionResponse)); 

            $deviceTimeController = new DeviceTimeController();
            $deviceTimeController->UpdateEndTime($id, $latestDeviceThreadsNumber, $deviceTimeTransactionResponse, StoppageTypeEnum::AUTO, $e->getMessage());

            return response()->json(['error' => 'An unexpected error occurred. Please try again later.'], 500);
        }
    }

    public function StopFreeLight($id)
    {
        $device = Device::findOrFail($id);
        $officialStopFreeTime = Carbon::now();
        $response = null;
        $deviceIpAddress = $device->IPAddress;

        $transactionType = TimeTransactionTypeEnum::ENDFREE;
        $startTime = null;
        $totalDuration = null;
        $totalRate = 0;
        $latestDeviceThreadsNumber = DeviceTimeTransactions::where('DeviceID', $id)
                    ->max('Thread');
        
        try 
        {
            $calculabletransactions = DeviceTimeTransactions::where('DeviceID', $id)
                        ->where('Thread', $latestDeviceThreadsNumber)
                        ->where('Active', true)
                        ->get(['Duration', 'Rate', 'IsOpenTime', 'TransactionDateTime']);
    
            $startTime = Carbon::parse($calculabletransactions->min('TransactionDateTime'));
            $totalDuration = $calculabletransactions->sum('Duration');
            $totalRate = $calculabletransactions->sum('Rate');

            $device->DeviceStatusID = DeviceStatusEnum::INACTIVE_ID;
            $device->save();

            $deviceDisplay = DeviceDisplay::updateOrCreate(
                // Check DeviceID existence
                ['DeviceID' => $id],
                // Values to update or create
                [
                    'TransactionType' => $transactionType,
                    'IsOpenTime' => false,
                    'StartTime' => $startTime,
                    'EndTime' => $officialStopFreeTime,
                    'PauseTime' => null,
                    'TotalTime' => 0,
                    'TotalRate' => 0
                ]
            );

            $client = new \GuzzleHttp\Client();
            $response = $client->request('POST', "http://$deviceIpAddress/api/stopfree");

            if ($response->getStatusCode() == 200) {

                $transaction = DeviceTimeTransactions::create([
                    'DeviceID' => $device->DeviceID,
                    'TransactionType' => $transactionType,
                    'Thread' => $latestDeviceThreadsNumber,
                    'IsOpenTime' => false,
                    'TransactionDateTime' => $officialStopFreeTime,
                    'Duration' => 0,
                    'Rate' => 0,
                    'Active' => false,
                    'CreatedByUserId' => auth()->id(),
                ]);

                $deviceTimeTransactionResponse = new DeviceTimeTransactionsResponse([
                    'DeviceID' => $id,
                    'TransactionType' => $transactionType,
                    'IsOpenTime' => false,
                    'StartTime' => $startTime,
                    'PauseTime' => null,
                    'ResumeTime' => null,
                    'EndTime' => $officialStopFreeTime,
                    'TotalTime' => $totalDuration,
                    'TotalRate' => $totalRate
                ]);

                event(new DeviceTransactionUpdates($deviceTimeTransactionResponse));
               
                DeviceTimeTransactions::where('DeviceID', $id)->update(['Active' => false]);

                $deviceTimeController = new DeviceTimeController();
                $deviceTimeController->UpdateEndTime($id, $latestDeviceThreadsNumber, $deviceTimeTransactionResponse, StoppageTypeEnum::MANUAL, null, true);

                return response()->json([
                    'success' => 'Free light stopped successfully.'
                ]);
            }

            return $this->handleErrorResponse($response, $id);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return $this->handleGuzzleException($e, $id);
        } catch (\Exception $e) {
            Log::error('Unexpected error stopping device time for device ' . $id, ['error' => $e->getMessage()]);

            $transaction = DeviceTimeTransactions::create([
                'DeviceID' => $device->DeviceID,
                'TransactionType' => $transactionType,
                'Thread' => $latestDeviceThreadsNumber,
                'IsOpenTime' => false,
                'TransactionDateTime' => $officialStopFreeTime,
                'Duration' => 0,
                'Rate' => 0,
                'Active' => false,
                'Reason' => 'Node was unreachable while attempting to end the free timer. The timer has been ended with an error status.',
                'CreatedByUserId' => auth()->id(),
            ]);

            $notification = Notifications::create([
                'Notification' => 'Node ' . $device->DeviceName . ' was unreachable while attempting to end the free timer. The timer has been ended with an error status.',
                'NotificationLevelID' => NotificationLevelEnum::ERROR_ID,
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

            $deviceTimeTransactionResponse = new DeviceTimeTransactionsResponse([
                'DeviceID' => $id,
                'TransactionType' => $transactionType,
                'IsOpenTime' => false,
                'StartTime' => $startTime,
                'PauseTime' => null,
                'ResumeTime' => null,
                'EndTime' => $officialStopFreeTime,
                'TotalTime' => $totalDuration,
                'TotalRate' => $totalRate,
                'DoHeartbeatCheck' => true
            ]);

            event(new DeviceTransactionUpdates($deviceTimeTransactionResponse)); 

            $deviceTimeController = new DeviceTimeController();
            $deviceTimeController->UpdateEndTime($id, $latestDeviceThreadsNumber, $deviceTimeTransactionResponse, StoppageTypeEnum::MANUAL, $e->getMessage());

            // return response()->json(['error' => 'An unexpected error occurred. Please try again later.'], 500);
            return response()->json(['success' => false, 'message' => 'Device ' . $device->DeviceName . ' is unresponsive. Checking status...']);
        }
    }

    public function StopFreeLightInvervention($id, $onlineToOffline)
    {
        $device = Device::findOrFail($id);
        $officialStopFreeTime = Carbon::now();
        $transactionType = TimeTransactionTypeEnum::ENDFREE;
        $startTime = null;
        $totalDuration = null;
        $totalRate = 0;
        $latestDeviceThreadsNumber = DeviceTimeTransactions::where('DeviceID', $id)
                ->max('Thread');
        $reason = "";

        try 
        {
            $calculabletransactions = DeviceTimeTransactions::where('DeviceID', $id)
            ->where('Thread', $latestDeviceThreadsNumber)
            ->where('Active', true)
            ->get(['Duration', 'Rate', 'IsOpenTime', 'TransactionDateTime']);

            $startTime = Carbon::parse($calculabletransactions->min('TransactionDateTime'));
            $totalDuration = $calculabletransactions->sum('Duration');
            $totalRate = $calculabletransactions->sum('Rate');

            $device->DeviceStatusID = DeviceStatusEnum::INACTIVE_ID;
            $device->save();

            $deviceDisplay = DeviceDisplay::updateOrCreate(
                // Check DeviceID existence
                ['DeviceID' => $id],
                // Values to update or create
                [
                    'TransactionType' => $transactionType,
                    'IsOpenTime' => false,
                    'StartTime' => $startTime,
                    'EndTime' => $officialStopFreeTime,
                    'PauseTime' => null,
                    'TotalTime' => 0,
                    'TotalRate' => 0
                ]
            );

            if ($onlineToOffline)
            {
                $reason = 'Node was offline while free light was active. Ending free light to prevent any interruption.';
            }
            else
            {
                $reason = 'Node was restarted while free light was active. Ending free light to prevent any interruption.';
            }

            $transaction = DeviceTimeTransactions::create([
                'DeviceID' => $device->DeviceID,
                'TransactionType' => $transactionType,
                'Thread' => $latestDeviceThreadsNumber,
                'IsOpenTime' => false,
                'TransactionDateTime' => $officialStopFreeTime,
                'Duration' => 0,
                'Rate' => 0,
                'Active' => false,
                'Reason' => $reason,
                'CreatedByUserId' => auth()->id(),
            ]);

            $deviceTimeTransactionResponse = new DeviceTimeTransactionsResponse([
                'DeviceID' => $id,
                'TransactionType' => $transactionType,
                'IsOpenTime' => false,
                'StartTime' => $startTime,
                'PauseTime' => null,
                'ResumeTime' => null,
                'EndTime' => $officialStopFreeTime,
                'TotalTime' => $totalDuration,
                'TotalRate' => $totalRate,
                'DoHeartbeatCheck' => true
            ]);

            event(new DeviceTransactionUpdates($deviceTimeTransactionResponse));

            DeviceTimeTransactions::where('DeviceID', $id)->update(['Active' => false]);

            $deviceTimeController = new DeviceTimeController();
            $deviceTimeController->UpdateEndTime($id, $latestDeviceThreadsNumber, $deviceTimeTransactionResponse, StoppageTypeEnum::AUTO, $reason, true);


            return response()->json([
                'success' => 'Free light stopped successfully.'
            ]);

        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return $this->handleGuzzleException($e, $id);
        } catch (\Exception $e) {
            // Log unexpected errors
            Log::error('Unexpected error stopping device time for device ' . $id, ['error' => $e->getMessage()]);
            return response()->json(['error' => 'An unexpected error occurred. Please try again later.'], 500);
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
