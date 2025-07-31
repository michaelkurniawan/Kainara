<?php

namespace Database\Factories;

use App\Models\Refund;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class RefundFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Refund::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        // Daftar alasan yang diizinkan oleh Stripe
        $stripeAllowedReasons = ['duplicate', 'fraudulent', 'requested_by_customer'];

        return [
            // Ensure a succeeded payment exists for the refund
            'payment_id' => Payment::factory()->state(['status' => 'succeeded', 'stripe_charge_id' => 'ch_' . Str::random(24)]),
            'stripe_refund_id' => 're_' . Str::random(24),
            'refunded_amount' => $this->faker->randomFloat(2, 10000, 1000000),
            // Menggunakan alasan yang diizinkan Stripe untuk simulasi API
            'reason' => $this->faker->randomElement($stripeAllowedReasons),
            'refunded_at' => null,
            'status' => $this->faker->randomElement(['succeeded', 'pending', 'failed']),
        ];
    }

    /**
     * Indicate that the refund succeeded.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function succeeded(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'succeeded',
            'refunded_at' => $this->faker->dateTimeThisYear(),
        ]);
    }

    /**
     * Indicate that the refund failed.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function failed(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
            'refunded_at' => null,
        ]);
    }

    /**
     * Indicate that the refund is pending.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function pending(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'refunded_at' => null,
        ]);
    }

    /**
     * Indicate that the refund is a partial refund.
     * This state should be used with a specific refunded_amount.
     *
     * @param float|null $amount
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function partiallyRefunded(?float $amount = null): Factory
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'succeeded',
            'refunded_at' => $this->faker->dateTimeThisYear(),
            'refunded_amount' => $amount,
        ]);
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Refund $refund) {
            $payment = $refund->payment;

            if (!$payment) {
                return;
            }

            // Reload payment and its refunds to get the most current state,
            // including the refund just created.
            $payment->load('refunds');
            $totalSucceededRefundedAmount = $payment->refunds->where('status', 'succeeded')->sum('refunded_amount');
            $hasPendingRefunds = $payment->refunds->where('status', 'pending')->isNotEmpty();

            if ($refund->status === 'succeeded') {
                if (abs($totalSucceededRefundedAmount - $payment->amount_paid) < 0.01) {
                    $payment->status = 'refunded';
                } else {
                    $payment->status = 'partially_refunded';
                }
            } elseif ($refund->status === 'pending') {
                if ($totalSucceededRefundedAmount > 0) {
                    $payment->status = 'partially_refunded'; // Payment is partially refunded with an additional pending refund
                } else {
                    $payment->status = 'refund_pending';
                }
            }
            $payment->save();
        });
    }
}