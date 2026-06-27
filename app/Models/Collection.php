<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Collection extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'sort_order', 'is_active', 'show_in_nav'];

    protected $casts = ['is_active' => 'boolean', 'show_in_nav' => 'boolean'];

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
