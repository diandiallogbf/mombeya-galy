<?php

namespace App\Filament\Resources\PressingOrders\Pages;

use App\Filament\Resources\PressingOrders\PressingOrderResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditPressingOrder extends EditRecord
{
    protected static string $resource = PressingOrderResource::class;

    public function getTitle(): string
    {
        return 'Pressing '.$this->record->reference;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('whatsapp')
                ->label('Contacter sur WhatsApp')
                ->icon(Heroicon::OutlinedChatBubbleLeftRight)
                ->color('success')
                ->url(fn () => PressingOrderResource::whatsappUrl($this->record), shouldOpenInNewTab: true),
            Action::make('call')
                ->label('Appeler')
                ->icon(Heroicon::OutlinedPhone)
                ->color('gray')
                ->url(fn () => 'tel:'.preg_replace('/\s+/', '', $this->record->customer_phone)),
            DeleteAction::make(),
        ];
    }
}
