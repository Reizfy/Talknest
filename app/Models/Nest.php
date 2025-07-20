<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nest extends Model
{
    protected $fillable = [
        'name', 'title', 'description', 'banner', 'owner_id'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('role')->withTimestamps();
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
