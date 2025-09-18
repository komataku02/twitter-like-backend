<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Models\Post;
use App\Models\PostImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;        // ★ これが必須（今回の本命）
use Illuminate\Support\Facades\Storage;

class PostsController extends Controller
{
    // GET /api/v1/posts
    public function index(Request $request)
    {
        return Post::with(['user:id,username,name', 'images'])
            ->latest()
            ->paginate(20);
    }

    // POST /api/v1/posts
    public function store(StorePostRequest $request)
    {
        /** @var \App\Models\User|null $user */
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $validated = $request->validated();

        return DB::transaction(function () use ($user, $request, $validated) {
            $post = new Post();
            $post->user_id = $user->id;
            $post->content = $validated['content'] ?? null;
            $post->save();

            // 画像は単体/配列の両方に耐える
            $files = $request->file('images', []);
            if ($files && !is_array($files)) {
                $files = [$files];
            }

            foreach (array_values($files) as $idx => $file) {
                // storage/app/public/post-images に保存
                $storedPath = Storage::disk('public')->putFile('post-images', $file);

                // 画像サイズ（取得失敗時は null）
                $w = null;
                $h = null;
                try {
                    [$w, $h] = @getimagesize($file->getRealPath()) ?: [null, null];
                } catch (\Throwable $e) {
                    // noop
                }

                PostImage::create([
                    'post_id' => $post->id,
                    'path'    => $storedPath, // 例: post-images/abc.jpg
                    'width'   => $w,
                    'height'  => $h,
                    'order'   => $idx,
                ]);
            }

            $post->load([
                'user:id,username,name',
                'images',
            ])->loadCount(['comments', 'likes']);

            return response()->json($post, 201);
        });
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
        return $post->load(['user:id,username,name', 'images']);
    }

    // PUT /api/v1/posts/{post}
    public function update(Request $request, Post $post)
    {
        $data = $request->validate([
            'content' => ['required', 'string', 'max:120'],
        ]);

        $post->update(['content' => trim($data['content'])]);
        $post->load(['user:id,username'])->loadCount(['comments', 'likes']);

        return response()->json($post);
    }
}
