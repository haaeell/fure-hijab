<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'excerpt', 'content', 'thumbnail',
        'category', 'tags', 'author',
        'meta_title', 'meta_description', 'meta_keywords',
        'is_published', 'published_at', 'view_count', 'read_time',
    ];

    protected $casts = [
        'tags'         => 'array',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public static $categories = [
        'styling-guide' => 'Styling Guide',
        'fabric-notes'  => 'Fabric Notes',
        'occasion'      => 'Occasion',
        'tips'          => 'Tips & Cara',
        'news'          => 'Berita & Update',
    ];

    public function getCategoryLabelAttribute(): string
    {
        return self::$categories[$this->category] ?? ucfirst($this->category);
    }

    public function incrementView(): void
    {
        $this->increment('view_count');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true)
            ->where(function ($q) {
                $q->whereNull('published_at')->orWhere('published_at', '<=', now());
            });
    }

    public function getMetaTitleAttribute($value): string
    {
        return $value ?: $this->title;
    }

    public function getMetaDescriptionAttribute($value): string
    {
        return $value ?: Str::limit(strip_tags($this->excerpt ?? $this->content ?? ''), 160);
    }
}
