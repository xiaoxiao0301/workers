<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RankControler extends Controller
{
    private $redis;
    private $leaderboard;

    public function __construct()
    {
        $this->redis = new \Redis();
        $this->redis->connect('127.0.0.1', 6379);
    }


    public function addLeaderboard($leaderboard, $node, $count = 1)
    {
        if ($leaderboard) {
            $this->leaderboard = "leaderboard:".$leaderboard;
        } else {
            $this->leaderboard = "leaderboard:".mt_rand(1000, 9999);
        }

        return $this->redis->zAdd($this->leaderboard,  ['NX'], $count, $node);
    }

    public function getLeaderboader($leaderboard, $number, $asc = true, $score = true)
    {
        $this->leaderboard = 'leaderboard:'.$leaderboard;
        if ($asc) {
            // 按照高分数进行一个排序
            $nowLeaderboard = $this->redis->zRevRange($this->leaderboard, 0, $number-1, $score);
        } else {
            // 按照高分数降序排序
            $nowLeaderboard = $this->redis->zRange($this->leaderboard, 0, $number-1, $score);
        }

        return $nowLeaderboard;
    }
}
