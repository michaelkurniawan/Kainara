<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'address_id',
        'status',
        'shipping_cost',
        'subtotal',
        'is_completed',
        'completed_at',
        'auto_complete_at',
        'original_user_name',
        'original_user_email',
        'shipping_label',
        'shipping_recipient_name',
        'shipping_phone',
        'shipping_address',
        'shipping_country',
        'shipping_city',
        'shipping_province',
        'shipping_postal_code',
        'payment_method',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'auto_complete_at' => 'datetime',
        'is_completed' => 'boolean',
    ];

    /**
     * Get the user that owns the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the user address associated with the order.
     */
    public function address(): BelongsTo
    {
        return $this->belongsTo(UserAddress::class, 'address_id');
    }

    /**
     * Get the order items for the order.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the payment for the order.
     */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Get the delivery for the order.
     */
    public function delivery(): HasOne
    {
        return $this->hasOne(Delivery::class);
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    public function getGrandTotalAttribute(): float
    {
        return (float) ($this->subtotal ?? 0) + (float) ($this->shipping_cost ?? 0);
    }

    public function hasReview(): bool
    {
        return $this->reviews()->exists();
    }

    public function isRefundable(): bool
    {
        return $this->status === 'Delivered' &&
               $this->payment()->where('status', 'succeeded')->exists();
    }
}