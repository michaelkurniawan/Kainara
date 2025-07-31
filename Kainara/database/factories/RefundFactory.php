<?php

namespace Database\Factories;

use App\Models\Refund;
use App\Models\Payment; // Pastikan Payment model diimport
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class RefundFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Refund::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'payment_id' => Payment::factory()->succeeded(), // Akan membuat Payment sukses baru jika tidak di-override
            'stripe_refund_id' => 're_' . Str::random(24),
            'refunded_amount' => $this->faker->randomFloat(2, 10000, 1000000), // Contoh antara 10,000 dan 1,000,000 IDR
            'reason' => $this->faker->randomElement(['duplicate', 'fraudulent', 'requested_by_customer', 'expired_uncaptured_charge']),
            'refunded_at' => $this->faker->dateTimeThisYear(),
            'status' => $this->faker->randomElement(['succeeded', 'pending', 'failed']),
        ];
    }

    /**
     * Indicate that the refund succeeded.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function succeeded()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'succeeded',
                'refunded_at' => $this->faker->dateTimeThisYear(),
            ];
        });
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterMaking(function (Refund $refund) {
            //
        })->afterCreating(function (Refund $refund) {
            // Setelah refund dibuat, update status order terkait
            $payment = $refund->payment;
            if ($payment && $payment->order) {
                if ($refund->status === 'succeeded' && $payment->order->status !== 'Refunded') {
                    $payment->order->status = 'Refunded';
                    $payment->order->save();
                }
            }
        });
    }
}