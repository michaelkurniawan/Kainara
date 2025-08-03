<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;


class ArtisanProfile extends Model
{
    use HasFactory;
    protected $guarded = []; // Izinkan semua kolom diisi secara massal

    // HAPUS relasi ke User
    // public function user(): BelongsTo { ... } // <-- HAPUS METHOD INI

    public function portfolio(): HasOne
    {
        return $this->hasOne(Portfolio::class);
    }
}