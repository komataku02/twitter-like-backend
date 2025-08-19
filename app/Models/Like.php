<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo};

class Like extends Model
{
    use HasFactory;

    protected $fillable = ['post_id', 'user_id'];

    //対象投稿
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    //いいねしたユーザー
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
