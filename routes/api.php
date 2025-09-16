<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HealthCheckController;
use App\Http\Controllers\Api\V1\PostsController;
use App\Http\Controllers\Api\V1\CommentsController;
use App\Http\Controllers\Api\V1\LikesController;

Route::prefix('v1')->group(function () {
    // 公開（read-only）
    Route::get('/health', [HealthCheckController::class, 'index']);
    Route::get('/posts', [PostsController::class, 'index']);
    Route::get('/posts/{post}', [PostsController::class, 'show'])->whereNumber('post');
    Route::get('/posts/{post}/comments', [CommentsController::class, 'index'])->whereNumber('post');

    // 認証必須（write系）※ prefix は付けない！同じ /v1 グループ内で middleware だけ付与
    Route::middleware('firebase')->group(function () {
        Route::post('/posts', [PostsController::class, 'store']);
        Route::put('/posts/{post}', [PostsController::class, 'update'])->whereNumber('post');
        Route::delete('/posts/{post}', [PostsController::class, 'destroy'])->whereNumber('post');

        Route::post('/posts/{post}/comments', [CommentsController::class, 'store'])->whereNumber('post');
        Route::delete('/posts/{post}/comments/{comment}', [CommentsController::class, 'destroy'])
            ->whereNumber('post')->whereNumber('comment');

        Route::post('/posts/{post}/likes/toggle', [LikesController::class, 'toggle'])->whereNumber('post');
    });
});

// 動作確認用
Route::get('/ping', fn() => ['ok' => true]);

// 認証確認用
Route::middleware('firebase')->group(function () {
    Route::get('/me', function (Request $req) {
        /** @var \App\Models\User $u */
        $u = $req->attributes->get('auth_user');
        return [
            'id' => $u->id,
            'name' => $u->name,
            'username' => $u->username,
            'email' => $u->email,
            'firebase_uid' => $u->firebase_uid,
        ];
    });
});
