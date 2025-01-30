<?php

namespace App\Events;

use App\Models\Device;
use App\Models\DeviceTimeTransactionsResponse;
use App\Models\NotificationResponse;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use WebSocket\Client;

class NotificationUpdates implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    public $notificationResponse;

    public function __construct(NotificationResponse $notificationResponse)
    {
        $this->notificationResponse = $notificationResponse;

        $message = [
            'type' => 'device.notification.updates',
            'payload' => $this->notificationResponse
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
        return new Channel('NotificationsChannel');
    }

    public function broadcastAs()
    {
        return 'device.notification.updates';
    }
}
