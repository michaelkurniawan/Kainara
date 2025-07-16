<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained('orders')->onDelete('cascade');
            $table->string('midtrans_transaction_id')->unique()->nullable();
            $table->string('midtrans_transaction_status')->default('pending');
            $table->decimal('amount_paid', 12, 2);
            $table->timestamp('paid_at')->nullable();
            $table->string('midtrans_payment_type')->nullable();
            $table->string('midtrans_snap_token')->nullable();
            $table->string('midtrans_va_number')->nullable();
            $table->string('midtrans_bank')->nullable();
            $table->string('midtrans_bill_key')->nullable();
            $table->string('midtrans_biller_key')->nullable();
            $table->string('midtrans_qr_code')->nullable();
            $table->string('midtrans_deeplink_url')->nullable();
            $table->string('midtrans_fraud_status')->nullable();
            $table->timestamps();
        });

        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained('payments')->onDelete('cascade');
            $table->decimal('refunded_amount', 12, 2);
            $table->string('reason')->nullable();
            $table->timestamp('refunded_at');
            $table->string('refund_reference')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};