<?php

namespace App\Models;

use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'origin',
        'type',
        'description',
        'price',
        'image',
    ];

    /**
     * Relationship to product_variants.
     */
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

}
