<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Payment;
use App\Models\Order;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ordersAwaitingPayment = Order::where('status', 'Awaiting Payment')
                                      ->doesntHave('payment')
                                      ->get();

        foreach ($ordersAwaitingPayment as $order) {
            $payment = Payment::factory()->create([
                'order_id' => $order->id,
                'amount_paid' => $order->grand_total, // Pastikan jumlah pembayaran sesuai dengan grand_total order
            ]);

            if ($payment->status === 'succeeded') {
                $order->status = 'Order Confirmed';
                $order->save();
            } elseif (in_array($payment->status, ['failed', 'canceled'])) {
                $order->status = 'Canceled';
                $order->save();
            }
            // Untuk status 'pending' atau 'requires_action', status order akan tetap 'Awaiting Payment'
        }

        // Opsional: Buat beberapa payment sukses tambahan yang tidak terkait dengan order 'Awaiting Payment'
        // Payment::factory()->count(5)->succeeded()->create();
    }
}