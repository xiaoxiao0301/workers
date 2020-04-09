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

// laravels.received_request
Route::get('/text', function () {
   var_dump(request()->all());
});

// Laravels 自定义事件
Route::get('/event/test', function () {
    $event = new \App\Events\TestEvent("测试异步事件监听以及处理");
    $success = \Hhxsv5\LaravelS\Swoole\Task\Event::fire($event);
    var_dump($success);
});

// Task任务
Route::get('/task/task', function () {
    $task = new \App\Jobs\TestTask('测试异步任务');
    // 异步投递任务，触发调用任务类的 handle 方法
    $success= \Hhxsv5\LaravelS\Swoole\Task\Task::deliver($task);
    var_dump($success);
});
