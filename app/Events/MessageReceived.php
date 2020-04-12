<?php

namespace App\Events;

use App\Listeners\MessageListener;
use App\Message;
use Carbon\Carbon;
use Hhxsv5\LaravelS\Swoole\Task\Event;

class MessageReceived extends Event
{
    protected $listeners = [
        MessageListener::class,
    ];

    private $message;

    private $userId;

    public function __construct($message, $userId = 0)
    {
        $this->message = $message;
        $this->userId = $userId;
    }

    public function getData()
    {
        $message = new Message();
        $message->room_id = $this->message->room_id;
        $message->msg = $this->message->type == 'text' ? $this->message->content : "";
        $message->img = $this->message->type == 'image' ? $this->message->image : "";
        $message->user_id = $this->userId;
        $message->created_at = Carbon::now();

        return $message;
     }

}
