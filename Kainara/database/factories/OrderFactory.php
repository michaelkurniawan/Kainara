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
            'status' => fake()->randomElement(['Awaiting Payment', 'Order Confirmed', 'Awaiting Shipment', 'Shipped', 'Delivered', 'Canceled', 'Returned', 'Refunded', 'Completed']), // Removed 'Completed' from random selection
            'subtotal' => $this->faker->randomFloat(2, 10000, 1000000),
            'shipping_cost' => $this->faker->randomFloat(2, 0, 50000),
            'is_completed' => false, // <<< MODIFIED: Default to false
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
            'is_completed' => false, // Ensure it's false for confirmed status
        ]);
    }

    /**
     * Indicate that the order is completed.
     */
    public function completed(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'Completed',
            'is_completed' => true,
        ]);
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Order $order) {
            // Only set completed_at if is_completed is true (which is handled by the completed() state)
            if ($order->is_completed && is_null($order->completed_at)) {
                $order->completed_at = now();
                $order->save();
            }
        });
    }
}
