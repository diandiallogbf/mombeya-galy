<?php

namespace App\Models;

use App\Support\Media;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'image', 'address', 'phone', 'opening_hours', 'latitude', 'longitude', 'position', 'is_active'])]
class Showroom extends Model
{
    public const DAYS = [
        'lundi' => 'Lundi',
        'mardi' => 'Mardi',
        'mercredi' => 'Mercredi',
        'jeudi' => 'Jeudi',
        'vendredi' => 'Vendredi',
        'samedi' => 'Samedi',
        'dimanche' => 'Dimanche',
    ];

    protected function casts(): array
    {
        return [
            'opening_hours' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('position')->orderBy('name');
    }

    public function imageUrl(): string
    {
        return Media::url($this->image);
    }

    public function mapUrl(): ?string
    {
        if ($this->latitude && $this->longitude) {
            return 'https://www.google.com/maps?q='.$this->latitude.','.$this->longitude;
        }

        return $this->address ? 'https://www.google.com/maps?q='.urlencode($this->address.', Conakry') : null;
    }
}
