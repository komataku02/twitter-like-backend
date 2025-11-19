<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentsController extends Controller
{
    // GET /api/v1/posts/{post}/comments
    public function index(Post $post)
    {
        $comments = $post->comments()
            ->with(['user:id,username'])
            ->orderBy('id', 'desc')
            ->paginate(20);

        return response()->json($comments);
    }

    // POST /api/v1/posts/{post}/comments
    public function store(Request $request, Post $post)
    {
        /** @var \App\Models\User $u */
        $u = $request->attributes->get('auth_user');

        $data = $request->validate([
            'content' => ['required', 'string', 'grapheme_max:120'],
        ]);

        $comment = $post->comments()->create([
            'user_id' => $u->id,
            'content' => $data['content'],
        ]);

        $comment->load(['user:id,username']);

        return response()->json($comment, 201);
    }

    // PUT /api/v1/posts/{post}/comments/{comment}
    public function update(Request $request, Post $post, Comment $comment)
    {
        /** @var \App\Models\User $u */
        $u = $request->attributes->get('auth_user');

        // 別ポストのコメントは編集させない
        if ((int)$comment->post_id !== (int)$post->id) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        // 自分のコメント以外は編集禁止
        if ((int)$comment->user_id !== (int)$u->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data = $request->validate([
            'content' => ['required', 'string', 'grapheme_max:120'],
        ]);

        $comment->content = $data['content'];
        $comment->save();

        $comment->load(['user:id,username']);

        return response()->json($comment);
    }

    // DELETE /api/v1/posts/{post}/comments/{comment}
    public function destroy(Request $request, Post $post, Comment $comment)
    {
        /** @var \App\Models\User $u */
        $u = $request->attributes->get('auth_user');

        if ((int)$comment->user_id !== (int)$u->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ((int)$comment->post_id !== (int)$post->id) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        $comment->delete();

        return response()->json(['deleted' => true]);
    }
}
