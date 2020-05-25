<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $fillable = [
        'fromid', 'toid', 'fromname', 'toname', 'content', 'isread', 'type'
    ];
}
