<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;
use App\Models\User; // Pastikan model User diimpor

class UserSeeder extends Seeder
{
    /**
     * Menjalankan seed database.
     *
     * Membuat pengguna admin utama dan beberapa pengguna acak
     * (admin dan pengguna biasa) untuk tujuan pengujian.
     */
    public function run(): void
    {
        // Membuat satu Super Admin
        User::factory()->create([
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin123'), // Kata sandi: admin123
            'role' => 'admin',
            'email_verified_at' => now(),
            'last_login' => now(),
        ]);

        // Membuat 5 pengguna dengan peran 'admin'
        User::factory(5)->admin()->create();

        // Membuat 40 pengguna dengan peran 'user'
        User::factory(40)->user()->create();

        // Membuat 5 pengguna dengan email belum diverifikasi
        User::factory(5)->unverified()->create();
    }
}