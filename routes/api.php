<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HealthCheckController;
use App\Http\Controllers\Api\V1\PostsController;
use App\Http\Controllers\Api\V1\CommentsController;
use App\Http\Controllers\Api\V1\LikesController;

// /api は RouteServiceProvider 側で自動付与される想定
Route::prefix('v1')->group(function () {
    // Health
    Route::get('/health', [HealthCheckController::class, 'index']);

    // Posts
    Route::get('/posts', [PostsController::class, 'index']);
    Route::post('/posts', [PostsController::class, 'store']);
    Route::delete('/posts/{post}', [PostsController::class, 'destroy']);
    Route::get('/posts/{post}', [PostsController::class, 'show'])->whereNumber('post');

    // Comments
    Route::get('/posts/{post}/comments', [CommentsController::class, 'index'])->whereNumber('post');
    Route::post('/posts/{post}/comments', [CommentsController::class, 'store'])->whereNumber('post');

    // Likes (toggle)
    Route::post('/posts/{post}/likes/toggle', [LikesController::class, 'toggle'])->whereNumber('post');
});
