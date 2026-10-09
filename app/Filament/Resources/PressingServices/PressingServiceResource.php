<?php

namespace App\Filament\Resources\PressingServices;

use App\Filament\Resources\PressingServices\Pages\ManagePressingServices;
use App\Models\PressingService;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use UnitEnum;

class PressingServiceResource extends Resource
{
    protected static ?string $model = PressingService::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static string|UnitEnum|null $navigationGroup = 'Pressing';

    protected static ?string $navigationLabel = 'Services & tarifs';

    protected static ?string $modelLabel = 'service';

    protected static ?string $pluralModelLabel = 'services';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('name')->label('Nom du service')->required()->maxLength(255)->columnSpanFull(),
                Select::make('category')->label('Catégorie')->options(PressingService::CATEGORIES)->required()->native(false)->default('homme'),
                TextInput::make('price')->label('Prix')->numeric()->minValue(0)->required()->suffix('GNF'),
                TextInput::make('unit')->label('Unité')->default('pièce')->placeholder('pièce, kg, mois…')->required(),
                TextInput::make('icon')->label('Icône')->placeholder('fa-solid fa-shirt')->helperText('Classe Font Awesome (fontawesome.com/icons).'),
                TextInput::make('description')->label('Description courte')->maxLength(255)->columnSpanFull(),
                TextInput::make('position')->label('Ordre')->numeric()->default(0),
                Toggle::make('is_active')->label('Proposé aux clients')->default(true)->inline(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('position')
            ->defaultSort('position')
            ->defaultGroup(Group::make('category')->label('Catégorie')->getTitleFromRecordUsing(fn (PressingService $record) => $record->categoryLabel()))
            ->columns([
                TextColumn::make('name')->label('Service')->searchable()->description(fn (PressingService $record) => $record->description),
                TextColumn::make('category')->label('Catégorie')->badge()->color('gray')->formatStateUsing(fn ($state) => PressingService::CATEGORIES[$state] ?? $state),
                TextColumn::make('price')->label('Prix')->sortable()->formatStateUsing(fn ($state, PressingService $record) => money($state, 'GNF').' / '.$record->unit),
                ToggleColumn::make('is_active')->label('Proposé'),
            ])
            ->filters([
                SelectFilter::make('category')->label('Catégorie')->options(PressingService::CATEGORIES),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ManagePressingServices::route('/')];
    }
}
