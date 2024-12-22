<?php

namespace App\Events;

use App\Models\Device;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class DeviceUpdated implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $device;

    public function __construct(Device $device)
    {
        $this->device = $device;
    }

    public function broadcastOn()
    {
        return new Channel('devices');
    }

    public function broadcastAs()
    {
        return 'device.updated';
    }
}
