<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Article extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

     protected $fillable = [
        'title',
        'slug',
        'content',
        'thumbnail',
        'admin_id',
     ];

     public function admin(): BelongsTo
     {
        return $this->belongsTo(User::class, 'admin_id');
     }

     public function getThumbnailUrlAttribute()
    {
        return $this->thumbnail ? Storage::url($this->thumbnail) : null;
    }
}
