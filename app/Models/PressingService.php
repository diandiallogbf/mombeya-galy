<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'category', 'description', 'price', 'unit', 'icon', 'position', 'is_active'])]
class PressingService extends Model
{
    public const CATEGORIES = [
        'homme' => 'Homme',
        'femme' => 'Femme',
        'bazin' => 'Spécial bazin',
        'maison' => 'Maison',
        'formule' => 'Formules & abonnements',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('position')->orderBy('name');
    }

    public function categoryLabel(): string
    {
        return self::CATEGORIES[$this->category] ?? ucfirst($this->category);
    }
}
