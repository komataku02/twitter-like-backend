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
        // 一覧（必要に応じてページネーション）
        $comments = $post->comments()
            ->with(['user:id,username'])
            ->orderBy('id', 'desc')
            ->paginate(20);

        return response()->json($comments);
    }

    // POST /api/v1/posts/{post}/comments
    public function store(Request $request, Post $post)
    {
        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            // あなたのプロジェクトではサーバ側も「絵文字1文字を1カウント」で統一中
            // AppServiceProvider で grapheme_max を登録済みなのでそれを使う
            'content' => ['required', 'string', 'grapheme_max:120'],
        ]);

        // ルートモデル経由で post_id を自動連携
        $comment = $post->comments()->create([
            'user_id' => $data['user_id'],
            'content' => $data['content'],
        ]);

        // フロントがそのまま使えるよう最低限 user を同梱
        $comment->load(['user:id,username']);

        return response()->json($comment, 201);
    }

    // DELETE /api/v1/posts/{post}/comments/{comment}
    public function destroy(Post $post, Comment $comment)
    {
        // 簡易認可（本来はPolicyかAuthで判定）
        // 暫定: user_id=1 だけ削除可
        if ((int)$comment->user_id !== 1) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        // 紐付け整合（別ポストのコメント削除防止）
        if ((int)$comment->post_id !== (int)$post->id) {
            return response()->json(['message' => 'Not Found'], 404);
        }
        $comment->delete();
        return response()->json(['deleted' => true]);
    }
}

