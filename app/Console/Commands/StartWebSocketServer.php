<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\WebSockets\WebSocketServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\Server\IoServer;

class StartWebSocketServer extends Command
{
    protected $signature = 'websocket:start';
    protected $description = 'Start the WebSocket server';

    public function __construct() {
        parent::__construct();
    }

    public function handle() {
        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    app(WebSocketServer::class)
                )
            ),
            8080,
            '0.0.0.0' // Allow connections from other machines
        );

        $this->info('WebSocket server started on port 8080');
        $server->run();
    }
}
