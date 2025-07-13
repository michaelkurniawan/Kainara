<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Payment;
use App\Models\Order;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Payment::class;

    public function definition(): array
    {
        $order = Order::where('status', 'Awaiting Payment')->doesntHave('payment')->inRandomOrder()->first();

        if (!$order) {
            $order = Order::factory()->create([
                'status' => 'Awaiting Payment',
                'subtotal' => fake()->randomFloat(2, 100000, 5000000),
                'shipping_cost' => fake()->randomFloat(2, 10000, 50000),
            ]);
        }

        $totalAmount = $order->subtotal + $order->shipping_cost;
        $transactionStatus = fake()->randomElement(['settlement', 'pending', 'expire', 'deny', 'cancel', 'challenge', 'capture']);
        $paidAt = null;
        $paymentType = null;
        $snapToken = null;
        $vaNumber = null;
        $bank = null;
        $qrCode = null;
        $deeplinkUrl = null;
        $fraudStatus = null;

        switch ($transactionStatus) {
            case 'settlement':
            case 'capture':
                $paidAt = fake()->dateTimeBetween('-1 week', 'now');
                $paymentType = fake()->randomElement(['bank_transfer', 'gopay', 'qris', 'permata_va', 'credit_card']);
                $fraudStatus = fake()->randomElement(['accept', 'challenge']);
                break;
            case 'pending':
            case 'challenge':
                $snapToken = Str::random(32);
                $paymentType = fake()->randomElement(['bank_transfer', 'gopay', 'qris', 'permata_va', 'credit_card']);
                break;
            case 'expire':
            case 'deny':
            case 'cancel':
                $paidAt = null;
                $paymentType = null;
                $snapToken = null;
                break;
        }
        
        if ($paymentType) {
            switch ($paymentType) {
                case 'bank_transfer':
                case 'permata_va':
                    $vaNumber = fake()->numerify('################');
                    $bank = ($paymentType === 'permata_va') ? 'permata' : fake()->randomElement(['bca', 'bni', 'mandiri', 'bri']);
                    break;
                case 'gopay':
                    $deeplinkUrl = 'https://gopay.link/' . fake()->slug();
                    break;
                case 'qris':
                    $qrCode = 'https://qris.link/' . fake()->slug();
                    break;
                case 'credit_card':
                    break;
            }
        }

        if (!$snapToken && in_array($transactionStatus, ['pending', 'challenge'])) {
            $snapToken = Str::random(32);
        }

        return [
            'order_id' => $order->id,
            'midtrans_transaction_id' => 'TRX-' . Str::upper(Str::random(10)) . '-' . fake()->unique()->numerify('####'),
            'midtrans_transaction_status' => $transactionStatus,
            'amount_paid' => $totalAmount,
            'paid_at' => $paidAt,
            'midtrans_payment_type' => $paymentType,
            'midtrans_snap_token' => $snapToken,
            'midtrans_va_number' => $vaNumber,
            'midtrans_bank' => $bank,
            'midtrans_bill_key' => null,
            'midtrans_biller_key' => null,
            'midtrans_qr_code' => $qrCode,
            'midtrans_deeplink_url' => $deeplinkUrl,
            'midtrans_fraud_status' => $fraudStatus,
        ];
    }
}
