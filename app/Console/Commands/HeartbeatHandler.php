<?php

namespace App\Console\Commands;

use App\Http\Controllers\DeviceManagementController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class HeartbeatHandler extends Command
{
    protected $signature = 'device-heartbeat:handler';
    protected $description = 'Check and sets device online status';

    private $deviceManagementController;

    public function __construct(DeviceManagementController $deviceManagementController)
    {
        parent::__construct();
        $this->deviceManagementController = $deviceManagementController;
    }

    public function handle()
    {
        try {
            $this->deviceManagementController->UpdateHeartbeatDeviceStatusToOffline();
        } catch (\Exception $e) {
            Log::error("[JOB]HeartbeatHandler: Failed to process device online status. ", [
                'error' => $e->getMessage()
            ]);
        }
    }
}
