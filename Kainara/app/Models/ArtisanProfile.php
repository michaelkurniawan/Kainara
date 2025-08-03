<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ArtisanProfile extends Model
{
    use HasFactory;
    protected $guarded = []; // Izinkan semua kolom diisi secara massal


    public function portfolios(): HasMany
    {
        return $this->hasMany(Portfolio::class);
    }
}