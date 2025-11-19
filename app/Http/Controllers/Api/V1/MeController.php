<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Http\Request;

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

        return [
            'id'           => $u->id,
            'name'         => $u->name,
            'username'     => $u->username,
            'email'        => $u->email,
            'firebase_uid' => $u->firebase_uid,
            'bio'          => $u->bio,

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

    // PUT /api/v1/me
    public function update(UpdateProfileRequest $request)
    {
        /** @var \App\Models\User $u */
        $u = $request->attributes->get('auth_user');

        $u->fill($request->validated());
        $u->save();

        return response()->json($this->toProfileArray($u));
    }
}
