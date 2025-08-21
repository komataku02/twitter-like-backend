<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentsController extends Controller
{
    //GET /api/v1/posts/{post}/comments
    public function index(Post $post)
    {
        $comments = Comment::with(['user:id,username'])->where('post_id',$post->id)->orderBy('id')->paginate(50);

        return response()->json($comments);
    }

    //POST /api/v1/posts/{post}/comments
    public function store(Request $request,Post $post)
    {
        $data = $request->validate([
            'user_id' => ['requires','integer','exists:users,id'],
            'content' => ['required','string','max:120'],
        ]);

        $data['post_id'] = $post->id;

        $comment = Comment::create($data);

        return response()->json($comment->load('user:id,username'),201);
    }
}
