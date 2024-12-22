<?php

namespace App\Console\Commands;

use App\Events\DeviceUpdated;
use Illuminate\Console\Command;
use App\Models\Device; // Import your model if necessary

class CheckDeviceStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'device:check-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the status of devices and update their status if offline';

    /**
     * Execute the console command.
     *
     * @return int
     */
    
    public function handle()
    {
        $timeout = now()->subMinutes(5);

        // Devices going offline
        $offlineDevices = Device::where('last_heartbeat', '<', $timeout)->where('IsOnline', 1)->get();
        foreach ($offlineDevices as $device) {
            $device->update(['IsOnline' => 0]);
            event(new DeviceUpdated($device)); // Fire event for offline devices
        }

        // Devices coming online
        $onlineDevices = Device::where('last_heartbeat', '>=', $timeout)->where('IsOnline', 0)->get();
        foreach ($onlineDevices as $device) {
            $device->update(['IsOnline' => 1]);
            event(new DeviceUpdated($device)); // Fire event for online devices
        }

        $this->info('Device status has been checked and updated.');
        return 0;
    }

}
