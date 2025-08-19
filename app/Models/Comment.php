<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo};

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'user_id',
        'content'
    ];

    //投稿対象
    public function post():BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    //コメント投稿者
    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
