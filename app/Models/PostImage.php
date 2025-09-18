<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class PostImage extends Model
{
    use HasFactory;

    protected $fillable = ['post_id','path','order','width','height'];
    protected $appends = ['url'];

    public function post(): BelongsTo {
        return $this->belongsTo(Post::class);}

    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->path);
    }
}
