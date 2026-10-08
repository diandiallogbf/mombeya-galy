<?php

namespace App\Filament\Resources\Slides;

use App\Filament\Resources\Slides\Pages\ManageSlides;
use App\Models\Slide;
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

class SlideResource extends Resource
{
    protected static ?string $model = Slide::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static string|UnitEnum|null $navigationGroup = 'Contenu du site';

    protected static ?string $navigationLabel = 'Bannières d\'accueil';

    protected static ?string $modelLabel = 'bannière';

    protected static ?string $pluralModelLabel = 'bannières';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                FileUpload::make('image')
                    ->label('Image')
                    ->helperText('Format paysage, 1900 × 900 px conseillé. Le texte ci-dessous s\'affiche par-dessus (laisser vide si le texte est déjà dans l\'image).')
                    ->image()
                    ->disk('public')
                    ->directory('slides')
                    ->maxSize(6144)
                    ->imageEditor()
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('title')->label('Titre')->maxLength(255)->columnSpanFull(),
                TextInput::make('subtitle')->label('Sous-titre')->maxLength(255)->columnSpanFull(),
                TextInput::make('button_text')->label('Texte du bouton')->maxLength(60),
                TextInput::make('button_link')->label('Lien du bouton')->placeholder('/boutique')->maxLength(255),
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
                ImageColumn::make('image')->label('Image')->disk('public')->imageHeight(60)->imageWidth(126),
                TextColumn::make('title')->label('Titre')->description(fn (Slide $record) => $record->subtitle)->wrap(),
                TextColumn::make('button_link')->label('Lien'),
                ToggleColumn::make('is_active')->label('Active'),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageSlides::route('/')];
    }
}
