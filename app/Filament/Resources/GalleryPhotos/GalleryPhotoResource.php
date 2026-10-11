<?php

namespace App\Filament\Resources\GalleryPhotos;

use App\Filament\Resources\GalleryPhotos\Pages\ManageGalleryPhotos;
use App\Models\GalleryPhoto;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use UnitEnum;

class GalleryPhotoResource extends Resource
{
    protected static ?string $model = GalleryPhoto::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCamera;

    protected static string|UnitEnum|null $navigationGroup = 'Contenu du site';

    protected static ?string $navigationLabel = 'Galerie « Portés par vous »';

    protected static ?string $modelLabel = 'photo';

    protected static ?string $pluralModelLabel = 'photos de la galerie';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                FileUpload::make('image')
                    ->label('Photo')
                    ->helperText('Format carré conseillé. Utilisez uniquement des photos de clients qui ont donné leur accord.')
                    ->image()
                    ->disk('public')
                    ->directory('galerie')
                    ->maxSize(5120)
                    ->imageEditor()
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('caption')->label('Légende')->maxLength(120),
                TextInput::make('link')->label('Lien (publication Instagram…)')->url(),
                TextInput::make('position')->label('Ordre')->numeric()->default(0),
                Toggle::make('is_active')->label('Visible')->default(true)->inline(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('position')
            ->defaultSort('position')
            ->columns([
                ImageColumn::make('image')->label('Photo')->disk('public')->imageHeight(64)->imageWidth(64),
                TextColumn::make('caption')->label('Légende')->placeholder('—'),
                ToggleColumn::make('is_active')->label('Visible'),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageGalleryPhotos::route('/')];
    }
}
