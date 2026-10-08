<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum PaymentStatus: string implements HasColor, HasLabel
{
    case Pending = 'en_attente';
    case Paid = 'paye';
    case Refunded = 'rembourse';

    public function getLabel(): string
    {
        return match ($this) {
            self::Pending => 'Non payé',
            self::Paid => 'Payé',
            self::Refunded => 'Remboursé',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Paid => 'success',
            self::Refunded => 'gray',
        };
    }
}
