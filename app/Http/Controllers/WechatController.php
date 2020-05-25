<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;

class WechatController extends Controller
{
    public function index(Request $request)
    {
        return view('wechat.index');
    }

    public function ws(Request $request)
    {
        $fromid = $request->fromid;
        $toid = $request->toid;
        return view('ws', compact("fromid", "toid"));
    }

    /**
     * 文本信息持久化
     *
     * @param Request $request
     */
    public function saveMessage(Request $request)
    {
        if ($request->ajax()) {
            $data = $request->all();
            $message = [
                'fromid' => $data['fromid'],
                'fromname' => $this->getUserName($data['fromid']),
                'toid' => $data['toid'],
                'toname' => $this->getUserName($data['toid']),
                'content' => $data['data'],
                'isread' => $data['isread'],
                'type' => 1,
            ];

            ChatMessage::create($message);
        }
    }

    /**
     * 获取头像
     *
     * @param Request $request
     */
    public function getInfo(Request $request)
    {
        if($request->ajax()) {
            $data = $request->all();
            $fromUserInfo = User::find($data['fromid']);
            $toUserInfo = User::find($data['toid']);
            return [
                'from_head' => $fromUserInfo['avatar'],
                'to_head' => $toUserInfo['avatar'],
                'from_name' => $fromUserInfo['name'],
                'to_name' => $toUserInfo['name']
            ];
        }
    }

    /**
     * 根据uid返回用户信息
     *
     * @param int $userid
     * @return mixed
     */
    private function getUserName(int $userid)
    {
        $name = User::find($userid);
        return $name->name;
    }
}
