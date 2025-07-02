<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Servers;
class UserServerStatus extends Model
{
    protected $fillable = [
        'user_id',
        'server_id',
        'server_name',
        'is_active',
    ];
protected $casts = [
        'is_active' => 'boolean',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function server()
    {
        return $this->belongsTo(Servers::class);
    }
}
