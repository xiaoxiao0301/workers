<?php


$server = new \Swoole\Http\Server('127.0.0.1', 9588);

$server->on('Request', function ($request, $response) {

    $channel = new \Swoole\Coroutine\Channel(3);

    go(function () use ($channel) {
        var_dump(time());

        $mysql = new Swoole\Coroutine\MySQL();
        $mysql->connect([
            'host' => '127.0.0.1',
            'user' => 'root',
            'password' => 'root',
            'database' => 'laravel58',
        ]);
        $result = $mysql->query('select sleep(3)');

        $channel->push($result);
    });

    go(function () use ($channel) {
        var_dump(time());

        $redis1 = new Swoole\Coroutine\Redis();
        $redis1->connect('127.0.0.1', 6379);
        $result = $redis1->set('hello', 'world');

        $channel->push($result);
    });

    go(function () use ($channel) {
        var_dump(time());

        $redis2 = new Swoole\Coroutine\Redis();
        $redis2->connect('127.0.0.1', 6379);

        $result = $redis2->get('hello');
        $channel->push($result);
    });

    $results = [];
    for ($i = 0; $i < 3; $i++) {
        $results[] = $channel->pop();
    }

    $response->end(json_encode([
        'data' => $results,
        'time' => time()
    ]));
});

$server->start();
