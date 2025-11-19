<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // すでにある場合は追加しないようにガードをかける

            if (!Schema::hasColumn('users', 'bio')) {
                $table->text('bio')->nullable()->after('firebase_uid');
            }

            if (!Schema::hasColumn('users', 'avatar_path')) {
                // bio がすでにある場合は after('bio') でOK
                $table->string('avatar_path')->nullable()->after('bio');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // あれば消す（なければ無視される）
            if (Schema::hasColumn('users', 'avatar_path')) {
                $table->dropColumn('avatar_path');
            }
            if (Schema::hasColumn('users', 'bio')) {
                $table->dropColumn('bio');
            }
        });
    }
};
