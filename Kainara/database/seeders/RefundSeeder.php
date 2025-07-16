<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Refund;
use App\Models\Payment;

class RefundSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paidPaymentsWithoutRefund = Payment::where('midtrans_transaction_status', 'settlement')->doesntHave('refund')->get();

        foreach ($paidPaymentsWithoutRefund as $payment) {
            if (fake()->boolean(30)) {
                $refund = Refund::factory()->create(['payment_id' => $payment->id]);

                $order = $payment->order;
                if ($order && $order->status !== 'Refunded') {
                    $order->status = 'Refunded';
                    $order->save();
                }
            }
        }
    }
}