<?php

namespace App\Filament\Resources\FoundationPosts\Pages;

use App\Filament\Resources\FoundationPosts\FoundationPostResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFoundationPost extends EditRecord
{
    protected static string $resource = FoundationPostResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
