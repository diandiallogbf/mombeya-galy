<?php

namespace App\Filament\Resources\FoundationPosts\Pages;

use App\Filament\Resources\FoundationPosts\FoundationPostResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFoundationPosts extends ListRecords
{
    protected static string $resource = FoundationPostResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Nouvelle action')];
    }
}
