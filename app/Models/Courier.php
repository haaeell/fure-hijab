<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Courier extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'logo', 'is_active', 'sort_order'];

    protected $casts = ['is_active' => 'boolean'];

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo ? asset('storage/' . $this->logo) : null;
    }
}
