<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Order;
use App\Models\User;
use App\Models\UserAddress;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

     protected $model = Order::class;

    public function definition(): array
    {
        $user = User::inRandomOrder()->first() ?: User::factory()->create();
        $userAddress = UserAddress::where('user_id', $user->id)->inRandomOrder()->first() ?: UserAddress::factory()->create(['user_id' => $user->id]);

        return [
            'user_id' => $user->id,
            'address_id' => $userAddress->id,
            'status' => fake()->randomElement(['Awaiting Payment', 'Order Confirmed', 'Awaiting Shipment', 'Shipped', 'Delivered', 'Canceled', 'Returned', 'Refunded', 'Completed']),
            'shipping_cost' => fake()->randomFloat(2, 10000, 50000),
            'subtotal' => 0,
            'is_completed' => fake()->boolean(30),
            'auto_complete_at' => null,
            'original_user_name' => $user->first_name . ' ' . $user->last_name,
            'original_user_email' => $user->email,
            'shipping_label' => null,
            'shipping_recipient_name' => $userAddress->recipient_name,
            'shipping_phone' => $userAddress->phone,
            'shipping_address' => $userAddress->address,
            'shipping_country' => $userAddress->country,
            'shipping_city' => $userAddress->city,
            'shipping_province' => $userAddress->province,
            'shipping_postal_code' => $userAddress->postal_code,
        ];
    }

    /**
     * Indicate that the order is confirmed.
     */
    public function confirmed(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'Order Confirmed',
        ]);
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Order $order) {
            if ($order->is_completed) {
                $order->completed_at = now();
                $order->save();
            }
        });
    }
}
