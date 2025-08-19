<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete(); // 投稿削除時にコメントも削除
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // ユーザー削除時にコメントも削除
            $table->string('content', 120); // コメント本文（120文字以内）
            $table->timestamps();

            $table->index(['post_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
