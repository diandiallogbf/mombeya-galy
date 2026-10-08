<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum OrderStatus: string implements HasColor, HasLabel
{
    case Pending = 'en_attente';
    case Confirmed = 'confirmee';
    case Preparing = 'en_preparation';
    case Shipped = 'expediee';
    case Delivered = 'livree';
    case Cancelled = 'annulee';

    public function getLabel(): string
    {
        return match ($this) {
            self::Pending => 'En attente',
            self::Confirmed => 'Confirmée',
            self::Preparing => 'En préparation',
            self::Shipped => 'Expédiée',
            self::Delivered => 'Livrée',
            self::Cancelled => 'Annulée',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Confirmed => 'info',
            self::Preparing => 'info',
            self::Shipped => 'primary',
            self::Delivered => 'success',
            self::Cancelled => 'danger',
        };
    }

    /**
     * Steps shown on the order tracking timeline (cancelled is handled apart).
     *
     * @return array<int, self>
     */
    public static function timeline(): array
    {
        return [self::Pending, self::Confirmed, self::Preparing, self::Shipped, self::Delivered];
    }
}
