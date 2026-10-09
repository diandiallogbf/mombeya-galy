<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Throwable;

#[Fillable(['key', 'value'])]
class Setting extends Model
{
    private const CACHE_KEY = 'shop.settings';

    /**
     * All settings merged over the defaults declared in config/shop.php.
     *
     * @return array<string, mixed>
     */
    public static function allValues(): array
    {
        try {
            $stored = Cache::rememberForever(self::CACHE_KEY, fn () => static::query()->pluck('value', 'key')->all());
        } catch (Throwable) {
            $stored = [];
        }

        // A value saved empty in the admin overrides the default (e.g. to hide a social icon).
        return array_merge(config('shop.defaults', []), $stored);
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return static::allValues()[$key] ?? $default;
    }

    /**
     * @param  array<string, mixed>  $values
     */
    public static function put(array $values): void
    {
        foreach ($values as $key => $value) {
            static::updateOrCreate(['key' => $key], ['value' => $value]);
        }
        Cache::forget(self::CACHE_KEY);
    }
}
