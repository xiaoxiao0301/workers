<?php

namespace App\Handlers;


use Hhxsv5\LaravelS\Swoole\WebSocketHandlerInterface;
use Illuminate\Support\Facades\Log;
use Swoole\Http\Request;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

class WebSocketHandler implements WebSocketHandlerInterface
{

    public function __construct()
    {
    }

    public function onOpen(Server $server, Request $request)
    {
        Log::info("WebSocket 连接建立了:". $request->fd);
    }

    public function onMessage(Server $server, Frame $frame)
    {
        Log::info("从 {$frame->fd} 接收到的数据是: {$frame->data}");
        foreach ($server->connections as $fd) {
            if (!$server->isEstablished($fd)) {
                // 连接不可用忽略
                continue;
            }
            $server->push($fd, $frame->data);
        }
    }

    public function onClose(Server $server, $fd, $reactorId)
    {
        Log::info("WebSocket Client {$fd} 连接关闭!");
    }
}
