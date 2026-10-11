<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum AppointmentStatus: string implements HasColor, HasLabel
{
    case Requested = 'nouveau';
    case Confirmed = 'confirme';
    case Done = 'realise';
    case Cancelled = 'annule';

    public function getLabel(): string
    {
        return match ($this) {
            self::Requested => 'Nouvelle demande',
            self::Confirmed => 'Rendez-vous confirmé',
            self::Done => 'Réalisé',
            self::Cancelled => 'Annulé',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Requested => 'warning',
            self::Confirmed => 'info',
            self::Done => 'success',
            self::Cancelled => 'danger',
        };
    }
}
