<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateProfileRequest;

class MeController extends Controller
{
    // GET /api/me
    public function show(Request $request)
    {
        /** @var \App\Models\User $u */
        $u = $request->attributes->get('auth_user');

        //投稿数・コメント数・「獲得いいね数」を集計
        $postsCount = $u->posts()->count();
        $commentsCount = $u->comments()->count();

        //獲得いいね数...このユーザーの投稿についたLIKEの総数
        $likesCount = $u->receivedlikes()->count();
        // もし「自分が押したいいね数」にしたい場合は、代わりに：
        // $likesCount = $u->likes()->count();

        return response()->json([
            'id' => $u->id,
            'name' => $u->name,
            'username' => $u->username,
            'email' => $u->email,
            'firebase_uid' => $u->firebase_uid,

            //プロフィール表示用の統計
            'posts_count' => $postsCount,
            'comments_count' => $commentsCount,
            'likes_count' => $likesCount,

            //フロントで登録日表示を使えるようにcreate_atも返す
            'created_at' => $u->created_at,
        ]);
    }

    //PUT /api/me
    public function update(UpdateProfileRequest $request)
    {
        /** @var \App\Models\User $u */
        $u = $request->attributes->get('auth_user');

        $u->fill($request->validated());
        $u->save();

        return response()->json([
            'id' => $u->id,
            'name' => $u->name,
            'username' => $u->username,
            'email' => $u->email,
        ]);
    }
}
