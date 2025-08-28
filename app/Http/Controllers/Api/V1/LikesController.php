<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;

class LikesController extends Controller
{
    //POST /api/v1/posts/{post}/Likes/toggle
    public function toggle(Request $request, Post $post)
    {
        $data = $request->validate([
            'user_id' => ['required','integer','exists:users,id'],
        ]);
        $userId = $data['user_id'];

        $like = $post->likes()->where('user_id', $userId)->first();

        if ($like) {
            $like->delete();
            $status = 'unliked';
        } else {
            $post->likes()->create(['user_id' => $userId]);
            $status = 'liked';
        }

        return response()->json([
            'status'       => $status,
            'likes_count'  => $post->likes()->count(),
        ]);
    }
}
