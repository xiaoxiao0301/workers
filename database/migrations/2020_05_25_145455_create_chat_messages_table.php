<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fromid')->comment('发送者id');
            $table->string('fromname')->comment('发送者昵称');
            $table->unsignedBigInteger('toid')->comment('接受者id');
            $table->string('toname')->comment('接受者昵称');
            $table->text('content');
            $table->tinyInteger('isread')->default(0)->comment('是否已读');
            $table->tinyInteger('type')->default(1)->comment('消息类型，1文本 2图片');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chat_messages');
    }
}
