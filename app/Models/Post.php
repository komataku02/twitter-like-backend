<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

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
}

