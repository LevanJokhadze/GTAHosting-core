<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servers extends Model
{
    protected $fillable = [
    'name',
    'node',
    'server',
    'playerCount',
    'csharpEnabled'
];

}