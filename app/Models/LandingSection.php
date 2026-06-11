<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LandingSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'eyebrow',
        'title',
        'subtitle',
        'button_text',
        'button_url',
        'image',
        'icon',
        'background_color',
        'text_color',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
