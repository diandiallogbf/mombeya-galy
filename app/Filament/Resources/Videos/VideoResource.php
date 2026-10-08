<?php

namespace App\Filament\Resources\Videos;

use App\Filament\Resources\Videos\Pages\ManageVideos;
use App\Models\Video;
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

class VideoResource extends Resource
{
    protected static ?string $model = Video::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedVideoCamera;

    protected static string|UnitEnum|null $navigationGroup = 'Contenu du site';

    protected static ?string $navigationLabel = 'Vidéos';

    protected static ?string $modelLabel = 'vidéo';

    protected static ?string $pluralModelLabel = 'vidéos';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('title')->label('Titre')->required()->maxLength(255)->columnSpanFull(),
                TextInput::make('youtube_url')
                    ->label('Lien YouTube')
                    ->required()
                    ->url()
                    ->placeholder('https://www.youtube.com/watch?v=...')
                    ->regex('~(youtu\.be/|v=|embed/|shorts/)[A-Za-z0-9_-]{11}~')
                    ->validationMessages(['regex' => 'Ce lien YouTube n\'est pas reconnu.'])
                    ->columnSpanFull(),
                FileUpload::make('thumbnail')
                    ->label('Miniature (optionnelle)')
                    ->helperText('Par défaut, la miniature YouTube est utilisée.')
                    ->image()
                    ->disk('public')
                    ->directory('videos')
                    ->columnSpanFull(),
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
                ImageColumn::make('thumbnail_url')->label('Aperçu')->state(fn (Video $record) => $record->thumbnailUrl())->imageHeight(54)->imageWidth(96),
                TextColumn::make('title')->label('Titre')->searchable(),
                TextColumn::make('youtube_url')->label('Lien')->limit(40),
                ToggleColumn::make('is_active')->label('Visible'),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageVideos::route('/')];
    }
}
