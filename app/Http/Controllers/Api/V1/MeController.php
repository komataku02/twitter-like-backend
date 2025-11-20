<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MeController extends Controller
{
    /**
     * 共通のレスポンス整形
     */
    private function toProfileArray(User $u): array
    {
        $postsCount    = $u->posts()->count();
        $commentsCount = Comment::where('user_id', $u->id)->count();
        $likesCount    = $u->receivedLikes()->count(); // このユーザーの投稿についたいいね

        // public ディスクに保存している前提で URL を生成
        $avatarUrl = $u->avatar_path
            ? Storage::disk('public')->url($u->avatar_path)
            : null;

        return [
            'id'           => $u->id,
            'name'         => $u->name,
            'username'     => $u->username,
            'email'        => $u->email,
            'firebase_uid' => $u->firebase_uid,
            'bio'          => $u->bio,
            'avatar_path'  => $u->avatar_path,
            'avatar_url'   => $avatarUrl,

            'posts_count'    => $postsCount,
            'comments_count' => $commentsCount,
            'likes_count'    => $likesCount,

            'created_at'     => $u->created_at,
        ];
    }

    // GET /api/v1/me
    public function show(Request $request)
    {
        /** @var \App\Models\User $u */
        $u = $request->attributes->get('auth_user');

        return response()->json($this->toProfileArray($u));
    }

    // PUT /api/v1/me （名前・ユーザー名・自己紹介など）
    public function update(UpdateProfileRequest $request)
    {
        /** @var \App\Models\User $u */
        $u = $request->attributes->get('auth_user');

        $u->fill($request->validated());
        $u->save();

        return response()->json($this->toProfileArray($u));
    }

    // POST /api/v1/me/avatar （アイコン画像アップロード）
    public function updateAvatar(Request $request)
    {
        /** @var \App\Models\User $u */
        $u = $request->attributes->get('auth_user');

        // 画像バリデーション（5MBまで / jpg,png,webp,gif）
        $request->validate([
            'avatar' => [
                'required',
                'image',
                'mimes:jpeg,jpg,png,webp,gif',
                'max:5120', // KB 単位 → 約5MB
            ],
        ]);

        // バリデーション後は request->file() から確実に取り出す
        $file = $request->file('avatar');
        if (!$file) {
            return response()->json(['message' => 'ファイルが送信されていません'], 422);
        }

        // 以前のアイコンがあれば削除（あればでOK）
        if ($u->avatar_path) {
            Storage::disk('public')->delete($u->avatar_path);
        }

        // public ディスクに保存（例: avatars/{user_id}/xxxxxx.png）
        $path = $file->store('avatars/'.$u->id, 'public');

        $u->avatar_path = $path;
        $u->save();

        return response()->json($this->toProfileArray($u));
    }
}
