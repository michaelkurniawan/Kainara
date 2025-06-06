<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
        'address_id', // This references the user_addresses table
        'status',
        'shipping_cost',
        'subtotal',
        'is_completed',
        'completed_at',
        'auto_complete_at',
        // Duplicated User Information
        'original_user_name',
        'original_user_email',
        // Duplicated Shipping Address Information
        'shipping_label',
        'shipping_recipient_name',
        'shipping_phone',
        'shipping_address',
        'shipping_country',
        'shipping_city',
        'shipping_province',
        'shipping_postal_code',
    ];

    /**
     * Get the user that owns the order.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user address associated with the order.
     */
    public function userAddress() // Renamed for clarity to match user_addresses table
    {
        return $this->belongsTo(UserAddress::class, 'address_id'); // Specify foreign key if it's not 'user_address_id'
    }

    /**
     * Get the order items for the order.
     */
    public function orderItems()
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
}