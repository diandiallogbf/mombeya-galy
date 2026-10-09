<?php

namespace App\Filament\Resources\PressingZones\Pages;

use App\Filament\Resources\PressingZones\PressingZoneResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManagePressingZones extends ManageRecords
{
    protected static string $resource = PressingZoneResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Nouvelle zone')];
    }
}
