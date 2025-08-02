<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Refund;
use App\Models\Payment;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RefundSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Alasan yang diizinkan oleh Stripe untuk API
        $stripeAllowedReasons = ['duplicate', 'fraudulent', 'requested_by_customer'];
        // Semua status yang mungkin untuk refund (sesuai dengan AdminRefundController)
        $allPossibleAdminStatuses = ['pending', 'approved', 'rejected', 'succeeded', 'failed'];


        // Ambil payment yang sudah sukses dan belum direfund sepenuhnya
        $eligiblePayments = Payment::where('status', 'succeeded')
                                   ->with('refunds')
                                   ->get();

        foreach ($eligiblePayments as $payment) {
            $totalRefundedAmount = $payment->refunds->where('status', 'succeeded')->sum('refunded_amount');
            $availableForRefund = $payment->amount_paid - $totalRefundedAmount;

            if ($availableForRefund > 0.01) {
                // 70% kemungkinan user mengajukan refund (status 'pending')
                if (fake()->boolean(70)) {
                    try {
                        Refund::factory()->pending()->create([
                            'payment_id' => $payment->id,
                            'refunded_amount' => $availableForRefund,
                            'reason' => fake()->randomElement($stripeAllowedReasons), // <--- Menggunakan stripeAllowedReasons
                            'refund_image' => fake()->boolean(40) ? 'public/refund_images/' . Str::uuid() . '.jpg' : null,
                            'admin_notes' => fake()->boolean(10) ? fake()->sentence() : null,
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Seeder failed to create pending refund: ' . $e->getMessage(), ['payment_id' => $payment->id]);
                    }
                }
            }
        }

        // --- Buat skenario refund untuk pengujian admin ---

        // 1. Refund yang sudah disetujui dan berhasil diproses Stripe
        $approvedAndSucceededPayment = Payment::factory()->succeeded()->create();
        try {
            Refund::factory()->succeeded()->create([
                'payment_id' => $approvedAndSucceededPayment->id,
                'refunded_amount' => $approvedAndSucceededPayment->amount_paid,
                'reason' => fake()->randomElement($stripeAllowedReasons), // <--- Menggunakan stripeAllowedReasons
                'refund_image' => 'public/refund_images/' . Str::uuid() . '.jpg',
                'admin_notes' => 'Approved and successfully processed by Stripe.',
            ]);
        } catch (\Exception $e) {
            Log::error('Seeder failed to create succeeded refund: ' . $e->getMessage(), ['payment_id' => $approvedAndSucceededPayment->id]);
        }

        // 2. Refund yang sudah disetujui tapi gagal diproses Stripe
        $approvedAndFailedPayment = Payment::factory()->succeeded()->create();
        try {
            Refund::factory()->failed()->create([
                'payment_id' => $approvedAndFailedPayment->id,
                'refunded_amount' => $approvedAndFailedPayment->amount_paid,
                'reason' => fake()->randomElement($stripeAllowedReasons), // <--- Menggunakan stripeAllowedReasons
                'refund_image' => 'public/refund_images/' . Str::uuid() . '.jpg',
                'admin_notes' => 'Approved but failed to process on Stripe.',
            ]);
        } catch (\Exception $e) {
            Log::error('Seeder failed to create failed refund: ' . $e->getMessage(), ['payment_id' => $approvedAndFailedPayment->id]);
        }

        // 3. Refund yang ditolak oleh admin
        $rejectedPayment1 = Payment::factory()->succeeded()->create();
        try {
            Refund::factory()->rejected()->create([
                'payment_id' => $rejectedPayment1->id,
                'reason' => fake()->randomElement($stripeAllowedReasons), // <--- Menggunakan stripeAllowedReasons
                'admin_notes' => 'Customer did not provide valid proof.',
            ]);
        } catch (\Exception $e) {
            Log::error('Seeder failed to create rejected refund 1: ' . $e->getMessage(), ['payment_id' => $rejectedPayment1->id]);
        }

        $rejectedPayment2 = Payment::factory()->succeeded()->create();
        try {
            Refund::factory()->rejected()->create([
                'payment_id' => $rejectedPayment2->id,
                'reason' => fake()->randomElement($stripeAllowedReasons), // <--- Menggunakan stripeAllowedReasons
                'admin_notes' => 'Flagged as suspicious by fraud detection.',
            ]);
        } catch (\Exception $e) {
            Log::error('Seeder failed to create rejected refund 2: ' . $e->getMessage(), ['payment_id' => $rejectedPayment2->id]);
        }
    }
}