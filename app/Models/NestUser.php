<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NestUser extends Model
{
    protected $table = 'nest_user';
    protected $fillable = [
        'nest_id', 'user_id', 'role'
    ];

    public function nest()
    {
        return $this->belongsTo(Nest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
