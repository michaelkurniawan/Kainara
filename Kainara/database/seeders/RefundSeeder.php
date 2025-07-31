<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Refund;
use App\Models\Payment;
use App\Models\Order; // Import Order model

class RefundSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Mendapatkan payment yang statusnya 'succeeded' dan belum memiliki refund
        $succeededPaymentsWithoutRefunds = Payment::where('status', 'succeeded')
                                                  ->doesntHave('refunds') // Menggunakan relasi 'refunds' (hasMany)
                                                  ->get();

        foreach ($succeededPaymentsWithoutRefunds as $payment) {
            // 30% kemungkinan untuk membuat refund
            if (fake()->boolean(30)) {
                $refund = Refund::factory()->create([
                    'payment_id' => $payment->id,
                    // Jumlah refund akan acak antara 10% hingga 100% dari amount_paid
                    'refunded_amount' => fake()->randomFloat(2, $payment->amount_paid * 0.1, $payment->amount_paid),
                ]);

                // Update status order jika refund berhasil
                $order = $payment->order; // Dapatkan order terkait
                if ($order && $refund->status === 'succeeded' && $order->status !== 'Refunded') {
                    $order->status = 'Refunded';
                    $order->save();
                }
            }
        }

        // Opsional: Buat beberapa refund tambahan (misalnya refund yang gagal)
        // Refund::factory()->count(3)->failed()->create();
    }
}