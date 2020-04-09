<?php

$process = new \Swoole\Process(function (\Swoole\Process $worker) {
    // 子进程逻辑
    //  通过管道从主进程读取数据
    $cmd = $worker->read();
    ob_start();
    // 执行外部程序并显示未经处理的原始输出，会直接打印输出
    passthru($cmd);
    $ret = ob_get_clean() ?: " ";
    $ret = trim($ret). ".worker pid: ". $worker->pid. "\n";
    // 将数据写入管道
    $worker->write($ret);
    // 退出子进程
    $worker->exit(0);
});

// 启动进程
$process->start();
// 从主进程将数据通过管道发送到子进程
$process->write("php --version");
// 打印从子进程返回的数据
$msg = $process->read();
echo "Result of worker:" . $msg;
