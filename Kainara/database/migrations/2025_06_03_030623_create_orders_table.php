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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('address_id')->nullable()->constrained('user_addresses')->onDelete('set null');
            $table->string('status', 50)->default('Awaiting Payment');
            $table->decimal('shipping_cost', 10, 2);
            $table->decimal('subtotal', 12, 2);
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('auto_complete_at')->nullable();
            $table->timestamps();
            $table->string('original_user_name')->nullable();
            $table->string('original_user_email')->nullable();
            $table->string('shipping_label')->nullable(); 
            $table->string('shipping_recipient_name')->nullable();
            $table->string('shipping_phone')->nullable(); 
            $table->text('shipping_address')->nullable(); 
            $table->string('shipping_country')->nullable(); 
            $table->string('shipping_city')->nullable(); 
            $table->string('shipping_province')->nullable(); 
            $table->string('shipping_postal_code')->nullable();
            $table->string('payment_method')->nullable(); 
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('set null');
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->onDelete('set null');
            $table->string('product_name');
            $table->string('product_image')->nullable();
            $table->string('variant_size')->nullable();
            $table->string('variant_color')->nullable();
            $table->decimal('price', 12, 2);
            $table->integer('quantity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};