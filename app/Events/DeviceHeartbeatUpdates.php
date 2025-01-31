<?php

namespace App\Events;

use App\Models\DeviceHeartbeatStatusResponse;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use WebSocket\Client;

class DeviceHeartbeatUpdates implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    public $deviceHeartbeatResponse;

    public function __construct(DeviceHeartbeatStatusResponse $deviceHeartbeatResponse)
    {
        $this->deviceHeartbeatResponse = $deviceHeartbeatResponse;

        $message = [
            'type' => 'device.heartbeat.updates',
            'payload' => $this->deviceHeartbeatResponse
        ];

        try {
            $client = new Client(config('app.websocket_url'));
            $client->send(json_encode($message));
            $client->close();
    
            return response()->json(['success' => true, 'message' => 'Message sent to WebSocket server.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function broadcastOn()
    {
        return new Channel('HeartbeatsChannel');
    }

    public function broadcastAs()
    {
        return 'device.heartbeat.updates';
    }
}
