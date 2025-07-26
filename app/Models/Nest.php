<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nest extends Model
{
    protected $fillable = [
        'name', 'description', 'banner', 'profile_image', 'owner_id'
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

    public function moderators()
    {
        return $this->users()->wherePivot('role', 'moderator');
    }
}
