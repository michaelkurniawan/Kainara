<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolios', function (Blueprint $table) {
            $table->id();
            // Portfolio berelasi dengan pendaftaran artisan, bukan user langsung
            $table->foreignId('artisan_profile_id')->constrained()->onDelete('cascade');

            // --- Data dari Step 3: Portfolio ---
            $table->string('project_title');
            $table->text('project_description');
            $table->string('fabric_type');
            $table->string('other_fabric_type')->nullable();
            $table->year('year_created');
            $table->json('photo_paths');    
            $table->string('video_link')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolios');
    }
};