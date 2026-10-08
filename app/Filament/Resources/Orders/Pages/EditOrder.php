<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    public function getTitle(): string
    {
        return 'Commande '.$this->record->reference;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('whatsapp')
                ->label('Contacter sur WhatsApp')
                ->icon(Heroicon::OutlinedChatBubbleLeftRight)
                ->color('success')
                ->url(fn () => OrderResource::whatsappUrl($this->record), shouldOpenInNewTab: true),
            Action::make('call')
                ->label('Appeler')
                ->icon(Heroicon::OutlinedPhone)
                ->color('gray')
                ->url(fn () => 'tel:'.preg_replace('/\s+/', '', $this->record->customer_phone)),
            DeleteAction::make(),
        ];
    }
}
