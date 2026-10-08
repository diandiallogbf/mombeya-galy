<?php

namespace App\Filament\Resources\DeliveryZones;

use App\Filament\Resources\DeliveryZones\Pages\ManageDeliveryZones;
use App\Models\DeliveryZone;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use UnitEnum;

class DeliveryZoneResource extends Resource
{
    protected static ?string $model = DeliveryZone::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    protected static string|UnitEnum|null $navigationGroup = 'Paramètres';

    protected static ?string $navigationLabel = 'Zones de livraison';

    protected static ?string $modelLabel = 'zone de livraison';

    protected static ?string $pluralModelLabel = 'zones de livraison';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('name')->label('Zone')->required()->maxLength(255)->columnSpanFull(),
                TextInput::make('fee')->label('Frais de livraison')->numeric()->minValue(0)->required()->suffix('GNF'),
                TextInput::make('delay')->label('Délai')->placeholder('24h'),
                TextInput::make('position')->label('Ordre')->numeric()->default(0),
                Toggle::make('is_active')->label('Active')->default(true)->inline(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('position')
            ->defaultSort('position')
            ->columns([
                TextColumn::make('name')->label('Zone')->searchable(),
                TextColumn::make('fee')->label('Frais')->formatStateUsing(fn ($state) => $state ? money($state, 'GNF') : 'Gratuit'),
                TextColumn::make('delay')->label('Délai'),
                ToggleColumn::make('is_active')->label('Active'),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageDeliveryZones::route('/')];
    }
}
