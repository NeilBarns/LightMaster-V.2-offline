<?php

namespace App\Events;

use App\Models\Device;
use App\Models\DeviceTimeTransactionsResponse;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use WebSocket\Client;

class DeviceAddRemoveUpdates implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    public function __construct()
    {
        $message = [
            'type' => 'device.add.remove.updates'
        ];

        try {
            $client = new Client(env('WEBSOCKET_URL'));
            $client->send(json_encode($message));
            $client->close();
    
            return response()->json(['success' => true, 'message' => 'Message sent to WebSocket server.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function broadcastOn()
    {
        return new Channel('AddRemoveChannel');
    }

    public function broadcastAs()
    {
        return 'device.add.remove.updates';
    }
}
