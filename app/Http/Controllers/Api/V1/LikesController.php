<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Like;
use Illuminate\Http\Request;

class LikesController extends Controller
{
    // POST /api/v1/posts/{post}/likes/toggle
    public function toggle(Request $request, Post $post)
    {
        /** @var \App\Models\User|null $auth */
        $auth = $request->attributes->get('auth_user'); // Firebaseミドルウェアでセット済み
        if (!$auth) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // (user_id, post_id) の一意制約がある前提
        $like = Like::where('post_id', $post->id)->where('user_id', $auth->id)->first();

        if ($like) {
            $like->delete();
            $status = 'unliked';
        } else {
            Like::create([
                'post_id' => $post->id,
                'user_id' => $auth->id,
            ]);
            $status = 'liked';
        }

        $count = $post->likes()->count();

        return response()->json([
            'status'       => $status,
            'likes_count'  => $count,
            'post_id'      => $post->id,
        ]);
    }
}
