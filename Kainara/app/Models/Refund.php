<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Refund extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'stripe_refund_id',
        'refunded_amount',
        'reason',
        'refund_image',
        'refunded_at',
        'status',
        'admin_notes',
    ];

    protected $casts = [
        'refunded_at' => 'datetime',
    ];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function refundItems(): HasMany
    {
        return $this->hasMany(RefundItem::class);
    }
}