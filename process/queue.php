<?php

$process = new \Swoole\Process(function (\Swoole\Process $worker) {
    // 子进程逻辑
    // 从消息队列读取数据
    $cmd = $worker->pop();
    echo "Message from master process: " . $cmd . "\n";
    ob_start();
    // 执行外部程序并显示未经处理的原始输出，会直接打印输出
    passthru($cmd);
    $ret = ob_get_clean() ?: " ";
    $ret = trim($ret). "\n worker pid: ". $worker->pid. "\n";
    // 将数据写入管道
    $worker->push($ret);
    // 退出子进程
    $worker->exit(0);
}, false, false);

/**
 * 调用 useQueue 表示使用消息队列进行进程间通信
 *  消息队列与管道通信不能共存
 *  第一个参数表示消息队列里的key, 第二个表示通信模式, 2表示争抢模式
 *  消息队列不支持事件循环，因此引入了 \Swoole\Process::IPC_NOWAIT 表示以非阻塞模式进行通信
 */

$process->useQueue(1, 2|\Swoole\Process::IPC_NOWAIT);
// 从主进程将命令推送到消息队列
$process->push('php --version');
// 从消息队列取回返回的消息
$msg = $process->pop();
echo "Message from worker process:\n" . $msg;

// 启动子进程
$process->start();
