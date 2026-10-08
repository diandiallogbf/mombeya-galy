<?php

namespace App\Models;

use App\Support\Media;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['title', 'youtube_url', 'thumbnail', 'position', 'is_active'])]
class Video extends Model
{
    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('position')->orderByDesc('id');
    }

    public function youtubeId(): ?string
    {
        if (preg_match('~(?:youtu\.be/|v=|embed/|shorts/)([A-Za-z0-9_-]{11})~', $this->youtube_url, $m)) {
            return $m[1];
        }

        return null;
    }

    public function embedUrl(): ?string
    {
        $id = $this->youtubeId();

        return $id ? "https://www.youtube-nocookie.com/embed/{$id}?autoplay=1&rel=0" : null;
    }

    public function thumbnailUrl(): string
    {
        if ($this->thumbnail) {
            return Media::url($this->thumbnail);
        }
        $id = $this->youtubeId();

        return $id ? "https://i.ytimg.com/vi/{$id}/hqdefault.jpg" : Media::url(null);
    }
}
