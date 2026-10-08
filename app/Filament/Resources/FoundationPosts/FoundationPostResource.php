<?php

namespace App\Filament\Resources\FoundationPosts;

use App\Filament\Resources\FoundationPosts\Pages\CreateFoundationPost;
use App\Filament\Resources\FoundationPosts\Pages\EditFoundationPost;
use App\Filament\Resources\FoundationPosts\Pages\ListFoundationPosts;
use App\Models\FoundationPost;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use UnitEnum;

class FoundationPostResource extends Resource
{
    protected static ?string $model = FoundationPost::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHeart;

    protected static string|UnitEnum|null $navigationGroup = 'Fondation';

    protected static ?string $navigationLabel = 'Actions de la fondation';

    protected static ?string $modelLabel = 'action';

    protected static ?string $pluralModelLabel = 'actions de la fondation';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make('Article')
                    ->columnSpan(['lg' => 2])
                    ->schema([
                        TextInput::make('title')->label('Titre')->required()->maxLength(255),
                        TextInput::make('slug')->label('Lien (slug)')->helperText('Généré automatiquement si vide.')->unique(ignoreRecord: true),
                        RichEditor::make('content')->label('Contenu'),
                        FileUpload::make('gallery')
                            ->label('Galerie photos')
                            ->image()
                            ->multiple()
                            ->reorderable()
                            ->panelLayout('grid')
                            ->disk('public')
                            ->directory('foundation')
                            ->maxFiles(12),
                    ]),
                Section::make('Publication')
                    ->columnSpan(['lg' => 1])
                    ->schema([
                        FileUpload::make('cover')->label('Image de couverture')->image()->disk('public')->directory('foundation')->imageEditor(),
                        DatePicker::make('published_at')->label('Date')->default(now())->native(false)->displayFormat('d/m/Y'),
                        Toggle::make('is_published')->label('Publié')->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('published_at', 'desc')
            ->columns([
                ImageColumn::make('cover')->label('Couverture')->disk('public')->imageHeight(56)->imageWidth(45),
                TextColumn::make('title')->label('Titre')->searchable()->wrap(),
                TextColumn::make('published_at')->label('Date')->date('d/m/Y')->sortable(),
                ToggleColumn::make('is_published')->label('Publié'),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFoundationPosts::route('/'),
            'create' => CreateFoundationPost::route('/create'),
            'edit' => EditFoundationPost::route('/{record}/edit'),
        ];
    }
}
