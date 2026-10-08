<?php

namespace App\Support;

use App\Models\Product;
use Illuminate\Support\Collection;

/**
 * Session cart. Lines are stored as product id + options; prices are always
 * read fresh from the database so admin price changes apply immediately.
 */
class Cart
{
    private const KEY = 'cart.lines';

    /**
     * @return array<string, array{product_id:int, size:?string, color:?string, quantity:int}>
     */
    public static function raw(): array
    {
        return session(self::KEY, []);
    }

    public static function lineKey(int $productId, ?string $size, ?string $color): string
    {
        return substr(md5($productId.'|'.$size.'|'.$color), 0, 12);
    }

    public static function add(Product $product, int $quantity, ?string $size = null, ?string $color = null): void
    {
        $lines = self::raw();
        $key = self::lineKey($product->id, $size, $color);
        $current = $lines[$key]['quantity'] ?? 0;
        $lines[$key] = [
            'product_id' => $product->id,
            'size' => $size,
            'color' => $color,
            'quantity' => self::clampQuantity($product, $current + $quantity),
        ];
        session([self::KEY => $lines]);
    }

    public static function update(string $key, int $quantity): void
    {
        $lines = self::raw();
        if (! isset($lines[$key])) {
            return;
        }
        if ($quantity < 1) {
            self::remove($key);

            return;
        }
        $product = Product::find($lines[$key]['product_id']);
        $lines[$key]['quantity'] = $product ? self::clampQuantity($product, $quantity) : $quantity;
        session([self::KEY => $lines]);
    }

    public static function remove(string $key): void
    {
        $lines = self::raw();
        unset($lines[$key]);
        session([self::KEY => $lines]);
    }

    public static function clear(): void
    {
        session()->forget(self::KEY);
    }

    private static function clampQuantity(Product $product, int $quantity): int
    {
        $max = min((int) config('shop.max_quantity', 20), max(1, $product->stock));

        return max(1, min($quantity, $max));
    }

    /**
     * Cart lines hydrated with their product; lines whose product vanished are dropped.
     *
     * @return Collection<int, object>
     */
    public static function lines(): Collection
    {
        $raw = self::raw();
        if (! $raw) {
            return collect();
        }
        $products = Product::with('category')->whereIn('id', array_column($raw, 'product_id'))->get()->keyBy('id');

        return collect($raw)
            ->filter(fn ($line) => isset($products[$line['product_id']]) && $products[$line['product_id']]->is_active)
            ->map(function ($line, $key) use ($products) {
                $product = $products[$line['product_id']];
                $unit = $product->finalPrice();

                return (object) [
                    'key' => $key,
                    'product' => $product,
                    'size' => $line['size'],
                    'color' => $line['color'],
                    'quantity' => $line['quantity'],
                    'unit_price' => $unit,
                    'total' => $unit * $line['quantity'],
                ];
            })
            ->values();
    }

    public static function count(): int
    {
        return (int) array_sum(array_column(self::raw(), 'quantity'));
    }

    public static function subtotal(): int
    {
        return (int) self::lines()->sum('total');
    }
}
