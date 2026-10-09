<?php

namespace App\Filament\Resources\PressingOrders\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Articles à traiter';

    protected static bool $isLazy = false;

    public function isReadOnly(): bool
    {
        return true;
    }

    public function table(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->columns([
                TextColumn::make('service_name')->label('Service'),
                TextColumn::make('quantity')->label('Qté'),
                TextColumn::make('unit_price')->label('Prix unitaire')->formatStateUsing(fn ($state) => money($state, 'GNF')),
                TextColumn::make('total')->label('Total')->weight('bold')->formatStateUsing(fn ($state) => money($state, 'GNF')),
            ]);
    }
}
