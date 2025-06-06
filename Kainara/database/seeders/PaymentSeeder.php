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
        $ordersAwaitingPayment = Order::where('status', 'Awaiting Payment')->doesntHave('payment')->get();

        foreach ($ordersAwaitingPayment as $order) {
            $payment = Payment::factory()->create([
                'order_id' => $order->id,
                'amount_paid' => $order->subtotal + $order->shipping_cost,
            ]);

            if ($payment->midtrans_transaction_status === 'settlement') {
                $order->status = 'Order Confirmed';
                $order->save();
            } elseif ($payment->midtrans_transaction_status === 'expire' || $payment->midtrans_transaction_status === 'deny' || $payment->midtrans_transaction_status === 'cancel') {
                $order->status = 'Canceled';
                $order->save();
            }
        }
    }
}
