<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// GatewayWorkerServer
// 点对点聊天界面
Route::get('/ws', 'WechatController@ws');
// 保存文本信息
Route::post('/save', 'WechatController@saveMessage');
// 获取头像和昵称
Route::post('/avatar', 'WechatController@getInfo');
// 获取聊天信息
Route::post('/message', 'WechatController@message');
// 文件上传
Route::post('/file', 'WechatController@file');
// 聊天列表页面
Route::get('/list', 'WechatController@list');
// 获取用户聊天列表
Route::post('/userMessage', 'WechatController@userMessage');
// 修改信息的状态
Route::post('/readMessage', 'WechatController@readMessage');

Route::get('/tt', 'WechatController@index');
