<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostImage;
use Illuminate\Http\Request;
use App\Http\Requests\StorePostRequest;
use Illuminate\Support\Facades\Storage;

class PostsController extends Controller
{
    // GET /api/v1/posts
    public function index(Request $request)
    {
        // ※ with() は「user_id」ではなくリレーション名「user」を使う
        return Post::with(['user:id,username,name', 'images'])
            ->latest()
            ->paginate(20);
    }

    // POST /api/v1/posts
    public function store(StorePostRequest $request)
    {
        /** @var \App\Models\User|null $auth */
        $auth = $request->attributes->get('auth_user');

        // 認証必須：auth_user が無い時に 401 を返す（← 逆になっていた）
        if (!$auth) {
            return response()->json(['message' => '認証されていません'], 401);
        }

        // StorePostRequest で検証済み（body を受けて DB の content に入れる）
        $text = $request->validated()['body'];

        $post = Post::create([
            'user_id' => $auth->id,
            'content' => $text,
        ]);

        $post->loadMissing(['user:id,username,name'])->loadCount(['comments', 'likes']);

        return response()->json($post, 201);
    }

    // DELETE /api/v1/posts/{post}
    public function destroy(Post $post)
    {
        $post->delete();
        return response()->json(['deleted' => true]);
    }

    // GET /api/v1/posts/{post}
    public function show(Post $post)
    {
        // ここも「user_id」ではなく「user」
        return $post->load(['user:id,username,name', 'images']);
    }

    // PUT /api/v1/posts/{post}
    public function update(Request $request, Post $post)
    {
        // 「grapheme_max」は未定義なら 500 になるので通常の max を使用
        $data = $request->validate([
            'content' => ['required', 'string', 'max:120'],
        ]);

        $post->update(['content' => trim($data['content'])]);
        $post->load(['user:id,username'])->loadCount(['comments', 'likes']);

        return response()->json($post);
    }
}
