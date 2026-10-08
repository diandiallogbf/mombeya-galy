<?php

namespace App\Support;

use Illuminate\Support\Str;

class Media
{
    public const PLACEHOLDER = 'images/placeholder.svg';

    /**
     * Public URL of a file stored on the public disk (or an absolute URL), with a placeholder fallback.
     */
    public static function url(?string $path): string
    {
        if (blank($path)) {
            return asset(self::PLACEHOLDER);
        }

        if (Str::startsWith($path, ['http://', 'https://', '//'])) {
            return $path;
        }

        if (Str::startsWith($path, 'images/')) {
            return asset($path);
        }

        // asset() follows the current host, so images work whatever the APP_URL/port.
        return asset('storage/'.ltrim($path, '/'));
    }
}
