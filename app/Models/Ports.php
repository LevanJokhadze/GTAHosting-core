<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ports extends Model
{
        protected $fillable = [
        'server_name',
        'server_ip',
        'port'
    ];
}
