<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

     protected $fillable = [
        'cart_id',
        'product_id',
        'product_variant_id',
        'quantity',
     ];

     public function cart(): BelongsTo
     {
        return $this->belongsTo(Cart::class, 'cart_id');
     }

     public function productVariant(): BelongsTo
     {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
     }

     public function product(): BelongsTo
     {
         return $this->belongsTo(Product::class, 'product_id');
     }
}
