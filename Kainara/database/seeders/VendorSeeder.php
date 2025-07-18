<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Vendor;

class VendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Karena kolom 'is_approved' sudah dihapus, semua vendor yang dibuat
        // melalui factory akan dianggap 'disetujui' secara implisit
        // karena tidak ada lagi status yang perlu dikelola.
        Vendor::factory()->count(10)->create();

        // Bagian untuk membuat 'unapproved vendors' DIHAPUS
        // karena konsep 'unapproved' tidak lagi ada di tabel vendors.
    }
}
