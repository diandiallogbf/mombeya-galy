<?php

namespace App\Filament\Resources\PressingZones;

use App\Filament\Resources\PressingZones\Pages\ManagePressingZones;
use App\Models\PressingZone;
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

class PressingZoneResource extends Resource
{
    protected static ?string $model = PressingZone::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;

    protected static string|UnitEnum|null $navigationGroup = 'Pressing';

    protected static ?string $navigationLabel = 'Zones de collecte';

    protected static ?string $modelLabel = 'zone de collecte';

    protected static ?string $pluralModelLabel = 'zones de collecte';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('name')->label('Commune / quartier')->required()->maxLength(255)->columnSpanFull(),
                TextInput::make('fee')->label('Frais de collecte + livraison')->numeric()->minValue(0)->required()->suffix('GNF'),
                TextInput::make('note')->label('Précision')->placeholder('ex : Kipé, Lambanyi, Nongo…')->maxLength(255),
                TextInput::make('position')->label('Ordre')->numeric()->default(0),
                Toggle::make('is_active')->label('Collecte active')->helperText('Activez une zone quand vous commencez à la desservir.')->default(true)->inline(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('position')
            ->defaultSort('position')
            ->columns([
                TextColumn::make('name')->label('Zone')->searchable()->description(fn (PressingZone $record) => $record->note),
                TextColumn::make('fee')->label('Frais')->formatStateUsing(fn ($state) => $state ? money($state, 'GNF') : 'Gratuit'),
                ToggleColumn::make('is_active')->label('Active'),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ManagePressingZones::route('/')];
    }
}
