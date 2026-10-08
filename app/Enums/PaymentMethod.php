<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PaymentMethod: string implements HasLabel
{
    case OrangeMoney = 'orange_money';
    case MtnMoney = 'mtn_money';
    case CashOnDelivery = 'livraison';
    case Card = 'carte';
    case MoneyTransfer = 'transfert';

    public function getLabel(): string
    {
        return match ($this) {
            self::OrangeMoney => 'Orange Money',
            self::MtnMoney => 'MTN Mobile Money',
            self::CashOnDelivery => 'Paiement à la livraison',
            self::Card => 'Carte bancaire',
            self::MoneyTransfer => 'Western Union / Ria / MoneyGram',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::OrangeMoney => 'Envoyez le montant au numéro Orange Money de la boutique.',
            self::MtnMoney => 'Envoyez le montant au numéro MTN MoMo de la boutique.',
            self::CashOnDelivery => 'Payez en espèces à la réception (Conakry uniquement).',
            self::Card => 'Visa / Mastercard : un conseiller vous envoie un lien de paiement sécurisé.',
            self::MoneyTransfer => 'Idéal depuis l\'étranger : un conseiller vous communique les informations du bénéficiaire.',
        };
    }

    /**
     * Setting key holding the merchant number shown to the customer, if any.
     */
    public function settingKey(): ?string
    {
        return match ($this) {
            self::OrangeMoney => 'orange_money_number',
            self::MtnMoney => 'mtn_money_number',
            default => null,
        };
    }
}
