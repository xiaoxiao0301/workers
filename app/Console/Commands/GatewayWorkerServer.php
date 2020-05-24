<?php

namespace App\Console\Commands;

use App\GatewayWorker\Events;
use GatewayWorker\BusinessWorker;
use GatewayWorker\Gateway;
use GatewayWorker\Register;
use Illuminate\Console\Command;
use Workerman\Worker;

class GatewayWorkerServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workerman {action} {--d}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start a Workerman server.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        global $argv;

        if (!in_array($action=$this->argument('action'), ['start', 'stop', 'restart'])) {
            $this->error('Error Arguments');
            exit();
        }

        $argv[0] = 'workerman';
        $argv[1] = $action;
        $argv[2] = $this->option('d') ? '-d' : '';

        $this->start();
    }

    private function start()
    {
        $this->startGateWay();
        $this->startBusinessWorker();
        $this->startRegister();
        Worker::runAll();
    }

    private function startGateWay()
    {
        $gateway = new Gateway("websocket://0.0.0.0:9000");
        $gateway->name = 'Gateway'; // 设置Gateway进程的名称，方便status命令中查看统计
        $gateway->count = 1; // 进程的数量
        $gateway->lanIp = '127.0.0.1'; // 内网ip,多服务器分布式部署的时候需要填写真实的内网ip
        $gateway->startPort = 2200; // 监听本机端口的起始端口
        $gateway->pingInterval = 30;
        $gateway->pingNotResponseLimit = 1; // 服务器主动发送心跳
        $gateway->pingData = '{"mode":"heart"}';
        $gateway->registerAddress = '127.0.0.1:12360'; # 注册服务地址

    }

    private function startRegister()
    {
        new Register("text://127.0.0.1:12360");
    }

    private function startBusinessWorker()
    {
        $worker = new BusinessWorker();
        $worker->name = 'BusinessWorker';
        $worker->count = 1;
        $worker->registerAddress = '127.0.0.1:12360';
        $worker->eventHandler = Events::class; // 至少实现onMessage静态方法
    }
}
