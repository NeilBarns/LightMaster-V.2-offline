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

class DeviceTransactionUpdates implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    public $deviceTransactionResponse;

    public function __construct(DeviceTimeTransactionsResponse $deviceTransactionResponse)
    {
        $this->deviceTransactionResponse = $deviceTransactionResponse;

        $resolvedIp = gethostbyname('lightmaster.local');
        $websocketUrl = "ws://{$resolvedIp}:8080";

        $message = [
            'type' => 'device.transaction.updates',
            'payload' => $this->deviceTransactionResponse
        ];

        try {
            $client = new Client($websocketUrl);
            $client->send(json_encode($message));
            $client->close();
    
            return response()->json(['success' => true, 'message' => 'Message sent to WebSocket server.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function broadcastOn()
    {
        return new Channel('TransactionsChannel');
    }

    public function broadcastAs()
    {
        return 'device.transaction.updates';
    }
}
