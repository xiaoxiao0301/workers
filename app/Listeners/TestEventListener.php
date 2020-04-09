<?php


namespace App\Listeners;

use App\Events\TestEvent;
use Hhxsv5\LaravelS\Swoole\Task\Listener;
use Illuminate\Support\Facades\Log;

class TestEventListener extends Listener
{
    /**
     * @var TestEvent
     */
    protected $event;

    public function handle()
    {
        Log::info(__CLASS__ . ': 开始处理', [$this->event->getData()]);
        sleep(3);// 模拟耗时代码的执行
        Log::info(__CLASS__ . ': 处理完毕');
    }
}
