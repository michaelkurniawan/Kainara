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
     * Metode ini membuat contoh pembayaran untuk pesanan yang menunggu pembayaran.
     */
    public function run(): void
    {
        $ordersAwaitingPayment = Order::where('status', 'Awaiting Payment')
                                      ->doesntHave('payment')
                                      ->get();

        foreach ($ordersAwaitingPayment as $order) {
            $payment = Payment::factory()->create([
                'order_id' => $order->id,
                'amount_paid' => $order->grand_total, // Ini akan menggunakan accessor Order::getGrandTotalAttribute()
            ]);

            if ($payment->status === 'succeeded') {
                $order->status = 'Order Confirmed';
                $order->save();
            } elseif (in_array($payment->status, ['failed', 'canceled'])) {
                $order->status = 'Canceled';
                $order->save();
            }
        }

    }
}