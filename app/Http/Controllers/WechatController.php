<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WechatController extends Controller
{
    public function index()
    {
        return view('wechat.index');
    }

    public function ws()
    {
        return view('ws');
    }
}
