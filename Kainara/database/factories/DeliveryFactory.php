<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Delivery;
use App\Models\Order;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Delivery>
 */
class DeliveryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Delivery::class;

    public function definition(): array
    {
        $order = Order::whereNotIn('status', ['Canceled', 'Returned', 'Refunded', 'Awaiting Payment'])->doesntHave('delivery')->inRandomOrder()->first();

        if (!$order) {
            $order = Order::factory()->create(['status' => 'Order Confirmed']);
        }

        $shippedAt = fake()->dateTimeBetween('-1 month', 'now');
        $deliveredAt = null;

        if (fake()->boolean(70)) {
            $deliveredAt = fake()->dateTimeBetween($shippedAt, 'now');
        }

        return [
            'order_id' => $order->id,
            'courier_name' => fake()->randomElement(['JNE', 'SiCepat', 'J&T Express', 'Tiki', 'Gojek', 'Grab']),
            'tracking_number' => fake()->regexify('[A-Z0-9]{15,20}'),
            'shipped_at' => $shippedAt,
            'delivered_at' => $deliveredAt,
        ];
    }
}
