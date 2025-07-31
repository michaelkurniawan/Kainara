<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_variant_id',
        'quantity',
        'price',
        'product_name',
        'product_image',
        'variant_size',
        'variant_color',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function refundItems(): HasMany 
    {
        return $this->hasMany(RefundItem::class);
    }

    public function getTotalRefundedQuantityAttribute(): int
    {
        return $this->refundItems()->whereHas('refund', function ($query) {
            $query->where('status', 'succeeded');
        })->sum('quantity_refunded');
    }

    public function getRefundableQuantityAttribute(): int
    {
        return $this->quantity - $this->total_refunded_quantity;
    }
}