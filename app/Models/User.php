<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'username',
        'firebase_uid',
        'bio',
        'avatar_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    //投稿
    public function posts():HasMany
    {
        return $this->hasMany(Post::class);
    }

    //コメント
    public function comments():HasMany
    {
        return $this->hasMany(Comment::class);
    }

    //いいね
    public function likes():HasMany
    {
        return $this->hasMany(Like::class);
    }

    // このユーザーの投稿が獲得したいいね（Post 経由で Like を参照）
    public function receivedLikes(): HasManyThrough
    {
        return $this->hasManyThrough(Like::class, Post::class);
    }
}
