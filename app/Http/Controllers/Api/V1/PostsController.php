<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PostsController extends Controller
{
    //GET /api/v1/posts
    public function index()
    {
        $posts = Post::with(['user:id,username'])->withCount(['comments','likes'])->orderByDesc('id')->paginate(20);

        return response()->json($posts);
    }

    //POST /api/v1/posts
    public function store(Request $request)
    {
        //※後でFirebase認証に書き換え予定。今はuser_idを受け取る。
        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'content' => ['required', 'string', 'grapheme_max:120'],
        ]);

        $post = Post::create($data);

        $post->load(['user:id,username'])->loadCount(['comments','likes']);

        return response()->json($post,201);
    }

    //DELETE /api/v1/posts/{post}
    public function destroy(Post $post)
    {
        $post->delete();
        return response()->json(['deleted' => true]);
    }

    //GET /api/v1/posts/{post}
    public function show(Post $post)
    {
        //必要な関連と件数を読込
        $post->load(['user:id,username'])
        ->loadCount(['comments', 'likes']);

        return response()->json($post);
    }

    public function update(Request $request, Post $post)
    {
        //TODO:認可(本人のみ編集)は後でPolicyで実装。今は省略
        $data = $request->validate([
            'content' => ['required', 'string', 'grapheme_max:120'],
        ]);

        $post->update(['content' => trim($data['content'])]);

        //一覧と同じ整合性のある返却
        $post->load(['user:id,username'])->loadCount(['comments','likes']);

        return response()->json($post);
    }
}
