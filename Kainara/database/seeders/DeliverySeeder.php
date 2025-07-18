<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Delivery;
use App\Models\Order;

class DeliverySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ordersWithoutDelivery = Order::whereNotIn('status', ['Canceled', 'Returned', 'Refunded', 'Awaiting Payment'])->doesntHave('delivery')->get();

        foreach ($ordersWithoutDelivery as $order) {
            $delivery = Delivery::factory()->create(['order_id' => $order->id]);

            if ($delivery->delivered_at && $order->status !== 'Delivered') {
                $order->status = 'Delivered';
            } elseif ($delivery->shipped_at) {
                $order->status = 'Shipped';
            } $order->save();
        }
    }
}