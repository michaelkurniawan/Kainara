<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'category_id',
        'vendor_id',
        'gender_id',
        'name',
        'slug',
        'origin', 
        'description',
        'price',
        'image', 
        'material', 
    ];

    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Get the product variants for the product.
     */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function vendor(): BelongsTo // Add this relationship
    {
        return $this->belongsTo(Vendor::class);
    }

    public function gender(): BelongsTo // New relationship for Gender
    {
        return $this->belongsTo(Gender::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    public function getTotalStockAttribute(): int
    {
        return $this->variants->sum('stock');
    }
}