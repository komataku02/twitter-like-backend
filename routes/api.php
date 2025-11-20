<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HealthCheckController;
use App\Http\Controllers\Api\V1\PostsController;
use App\Http\Controllers\Api\V1\CommentsController;
use App\Http\Controllers\Api\V1\LikesController;
use App\Http\Controllers\Api\V1\MeController;

Route::prefix('v1')->group(function () {
    // 公開（read-only）
    Route::get('/health', [HealthCheckController::class, 'index']);
    Route::get('/posts', [PostsController::class, 'index']);
    Route::get('/posts/{post}', [PostsController::class, 'show'])->whereNumber('post');
    Route::get('/posts/{post}/comments', [CommentsController::class, 'index'])->whereNumber('post');

    // 認証必須（write系）
    Route::middleware('firebase')->group(function () {
        // posts
        Route::post('/posts', [PostsController::class, 'store']);
        Route::put('/posts/{post}', [PostsController::class, 'update'])->whereNumber('post');
        Route::delete('/posts/{post}', [PostsController::class, 'destroy'])->whereNumber('post');

        // comments
        Route::post('/posts/{post}/comments', [CommentsController::class, 'store'])->whereNumber('post');
        Route::put('/posts/{post}/comments/{comment}', [CommentsController::class, 'update'])
            ->whereNumber('post')->whereNumber('comment');
        Route::delete('/posts/{post}/comments/{comment}', [CommentsController::class, 'destroy'])
            ->whereNumber('post')->whereNumber('comment');

        // likes
        Route::post('/posts/{post}/likes/toggle', [LikesController::class, 'toggle'])->whereNumber('post');

        // me
        Route::get('/me', [MeController::class, 'show']);
        Route::put('/me', [MeController::class, 'update']);
        Route::post('/me/avatar', [MeController::class, 'updateAvatar']);
    });
});

// 動作確認用
Route::get('/ping', fn () => ['ok' => true]);
