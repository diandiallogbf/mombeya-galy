<?php

namespace App\Models;

use App\Support\Media;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable([
    'category_id', 'name', 'slug', 'reference', 'price', 'discount_price', 'description',
    'images', 'sizes', 'colors', 'stock', 'is_active', 'is_deliverable', 'is_new',
    'is_prestige', 'is_trend_week', 'is_trend_month', 'views', 'sales_count',
])]
class Product extends Model
{
    public const SORTS = [
        'populaire' => 'Tri par popularité',
        'recent-ancien' => 'Tri du plus récent au plus ancien',
        'ancien-recent' => 'Tri du plus ancien au plus récent',
        'croissant' => 'Tri par prix croissant',
        'decroissant' => 'Tri par prix décroissant',
    ];

    protected function casts(): array
    {
        return [
            'images' => 'array',
            'sizes' => 'array',
            'colors' => 'array',
            'price' => 'integer',
            'discount_price' => 'integer',
            'is_active' => 'boolean',
            'is_deliverable' => 'boolean',
            'is_new' => 'boolean',
            'is_prestige' => 'boolean',
            'is_trend_week' => 'boolean',
            'is_trend_month' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Product $product) {
            if (blank($product->slug)) {
                $product->slug = static::uniqueSlug($product->name);
            }
            if (blank($product->reference)) {
                $product->reference = 'MG-'.strtoupper(Str::random(6));
            }
        });
    }

    public static function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'produit';
        $slug = $base;
        $i = 1;
        while (static::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeSorted(Builder $query, ?string $sort): Builder
    {
        return match ($sort) {
            'populaire' => $query->orderByDesc('sales_count')->orderByDesc('views'),
            'ancien-recent' => $query->orderBy('created_at')->orderBy('id'),
            'croissant' => $query->orderByRaw('COALESCE(discount_price, price) asc'),
            'decroissant' => $query->orderByRaw('COALESCE(discount_price, price) desc'),
            default => $query->orderByDesc('created_at')->orderByDesc('id'),
        };
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('reference', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%");
        });
    }

    public function hasDiscount(): bool
    {
        return $this->discount_price !== null && $this->discount_price > 0 && $this->discount_price < $this->price;
    }

    public function finalPrice(): int
    {
        return $this->hasDiscount() ? $this->discount_price : $this->price;
    }

    public function discountPercent(): int
    {
        return $this->hasDiscount() ? (int) round(100 - ($this->discount_price * 100 / $this->price)) : 0;
    }

    public function inStock(): bool
    {
        return $this->stock > 0;
    }

    /**
     * @return array<int, string>
     */
    public function imageUrls(): array
    {
        $images = array_values(array_filter($this->images ?? []));

        return $images ? array_map(fn ($path) => Media::url($path), $images) : [Media::url(null)];
    }

    public function mainImageUrl(): string
    {
        return $this->imageUrls()[0];
    }

    /**
     * @return array<int, string>
     */
    public function sizeList(): array
    {
        return array_values(array_filter(array_map('trim', $this->sizes ?? [])));
    }

    /**
     * @return array<int, string>
     */
    public function colorList(): array
    {
        return array_values(array_filter(array_map('trim', $this->colors ?? [])));
    }
}
