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
            $table->foreignId('order_id')->unique();
            $table->string('payment_method');
            $table->string('payment_status')->default('pending'); // pending, completed, failed
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_reference')->nullable();
            $table->decimal('amount_paid', 12, 2);
            $table->timestamps();
        });

        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id');
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
