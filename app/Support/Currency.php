<?php

namespace App\Support;

use App\Models\Setting;

class Currency
{
    public const BASE = 'GNF';

    public static function current(): string
    {
        $code = session('currency', self::BASE);

        return array_key_exists($code, config('shop.currencies')) ? $code : self::BASE;
    }

    /**
     * How many GNF make one unit of the given currency.
     */
    public static function rate(string $code): float
    {
        return match ($code) {
            'USD' => max(1, (float) Setting::get('usd_rate', 8650)),
            'EUR' => max(1, (float) Setting::get('eur_rate', 10100)),
            default => 1.0,
        };
    }

    /**
     * Format an amount expressed in GNF, converted to the visitor's currency unless $code is given.
     */
    public static function format(int|float|null $amountGnf, ?string $code = null): string
    {
        $code ??= self::current();
        $amount = (float) ($amountGnf ?? 0);

        return match ($code) {
            'USD' => '$'.number_format($amount / self::rate('USD'), 2, ',', ' '),
            'EUR' => number_format($amount / self::rate('EUR'), 2, ',', ' ').' €',
            default => number_format($amount, 0, ',', '.').' GNF',
        };
    }
}
