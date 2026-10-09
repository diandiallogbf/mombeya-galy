<?php

namespace App\Filament\Resources\PressingServices\Pages;

use App\Filament\Resources\PressingServices\PressingServiceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManagePressingServices extends ManageRecords
{
    protected static string $resource = PressingServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Nouveau service')];
    }
}
