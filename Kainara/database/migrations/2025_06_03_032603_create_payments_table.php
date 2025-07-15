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
            $table->string('stripe_payment_intent_id')->unique();
            $table->string('status')->default('pending'); // e.g., pending, succeeded, requires_action, canceled, failed
            $table->decimal('amount_paid', 12, 2);
            $table->timestamp('paid_at')->nullable();
            $table->string('currency')->default('IDR');
            $table->string('payment_method_type')->nullable(); // e.g., card, bank_transfer, e_wallet
            $table->json('card_details')->nullable(); // Stores last4, brand, etc.
            $table->json('payment_method_details')->nullable(); // To store more raw details from Stripe
            $table->timestamps();
        });

        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained('payments')->onDelete('cascade');
            $table->string('stripe_refund_id')->unique();
            $table->decimal('refunded_amount', 12, 2);
            $table->string('reason')->nullable();
            $table->timestamp('refunded_at');
            $table->string('status')->default('pending'); // e.g., succeeded, pending, failed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refunds');
        Schema::dropIfExists('payments');
    }
};