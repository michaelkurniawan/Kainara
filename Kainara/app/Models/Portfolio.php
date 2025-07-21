<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Portfolio extends Model {
    use HasFactory;
    protected $guarded = [];

    
    /**
     * The attributes that should be cast.
     * @var array
     */
    protected $casts = [
        'photo_paths' => 'array', // <-- PASTIKAN BARIS INI ADA
    ];
    

    public function artisanProfile(): BelongsTo {
        return $this->belongsTo(ArtisanProfile::class);
    }
}