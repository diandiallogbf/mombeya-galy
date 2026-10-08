<?php

namespace App\Models;

use App\Support\Media;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

#[Fillable(['title', 'slug', 'cover', 'gallery', 'content', 'published_at', 'is_published'])]
class FoundationPost extends Model
{
    protected function casts(): array
    {
        return [
            'gallery' => 'array',
            'published_at' => 'date',
            'is_published' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (FoundationPost $post) {
            if (blank($post->slug)) {
                $base = Str::slug($post->title) ?: 'article';
                $slug = $base;
                $i = 1;
                while (static::where('slug', $slug)->where('id', '!=', $post->id)->exists()) {
                    $slug = $base.'-'.$i++;
                }
                $post->slug = $slug;
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)->orderByDesc('published_at')->orderByDesc('id');
    }

    public function coverUrl(): string
    {
        return Media::url($this->cover);
    }

    /**
     * @return array<int, string>
     */
    public function galleryUrls(): array
    {
        return array_map(fn ($path) => Media::url($path), array_values(array_filter($this->gallery ?? [])));
    }
}
