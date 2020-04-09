<?php


namespace App\Services;


use Hhxsv5\LaravelS\Swoole\WebSocketHandlerInterface;
use Illuminate\Support\Facades\Log;
use Swoole\Http\Request;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

class WebScoketService implements WebSocketHandlerInterface
{

    public function __construct()
    {
    }

    /**
     * 建立连接时触发
     * @param Server $server
     * @param Request $request
     */
    public function onOpen(Server $server, Request $request)
    {
        // 在触发 WebSocket 连接建立事件之前，Laravel 应用初始化的生命周期已经结束，你可以在这里获取 Laravel 请求和会话数据
        Log::info('WebSocket连接建立, Client Id: '. $request->fd);
        app('swoole')->wsTable->set('fd'. $request->fd, ['value' => $request->fd]);
        $server->push($request->fd, 'Welcome to WebSocket Server built on LaravelS');
    }

    /**
     * 收到消息🥌时触发
     * @param Server $server
     * @param Frame $frame
     */
    public function onMessage(Server $server, Frame $frame)
    {
        foreach (app('swoole')->wsTable as $k => $row) {
            if (strpos($k, 'fd') === 0 && $server->exist($row['value'])) {
                Log::info('Receive message from client:'. $row['value']);
                $server->push($frame->fd, 'This is a message sent from WebSocket Server'. date('Y-m-d H:i:s'));
            }
        }

    }

    /**
     * 关闭连接时触发
     * @param Server $server
     * @param $fd
     * @param $reactorId
     */
    public function onClose(Server $server, $fd, $reactorId)
    {
        Log::info('WebSocket连接关闭, Client Id: '. $fd);
    }
}
