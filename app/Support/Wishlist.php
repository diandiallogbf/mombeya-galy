<?php

namespace App\Support;

use App\Models\Product;
use App\Models\User;

/**
 * Favourites: kept in the session for visitors, in the `favorites` table for
 * signed-in customers (the session list is merged into the account at login).
 */
class Wishlist
{
    private const KEY = 'wishlist.ids';

    /** Per-request cache key (product cards ask many times per page). */
    private const CACHE = 'wishlist.cache';

    /**
     * @return array<int, int>
     */
    public static function ids(): array
    {
        $cache = request()->attributes;
        if (! $cache->has(self::CACHE)) {
            $user = auth()->user();
            $cache->set(self::CACHE, $user
                ? $user->favoriteProducts()->pluck('products.id')->map(fn ($id) => (int) $id)->all()
                : array_map('intval', session(self::KEY, [])));
        }

        return $cache->get(self::CACHE);
    }

    public static function has(int $productId): bool
    {
        return in_array($productId, self::ids(), true);
    }

    public static function count(): int
    {
        return count(self::ids());
    }

    /**
     * Adds or removes the product; returns true when it is now a favourite.
     */
    public static function toggle(Product $product): bool
    {
        $added = ! self::has($product->id);
        $user = auth()->user();

        if ($user) {
            $added ? $user->favoriteProducts()->syncWithoutDetaching([$product->id]) : $user->favoriteProducts()->detach($product->id);
        } else {
            $ids = array_values(array_diff(self::ids(), [$product->id]));
            if ($added) {
                $ids[] = $product->id;
            }
            session([self::KEY => $ids]);
        }

        self::flush();

        return $added;
    }

    public static function mergeSessionInto(User $user): void
    {
        $ids = session(self::KEY, []);
        if ($ids) {
            $existing = Product::whereIn('id', $ids)->pluck('id')->all();
            $user->favoriteProducts()->syncWithoutDetaching($existing);
            session()->forget(self::KEY);
        }
        self::flush();
    }

    public static function flush(): void
    {
        request()->attributes->remove(self::CACHE);
    }
}
