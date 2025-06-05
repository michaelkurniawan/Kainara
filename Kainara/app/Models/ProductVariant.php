<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'name',
        'value',
        'additional_price',
        'stock_quantity',
        'image',
    ];

    /**
     * Get the product that the variant belongs to.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}