<?php

use App\Models\Setting;
use App\Support\Currency;

if (! function_exists('money')) {
    /**
     * Format a GNF amount in the visitor's currency (or the given one).
     */
    function money(int|float|null $amountGnf, ?string $currency = null): string
    {
        return Currency::format($amountGnf, $currency);
    }
}

if (! function_exists('setting')) {
    function setting(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default);
    }
}

if (! function_exists('whatsapp_url')) {
    function whatsapp_url(?string $text = null): string
    {
        $number = preg_replace('/\D+/', '', (string) setting('whatsapp'));

        return 'https://wa.me/'.$number.($text ? '?text='.rawurlencode($text) : '');
    }
}
