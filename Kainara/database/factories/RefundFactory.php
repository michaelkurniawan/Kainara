<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Refund;
use App\Models\Payment;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Refund>
 */
class RefundFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

     protected $model = Refund::class;

    public function definition(): array
    {

        $payment = Payment::where('midtrans_transaction_status', 'settlement')->doesntHave('refund')->inRandomOrder()->first();

        if (!$payment) {
            $payment = Payment::factory()->create(['midtrans_transaction_status' => 'settlement']);
        }

        $refundedAmount = fake()->randomFloat(2, 10000, $payment->amount_paid);
        if ($refundedAmount > $payment->amount_paid) {
            $refundedAmount = $payment->amount_paid;
        }

        return [
            'payment_id' => $payment->id,
            'refunded_amount' => $refundedAmount,
            'reason' => fake()->randomElement(['Product returned by customer', 'Item out of stock', 'Customer request', 'Technical issue', null]),
            'refunded_at' => fake()->dateTimeBetween($payment->paid_at ?? '-1 week', 'now'),
            'refund_reference' => 'REF-' . Str::upper(Str::random(10)),
        ];
    }
}
