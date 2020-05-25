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


    public function message(Request $request)
    {
        if ($request->ajax()) {
            $data = $request->all();
            $count = ChatMessage::where('fromid', $data['fromid'])
                ->where('toid', $data['toid'])
                ->orWhere(function ($query) use($data) {
                    $query->where('fromid', $data['toid'])
                        ->where('toid', $data['fromid']);
                })
                ->count();
            // 记录多余10条取最后10条，否则全部取出来
            if ($count > 10) {
                $message = ChatMessage::where('fromid', $data['fromid'])
                    ->where('toid', $data['toid'])
                    ->orWhere(function ($query) use($data) {
                        $query->where('fromid', $data['toid'])
                            ->where('toid', $data['fromid']);
                    })
                    ->offset($count-10)
                    ->limit(10)
                    ->orderBy('id')
                    ->get();
            } else {
                $message = ChatMessage::where('fromid', $data['fromid'])
                    ->where('toid', $data['toid'])
                    ->orWhere(function ($query) use($data) {
                        $query->where('fromid', $data['toid'])
                            ->where('toid', $data['fromid']);
                    })
                    ->orderBy('id')
                    ->get();
            }

            return $message;
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
