<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Server_config extends Model
{
        protected $fillable = [
        'server_name',
        'max_players',
        'gamemode',
        'stream-distance',
        "announce",
        "cSharp",
        "port",
        "voice-chat",
        "voice-chat-sample-rate",
        "bind"
    ];
}
