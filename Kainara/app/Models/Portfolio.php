<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Portfolio extends Model {
    use HasFactory;
    protected $guarded = [];

    public function artisanProfile(): BelongsTo {
        return $this->belongsTo(ArtisanProfile::class);
    }
}