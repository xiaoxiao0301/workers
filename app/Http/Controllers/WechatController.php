<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
     * 获取头像和昵称
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
     * 初始化聊天信息
     *
     * @param Request $request
     * @return mixed
     */
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
     * 文件上传
     *
     * @param Request $request
     */
    public function file(Request $request)
    {
        $fromid = $request->fromid;
        $toid = $request->toid;
        $isread = $request->online;

//        $name = $request->file('file')->getClientOriginalName(); // 获取文件的原始名称
        $extension = $request->file('file')->extension(); // 文件扩展名
        $size = $request->file('file')->getSize();
        $type = ['jpg', 'jpeg', 'gif', 'png'];
        if (!in_array($extension, $type)) {
            return [
                'status' => -1,
                'msg' => '上传类型错误'
            ];
        }
        if ($size / 1024 > 5120) {
            return [
                'status' => -1,
                'msg' => '上传图片过大'
            ];
        }

        $path = Storage::disk('public')->putFile('message_img', $request->file('file'));
        $data['fromid'] = $fromid;
        $data['fromname'] = $this->getUserName($fromid);
        $data['toid'] = $toid;
        $data['toname'] = $this->getUserName($toid);
        $data['content'] = $path;
        $data['isread'] = $isread;
        $data['type'] = 2;
        $message = ChatMessage::create($data);
        if ($message) {
            return [
                'status' => 1,
                'path' => 'storage/'.ltrim($path, '/'),
            ];
        } else {
            return [
                'status' => -1,
                'msg' => '上传失败',
            ];
        }
    }

    /**
     * 根据uid返回用户信息
     *
     * @param string $userid
     * @return mixed
     */
    private function getUserName(string $userid)
    {
        $name = User::find($userid);
        return $name->name;
    }
}
