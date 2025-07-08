<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Server_config extends Model
{
        protected $table = 'server_config'; 
        protected $fillable = [
        'server_name',
        'max_players',
        'gamemode',
        'stream_distance',
        "announce",
        "cSharp",
        "port",
        "voice_chat",
        "voice_chat_sample_rate",
        "bind"
    ];
}
