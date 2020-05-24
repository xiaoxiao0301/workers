<?php

namespace App\GatewayWorker;

use GatewayWorker\Lib\Gateway;

/**
 * Class Events
 * @package App\GatewayWorker
 */
class Events
{
    public static function onWorkerStart($businessWorker)
    {
        echo "BusinessWorker Start\n";
    }

    public static function onConnect($client_id)
    {
        // 向当前client_id发送数据
        Gateway::sendToClient($client_id, "Hello $client_id\r\n");
        // 向所有人发送
        Gateway::sendToAll("$client_id login\r\n");
    }

    public static function onWebSocketConnect($client_id, $data)
    {

    }

    public function onMessage($client_id, $message)
    {
        // 向所有人发送
        Gateway::sendToAll("$client_id said $message\r\n");
    }

    public static function onClose($client_id)
    {
        // 向所有人发送
        GateWay::sendToAll("$client_id logout\r\n");
    }
}
