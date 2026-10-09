<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum PressingStatus: string implements HasColor, HasLabel
{
    case Requested = 'demande_recue';
    case Collected = 'collecte';
    case Cleaning = 'en_nettoyage';
    case Ready = 'pret';
    case Delivered = 'livre';
    case Cancelled = 'annule';

    public function getLabel(): string
    {
        return match ($this) {
            self::Requested => 'Demande reçue',
            self::Collected => 'Collecté / déposé',
            self::Cleaning => 'En nettoyage',
            self::Ready => 'Prêt',
            self::Delivered => 'Livré / retiré',
            self::Cancelled => 'Annulé',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Requested => 'warning',
            self::Collected => 'info',
            self::Cleaning => 'info',
            self::Ready => 'primary',
            self::Delivered => 'success',
            self::Cancelled => 'danger',
        };
    }

    /**
     * @return array<int, self>
     */
    public static function timeline(): array
    {
        return [self::Requested, self::Collected, self::Cleaning, self::Ready, self::Delivered];
    }

    /**
     * @return array<int, string>
     */
    public static function icons(): array
    {
        return ['fa-receipt', 'fa-truck-pickup', 'fa-soap', 'fa-shirt', 'fa-house-circle-check'];
    }
}
