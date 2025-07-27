<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Order; // Pastikan Order model diimport
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PaymentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Payment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $status = $this->faker->randomElement(['succeeded', 'failed', 'pending', 'canceled', 'requires_action']);
        $amountPaid = $this->faker->randomFloat(2, 100000, 5000000); // Contoh antara 100,000 dan 5,000,000 IDR
        $paidAt = ($status === 'succeeded') ? $this->faker->dateTimeThisYear() : null;
        $paymentMethodType = $this->faker->randomElement(['card', 'transfer_bank', 'e_wallet']);

        $cardDetails = null;
        $paymentMethodDetails = null;

        if ($paymentMethodType === 'card') {
            $cardDetails = [
                'last4' => $this->faker->randomNumber(4, true),
                'brand' => $this->faker->creditCardType(),
            ];
            $paymentMethodDetails = [
                'type' => 'card',
                'card' => $cardDetails,
            ];
        } elseif ($paymentMethodType === 'e_wallet') {
            $paymentMethodDetails = [
                'type' => $this->faker->randomElement(['gopay', 'ovo', 'dana']),
                'details' => [
                    'account_number' => $this->faker->phoneNumber(),
                ],
            ];
        }

        return [
            'order_id' => Order::factory(), // Akan membuat Order baru jika tidak di-override
            'stripe_payment_intent_id' => 'pi_' . Str::random(24),
            'status' => $status,
            'amount_paid' => $amountPaid,
            'paid_at' => $paidAt,
            'currency' => 'idr',
            'payment_method_type' => $paymentMethodType,
            'card_details' => $cardDetails,
            'payment_method_details' => $paymentMethodDetails,
        ];
    }

    /**
     * Indicate that the payment succeeded.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function succeeded()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'succeeded',
                'paid_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            ];
        });
    }

    /**
     * Indicate that the payment is pending.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function pending()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'pending',
                'paid_at' => null,
            ];
        });
    }

    /**
     * Indicate that the payment failed or was canceled/expired.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function failedOrCanceled()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => $this->faker->randomElement(['failed', 'canceled']),
                'paid_at' => null,
            ];
        });
    }
}