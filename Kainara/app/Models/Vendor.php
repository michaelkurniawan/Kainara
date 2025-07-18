<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'address',
        'city',
        'province',
        'postal_code',
        'business_type',
        'business_description',
    ];

    // Define relationship with products if needed
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}