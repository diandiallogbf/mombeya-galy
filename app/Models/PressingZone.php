<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'fee', 'note', 'position', 'is_active'])]
class PressingZone extends Model
{
    protected function casts(): array
    {
        return [
            'fee' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('position')->orderBy('name');
    }
}
