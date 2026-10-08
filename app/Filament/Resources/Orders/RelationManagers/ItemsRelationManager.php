<?php

namespace App\Filament\Resources\Orders\RelationManagers;

use App\Models\OrderItem;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Articles commandés';

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
                ImageColumn::make('product_image')->label('')->state(fn (OrderItem $record) => $record->imageUrl())->imageHeight(56)->imageWidth(42),
                TextColumn::make('product_name')
                    ->label('Produit')
                    ->url(fn (OrderItem $record) => $record->product ? route('product.show', $record->product) : null, shouldOpenInNewTab: true)
                    ->description(fn (OrderItem $record) => $record->product?->reference),
                TextColumn::make('size')->label('Taille')->placeholder('—'),
                TextColumn::make('color')->label('Couleur')->placeholder('—'),
                TextColumn::make('quantity')->label('Qté'),
                TextColumn::make('unit_price')->label('Prix unitaire')->formatStateUsing(fn ($state) => money($state, 'GNF')),
                TextColumn::make('total')->label('Total')->weight('bold')->formatStateUsing(fn ($state) => money($state, 'GNF')),
            ]);
    }
}
