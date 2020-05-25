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
//        Gateway::sendToClient($client_id, "Hello $client_id\r\n");
        Gateway::sendToClient($client_id, json_encode([
            'type' => 'init',
            'client_id' => $client_id
        ]));
    }

    public static function onWebSocketConnect($client_id, $data)
    {

    }

    public static function onMessage($client_id, $message)
    {
        $messageData = json_decode($message, true);
        if(!$messageData) {
            return;
        }

        switch ($messageData['type']) {
            case 'bind':
                Gateway::bindUid($client_id, $messageData['fromid']);
                return;
            case 'ping':
                echo '正常检测';
                return;
            case 'text':
                $text = nl2br(htmlspecialchars($messageData['data']));
                $fromid = $messageData['fromid'];
                $toid = $messageData['toid'];
                $data = [
                    'type' => 'text',
                    'data' => $text,
                    'fromid' => $fromid,
                    'toid' => $toid,
                    'time' => date('Y-m-d H:i:s')
                ];
                // 判度是否在线
                if (Gateway::isUidOnline($toid)) {
                    Gateway::sendToUid($toid, json_encode($data));
                    $data['isread'] = 1;
                } else {
                    $data['isread'] = 0;
                }
                $data['type'] = 'save';
                Gateway::sendToUid($fromid, json_encode($data));
                return;
        }

    }

    public static function onClose($client_id)
    {
        // 向所有人发送
//        GateWay::sendToAll("$client_id logout\r\n");
        echo "$client_id logout\r\n";
    }
}
