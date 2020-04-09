<?php


namespace App\Jobs\Timer;


use Hhxsv5\LaravelS\Swoole\Timer\CronJob;
use Illuminate\Support\Facades\Log;

class TestCronJob extends CronJob
{
    protected $i = 0;

    public function run()
    {
        Log::info(__METHOD__, ['start', $this->i, microtime(true)]);
        $this->i++;
        Log::info(__METHOD__, ['end', $this->i, microtime(true)]);
        if ($this->i == 3) {
            Log::info(__METHOD__, ['stop', $this->i, microtime(true)]);
            // 清除定时器
            $this->stop();
        }
    }

    public function interval()
    {
        // 定时器间隔，单位为 ms
        return 1000;
    }

    // 是否在设置之后立即触发 run 方法执行
    public function isImmediate()
    {
        return false; // // 是否立即执行第一次，false则等待间隔时间后执行第一次
    }
}
