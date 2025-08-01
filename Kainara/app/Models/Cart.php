<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Factories\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cart extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

     protected $fillable = [
         'user_id',
     ];

     public function user(): BelongsTo
     {
        return $this->belongsTo(User::class, 'user_id');
     }

     public function cartItems(): HasMany
     {
        return $this->hasMany(CartItem::class);
     }
}
