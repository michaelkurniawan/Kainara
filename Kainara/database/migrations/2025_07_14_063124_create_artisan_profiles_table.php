<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('artisan_profiles', function (Blueprint $table) {
            $table->id();
            // HAPUS SEMUA REFERENSI KE user_id

            // --- Data Kontak Utama Pendaftar ---
            $table->string('name');
            $table->string('email'); // Tidak perlu unique jika pendaftaran boleh berulang
            $table->string('status')->default('pending'); // 'pending', 'approved', 'rejected'

            // --- Data dari Step 1: Owner Profile ---
            $table->date('date_of_birth');
            $table->string('gender');
            $table->string('phone_number');
            $table->text('home_address');
            $table->string('home_province');
            $table->string('home_city');
            $table->string('home_postal_code');

            // --- Data dari Step 2: Business Information ---
            $table->string('business_name');
            $table->string('business_type');
            $table->string('other_business_type')->nullable();
            $table->text('business_description');
            $table->string('business_phone_number')->nullable();
            $table->string('business_email')->nullable();
            $table->text('business_address')->nullable();
            $table->string('business_province')->nullable();
            $table->string('business_city')->nullable();
            $table->string('business_postal_code')->nullable();

            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('artisan_profiles'); }
};