<?php

namespace App\Console\Commands;

use App\Enums\DeviceStatusEnum;
use App\Enums\QueueStatusEnum;
use App\Http\Controllers\DeviceTimeController;
use App\Models\TimeTransactionQueue;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Psy\Readline\Hoa\Console;

class TimeTransactionQueueHandler extends Command
{
    protected $signature = 'time-transaction-queue:handler';
    protected $description = 'Check and process expired timers';

    private $deviceTimeController;

    public function __construct(DeviceTimeController $deviceTimeController)
    {
        parent::__construct();
        $this->deviceTimeController = $deviceTimeController;
    }

    public function handle()
    {
        try {
            $now = Carbon::now();

            $expiredTimers = TimeTransactionQueue::where('QueueStatusID', QueueStatusEnum::ACTIVE_ID)
                ->where('EndTime', '<=', $now)
                ->whereDoesntHave('device', function ($query) {
                    $query->where('DeviceStatusID', DeviceStatusEnum::PAUSE_ID);
                })
                ->distinct()
                ->get();

            if ($expiredTimers->isEmpty()) {
                $this->info('No expired timers found.');
                return;
            }

            foreach ($expiredTimers as $timer) {
                $this->processExpiredTimer($timer);
            }
        } catch (\Exception $e) {
            Log::error('Error processing expired timers', ['error' => $e->getMessage()]);
        }
    }

    private function processExpiredTimer($timer)
    {
        try {
            if ($timer->DeviceStatusID == DeviceStatusEnum::STARTFREE_ID)
            {
                $this->deviceTimeController->EndDeviceStartFreeTimeAuto($timer->DeviceID);
            }
            else
            {
                $this->deviceTimeController->EndDeviceTimeAuto($timer->DeviceID);
            }
            
            $this->info("Processed expired timer for DeviceID: {$timer->DeviceID}");
        } catch (\Exception $e) {
            Log::error("[JOB]TimeTransactionQueueHandler: Failed to process timer for DeviceID: {$timer->DeviceID}", [
                'error' => $e->getMessage()
            ]);
        }
        finally {
        }
    }
}
