<?php

$workerNum = 5;
$pool = new \Swoole\Process\Pool($workerNum);

$pool->on("WorkerStart", function (Swoole\Process\Pool $pool, int $workerId) {
    echo "Worker#{$workerId} is started\n";
    $redis = new Redis();
    $redis->connect('127.0.0.1', 6379);
    $key = "poll_key";
    while (true) {
        $msgs = $redis->brPop($key, 2);
        if ($msgs == null) {
            continue;
        }
        var_dump($msgs);
        echo "Processed by Worker#{$workerId}\n";
    }
});

$pool->on("WorkerStop", function (Swoole\Process\Pool $pool, int $workerId) {
    echo "Worker#({$workerId}) is stopped\n";
});

$pool->start();
