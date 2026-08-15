<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Article extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'category',
        'excerpt',
        'body',
        'cover_image',
        'author_name',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    // Auto-generate slug from title
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($article) {
            if (empty($article->slug)) {
                $article->slug = Str::slug($article->title) . '-' . time();
            }
        });
    }

    // Only published articles
    public function scopePublished($query)
    {
        return $query->where('is_published', true)
                     ->whereNotNull('published_at')
                     ->where('published_at', '<=', now());
    }

    // Cover image URL
    public function getCoverUrlAttribute(): string
    {
        if ($this->cover_image) {
            return asset('storage/' . $this->cover_image);
        }
        return asset('images/kampus-aerial.png'); // default fallback
    }

    // Category color for badges
    public function getCategoryColorAttribute(): string
    {
        return match($this->category) {
            'Berita'       => 'bg-blue-100 text-blue-700',
            'Edukasi'      => 'bg-green-100 text-green-700',
            'Kebijakan'    => 'bg-purple-100 text-purple-700',
            'Pengumuman'   => 'bg-orange-100 text-orange-700',
            default        => 'bg-gray-100 text-gray-600',
        };
    }
}
