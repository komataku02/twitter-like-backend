<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostsController extends Controller
{
    //GET /api/v1/posts
    public function index(Request $req)
    {
        return Post::with(['user_id,username,name','images'])->latest()->paginate(20);
    }

    //POST /api/v1/posts
    public function store(StorePostRequest $req)
    {
        $post = Post::create([
            'user_id' => auth()->id(),
            'body' => $req->string('body')->toString() ?: null,
        ]);

        /** @var \illuminate\Http\UploadedFile[] $files */
        $files = $req->file('images', []);
        foreach ($files as $i => $file) {
            $path =$file->store("posts/{$post->id}",'public');
            $imgSize = @getimagesize($file->getRealPath());
            PostImage::create([
                'post_id' => $post->id,
                'path' => $path,
                'order' => $i,
                'width' => $imageSize[0] ?? null,
                'height' => $imageSize[1] ?? null,
                'mime' => $file->getMimeType(),
                'size' => $file->getSize(),
            ]);
        }
        return $post->load(['user:id,username,name','images']);
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
        return $post->load(['user_id,username,name','images']);
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
