<?php
namespace App\WebSockets;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Illuminate\Support\Facades\Event;

class WebSocketServer implements MessageComponentInterface
{
    protected $clients;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        echo "WebSocket Server started...\n";
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        echo "New connection: ({$conn->resourceId})\n";

        //Send initial server time for time sync 
        //(this will be used to sync the client time with the server)
        $payload = json_encode([
            'type' => 'server-time-sync',
            'serverTime' => round(microtime(true) * 1000), // timestamp in ms
        ]);
        echo "Synced time: {$payload}";
        $conn->send($payload);
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        echo "Message received: {$msg}\n";

        foreach ($this->clients as $client) {
            if ($from !== $client) {
                $client->send($msg);
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Error: {$e->getMessage()}\n";
        $conn->close();
    }
}
