<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menjalankan migrasi.
     *
     * Membuat tabel 'users' dan 'user_addresses',
     * serta menambahkan kolom 'role' ke tabel 'users'.
     */
    public function up(): void
    {
        // Membuat tabel 'users'
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('profile_picture')->default('default.png');
            $table->date('dob')->nullable();
            $table->enum('role', ['user', 'admin'])->default('user');
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('last_login')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        // Membuat tabel 'user_addresses'
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('label');
            $table->string('recipient_name');
            $table->string('phone');
            $table->text('address');
            $table->string('country')->nullable();
            $table->string('city');
            $table->string('province');
            $table->string('postal_code');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        // Membuat tabel 'password_reset_tokens'
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // Membuat tabel 'sessions'
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Membalikkan migrasi.
     *
     * Menghapus tabel jika migrasi dibatalkan.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('user_addresses');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};