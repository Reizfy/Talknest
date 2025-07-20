<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'user_id', 'nest_id', 'title', 'content', 'media'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function nest()
    {
        return $this->belongsTo(Nest::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function votes()
    {
        return $this->morphMany(Vote::class, 'votable');
    }
}
