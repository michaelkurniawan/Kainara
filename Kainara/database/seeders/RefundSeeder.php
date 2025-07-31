<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Refund;
use App\Models\Payment;
use App\Models\Order; // Order model is not directly used for seeding refunds, but good to have if needed for context

class RefundSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Daftar alasan yang diizinkan oleh Stripe
        $stripeAllowedReasons = ['duplicate', 'fraudulent', 'requested_by_customer'];

        // Get payments that are succeeded or partially refunded and are not fully refunded yet
        $succeededPayments = Payment::where(function($query) {
                                $query->where('status', 'succeeded')
                                      ->orWhere('status', 'partially_refunded');
                            })
                            // Eager load refunds to correctly calculate availableForRefund
                            ->with('refunds')
                            ->get();

        foreach ($succeededPayments as $payment) {
            $totalRefundedAmount = $payment->refunds->where('status', 'succeeded')->sum('refunded_amount');
            $availableForRefund = $payment->amount_paid - $totalRefundedAmount;

            // Only attempt to create a refund if there's a significant amount left to refund
            if ($availableForRefund > 0.01) {
                // 50% chance to create a refund for this payment
                if (fake()->boolean(50)) {
                    $refundAmount = 0;
                    $status = 'succeeded';

                    // 70% chance to attempt a full refund, otherwise a partial refund
                    if (fake()->boolean(70) && $availableForRefund > 0.01) {
                        $refundAmount = $availableForRefund;
                    } else {
                        // Ensure partial refund is less than or equal to available amount
                        $refundAmount = fake()->randomFloat(2, $availableForRefund * 0.1, $availableForRefund);
                    }

                    // Prevent creating a refund with zero or negative amount
                    if ($refundAmount <= 0) {
                        continue;
                    }

                    // 10% chance for the refund to be pending or failed
                    if (fake()->boolean(10)) {
                        $status = fake()->randomElement(['pending', 'failed']);
                        // For pending/failed, the amount could still be a part of the original payment,
                        // or a random amount up to the available for refund. Let's keep it within bounds.
                        $refundAmount = fake()->randomFloat(2, $availableForRefund * 0.1, $availableForRefund);
                    }

                    // Create the refund and let the RefundFactory's `afterCreating` callback update the payment status
                    Refund::factory()->create([
                        'payment_id' => $payment->id,
                        'refunded_amount' => $refundAmount,
                        'status' => $status,
                        'refunded_at' => ($status === 'succeeded') ? now() : null,
                        'reason' => fake()->randomElement($stripeAllowedReasons),
                    ]);
                }
            }
        }
    }
}