<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Payment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'midtrans_transaction_id',
        'midtrans_transaction_status',
        'amount_paid',
        'paid_at',
        'midtrans_payment_type',
        'midtrans_snap_token',
        'midtrans_va_number',
        'midtrans_bank',
        'midtrans_bill_key',
        'midtrans_biller_key',
        'midtrans_qr_code',
        'midtrans_deeplink_url',
        'midtrans_fraud_status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount_paid' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    /**
     * Get the order that the payment belongs to.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * Get the refund associated with the payment.
     */
    public function refund(): HasOne
    {
        return $this->hasOne(Refund::class);
    }
}
