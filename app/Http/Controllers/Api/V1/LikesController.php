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

        $like = Like::where('post_id',$post->id)->where('User_id',$data['user_id'])->first();

        if ($like) {
            $like->delete();
            $status = 'unliked';
        } else {
            Like::create([
                'post_id' => $post->id, 'user_id' => $data['user_id'],
            ]);
            $status = 'liked';
        }

        $likesCount = Like::where('post_id', $post->id)->count();

        return response()->json([
            'status' => $status,
            'likes_count' => $likesCount,
        ]);
    }
}
