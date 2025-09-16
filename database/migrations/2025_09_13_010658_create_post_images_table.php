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
        Schema::create('post_images', function (Blueprint $table) {
            if (!Schema::hasColumn('post_images', 'post_id')) {
                $table->foreignId('post_id')->constrained('posts')->cascadeOnDelete();
            }
            if (!Schema::hasColumn('post_images', 'path')) {
                $table->string('path'); // storageの相対パスなど
            }
            if (!Schema::hasColumn('post_images', 'order')) {
                $table->unsignedTinyInteger('order')->default(0);
            }
            $table->index('post_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_images');
    }
};
