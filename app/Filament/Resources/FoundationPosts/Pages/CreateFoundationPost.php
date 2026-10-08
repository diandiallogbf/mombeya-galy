<?php

namespace App\Filament\Resources\FoundationPosts\Pages;

use App\Filament\Resources\FoundationPosts\FoundationPostResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFoundationPost extends CreateRecord
{
    protected static string $resource = FoundationPostResource::class;
}
