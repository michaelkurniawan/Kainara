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
            $table->string('status')->default('pending'); 
            $table->decimal('amount_paid', 12, 2); 
            $table->timestamp('paid_at')->nullable(); 
            $table->string('currency')->default('IDR');
            $table->string('payment_method_type')->nullable();
            $table->json('card_details')->nullable();
            $table->json('payment_method_details')->nullable(); 
            $table->timestamps();
        });

        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained('payments')->onDelete('cascade');
            $table->string('stripe_refund_id')->unique();
            $table->decimal('refunded_amount', 12, 2); 
            $table->string('reason')->nullable(); 
            $table->timestamp('refunded_at'); 
            $table->string('status')->default('pending'); 
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