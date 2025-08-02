<?php

namespace Database\Factories;

use App\Models\Refund;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class RefundFactory extends Factory
{
    protected $model = Refund::class;

    public function definition(): array
    {
        $stripeAllowedReasons = ['duplicate', 'fraudulent', 'requested_by_customer'];
        $allPossibleStatuses = ['pending', 'approved', 'rejected', 'succeeded', 'failed'];

        return [
            'payment_id' => Payment::factory()->succeeded()->create()->id,
            'stripe_refund_id' => 're_' . Str::random(24),
            'refunded_amount' => 0,
            'reason' => $this->faker->randomElement($stripeAllowedReasons),
            'refund_image' => null,
            'refunded_at' => null,
            'status' => $this->faker->randomElement($allPossibleStatuses),
            'admin_notes' => $this->faker->boolean(30) ? $this->faker->sentence() : null,
        ];
    }

    public function pending(): Factory
    {
        return $this->state(function (array $attributes) {
            $payment = Payment::find($attributes['payment_id'] ?? null);
            if (!$payment) {
                $payment = Payment::factory()->succeeded()->create();
                $attributes['payment_id'] = $payment->id;
            }
            return [
                'status' => 'pending',
                'stripe_refund_id' => null,
                'refunded_amount' => $payment->amount_paid,
                'refunded_at' => null,
                'reason' => 'requested_by_customer', // Tetap ini untuk user request
                'admin_notes' => 'Permintaan refund diajukan oleh pengguna.',
            ];
        });
    }

    public function approved(): Factory
    {
        return $this->state(function (array $attributes) {
            $payment = Payment::find($attributes['payment_id'] ?? null) ?: Payment::factory()->succeeded()->create();
            return [
                'status' => 'approved',
                'stripe_refund_id' => null,
                'refunded_amount' => $payment->amount_paid,
                'refunded_at' => null,
                'reason' => 'requested_by_customer', // Tetap ini
                'admin_notes' => $this->faker->sentence() . ' (Approved by admin)',
            ];
        });
    }

    public function rejected(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'refunded_at' => null,
            'refunded_amount' => 0,
            'stripe_refund_id' => null,
            'reason' => 'fraudulent', // Tetap ini
            'admin_notes' => $this->faker->sentence() . ' (Rejected by admin)',
        ]);
    }

    public function succeeded(): Factory
    {
        return $this->state(function (array $attributes) {
            $payment = Payment::find($attributes['payment_id'] ?? null) ?: Payment::factory()->succeeded()->create();
            return [
                'status' => 'succeeded',
                'refunded_at' => $this->faker->dateTimeThisYear(),
                'refunded_amount' => $payment->amount_paid,
                'stripe_refund_id' => 're_' . Str::random(24),
                'reason' => 'requested_by_customer', // Tetap ini
                'admin_notes' => $this->faker->boolean(50) ? $this->faker->sentence() : null,
            ];
        });
    }

    public function failed(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
            'refunded_at' => null,
            'refunded_amount' => $this->faker->randomFloat(2, 100, 100000),
            'stripe_refund_id' => 're_' . Str::random(24),
            'reason' => 'duplicate', // Tetap ini
            'admin_notes' => $this->faker->boolean(70) ? $this->faker->sentence() : null,
        ]);
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Refund $refund) {
            $payment = $refund->payment;
            $order = $payment->order;

            if (!$payment || !$order) {
                Log::error('Payment or Order not found for refund after creation.', ['refund_id' => $refund->id]);
                return;
            }

            switch ($refund->status) {
                case 'pending':
                    $payment->status = 'refund_pending';
                    $order->status = 'Refund Pending';
                    break;
                case 'approved':
                    // Factory membuat status 'approved', tapi proses Stripe belum terjadi.
                    // Status payment/order akan diupdate saat AdminRefundController memprosesnya.
                    break;
                case 'rejected':
                    if ($payment->status === 'refund_pending') {
                        $payment->status = 'succeeded';
                        $order->status = 'Delivered';
                    }
                    break;
                case 'succeeded':
                    $payment->status = 'refunded';
                    $order->status = 'Refunded';
                    foreach ($order->orderItems as $orderItem) {
                        if ($orderItem->product_variant_id) {
                            $variant = ProductVariant::find($orderItem->product_variant_id);
                            if ($variant) $variant->increment('stock', $orderItem->quantity);
                        } else {
                            $product = Product::find($orderItem->product_id);
                            if ($product) $product->increment('stock', $orderItem->quantity);
                        }
                    }
                    break;
                case 'failed':
                    if ($payment->status === 'refund_pending') {
                        $payment->status = 'succeeded';
                        $order->status = 'Delivered';
                    }
                    break;
            }
            $payment->save();
            $order->save();
        });
    }
}