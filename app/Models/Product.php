<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'brand_id',
        'name',
        'slug',
        'description',
        'short_description',
        'price',
        'compare_price',
        'modal_price',
        'stock',
        'weight',
        'sku',
        'is_active',
        'sold_count',
        'has_variant'
    ];

    protected $casts = [
        'has_variant' => 'boolean',
        'is_active'   => 'boolean',
        'price'       => 'float',
        'compare_price' => 'float',
        'modal_price' => 'float',
        'stock'       => 'integer',
        'sold_count'  => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function collections()
    {
        return $this->belongsToMany(Collection::class);
    }
}
