<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Post;

class CommentController extends Controller
{
    public function store(Request $request, Post $post)
    {
        if (!auth()->check()) {
            return response()->json(['redirect' => route('register')], 401);
        }
        $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id',
        ]);
        $comment = new Comment();
        $comment->content = $request->content;
        $comment->user_id = auth()->id();
        $comment->post_id = $post->id;
        if ($request->filled('parent_id')) {
            $comment->parent_id = $request->parent_id;
        }
        $comment->save();
        $comment->load('user:id,username,avatar');
        return response()->json([
            'id' => $comment->id,
            'content' => $comment->content,
            'username' => $comment->user->username ?? 'Unknown',
            'user_image' => $comment->user->avatar ?? null,
            'created_at' => $comment->created_at,
            'parent_id' => $comment->parent_id,
        ]);
    }
}
