<?php

// 初始化一个容量为 1024 的 Swoole Table
$table = new \Swoole\Table(1024);
// 在 Table 中新增 id 列
$table->column('id', \Swoole\Table::TYPE_INT);
// 在 Table 中新增 name 列，长度为 50
$table->column('name', \Swoole\Table::TYPE_STRING, 10);
// 在 Table 中新泽 score 列
$table->column('score', \Swoole\Table::TYPE_FLOAT);
// 创建这个 Swoole Table
$table->create();


// 设置 Key-Value 值
$table->set('student-1', ['id' => 1, 'name' => '学小君', 'score' => 80]);
$table->set('student-2', ['id' => 2, 'name' => '学院君', 'score' => 90]);

// 如果指定 Key 值存在则打印对应 Value 值
if ($table->exist('student-1')) {
    echo "Student-" . $table->get('student-1', 'id') . ':' . $table->get('student-1', 'name') . ":" .
        $table->get('student-1', 'score') . "\n";
}

// 自增操作
$table->incr('student-2', 'score', 5);
// 自减操作
$table->decr('student-2', 'score', 5);

// 表中总记录数
$count = $table->count();

// 删除指定表记录
$table->del('student-1');
