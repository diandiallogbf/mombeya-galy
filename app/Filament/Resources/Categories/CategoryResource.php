<?php

namespace App\Filament\Resources\Categories;

use App\Filament\Resources\Categories\Pages\ManageCategories;
use App\Models\Category;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use UnitEnum;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static string|UnitEnum|null $navigationGroup = 'Catalogue';

    protected static ?string $navigationLabel = 'Catégories';

    protected static ?string $modelLabel = 'catégorie';

    protected static ?string $pluralModelLabel = 'catégories';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('name')
                    ->label('Nom')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Set $set, ?string $state, string $operation) => $operation === 'create' ? $set('slug', Str::slug((string) $state)) : null),
                TextInput::make('slug')
                    ->label('Lien (slug)')
                    ->required()
                    ->alphaDash()
                    ->unique(ignoreRecord: true),
                FileUpload::make('image')
                    ->label('Image de la vignette')
                    ->helperText('Format portrait conseillé (ex : 480 × 600 px).')
                    ->image()
                    ->disk('public')
                    ->directory('categories')
                    ->maxSize(4096)
                    ->imageEditor()
                    ->columnSpanFull(),
                Textarea::make('description')->label('Description')->rows(2)->columnSpanFull(),
                TextInput::make('icon')
                    ->label('Icône du menu mobile')
                    ->placeholder('fa-solid fa-shirt')
                    ->helperText('Classe Font Awesome (fontawesome.com/icons).'),
                TextInput::make('position')->label('Ordre d\'affichage')->numeric()->default(0),
                Grid::make(3)->columnSpanFull()->schema([
                    Toggle::make('is_active')->label('Active')->default(true),
                    Toggle::make('show_on_home')->label('Vignette sur l\'accueil')->default(true),
                    Toggle::make('show_in_menu')->label('Dans le menu')->default(true),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('position')
            ->defaultSort('position')
            ->columns([
                ImageColumn::make('image')->label('Image')->disk('public')->imageHeight(56)->imageWidth(45),
                TextColumn::make('name')->label('Nom')->searchable()->description(fn (Category $record) => '/categorie/'.$record->slug),
                TextColumn::make('products_count')->label('Produits')->counts('products')->badge(),
                ToggleColumn::make('is_active')->label('Active'),
                ToggleColumn::make('show_on_home')->label('Accueil'),
                ToggleColumn::make('show_in_menu')->label('Menu'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageCategories::route('/'),
        ];
    }
}
