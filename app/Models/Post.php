<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};
use Illuminate\Support\Facades\Storage;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'content'
    ];

    //投稿者
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    //コメント一覧
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    //いいね一覧
    public function likes():HasMany
    {
        return $this->hasMany(Like::class);
    }

    // ★ 画像リレーション（order順）
    public function images(): HasMany
    {
        return $this->hasMany(PostImage::class)->orderBy('order');
    }

    // ★ Post削除前に紐づく物理ファイルを削除
    protected static function booted(): void
    {
        static::deleting(function (Post $post) {
            // 画像テーブルからパスだけ取得して削除（N+1回避）
            $paths = $post->images()->pluck('path')->all();
            foreach ($paths as $p) {
                if ($p) {
                    Storage::disk('public')->delete($p);
                }
            }
        });
    }
}

