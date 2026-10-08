<?php

namespace App\Filament\Resources\Products;

use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\EditProduct;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Models\Product;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ReplicateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use UnitEnum;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingBag;

    protected static string|UnitEnum|null $navigationGroup = 'Catalogue';

    protected static ?string $navigationLabel = 'Produits';

    protected static ?string $modelLabel = 'produit';

    protected static ?string $pluralModelLabel = 'produits';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 1;

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'reference'];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Group::make([
                    Section::make('Informations')
                        ->columns(2)
                        ->schema([
                            TextInput::make('name')
                                ->label('Nom du produit')
                                ->required()
                                ->maxLength(255)
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (Get $get, Set $set, ?string $state, string $operation) {
                                    if ($operation === 'create' && blank($get('slug'))) {
                                        $set('slug', Str::slug((string) $state));
                                    }
                                }),
                            TextInput::make('slug')
                                ->label('Lien (slug)')
                                ->helperText('Laisser vide pour le générer automatiquement.')
                                ->unique(ignoreRecord: true)
                                ->maxLength(255),
                            Select::make('category_id')
                                ->label('Catégorie')
                                ->relationship('category', 'name')
                                ->searchable()
                                ->preload()
                                ->createOptionForm([
                                    TextInput::make('name')->label('Nom')->required(),
                                    TextInput::make('slug')->label('Slug')->required()->unique('categories', 'slug'),
                                ]),
                            TextInput::make('reference')
                                ->label('Référence')
                                ->helperText('Générée automatiquement si vide.')
                                ->unique(ignoreRecord: true)
                                ->maxLength(50),
                            RichEditor::make('description')
                                ->label('Description / composition')
                                ->toolbarButtons([['bold', 'italic', 'underline'], ['bulletList', 'orderedList'], ['undo', 'redo']])
                                ->columnSpanFull(),
                        ]),

                    Section::make('Photos')
                        ->description('La première photo est utilisée comme image principale. Glissez-déposez pour réordonner.')
                        ->schema([
                            FileUpload::make('images')
                                ->hiddenLabel()
                                ->image()
                                ->multiple()
                                ->reorderable()
                                ->appendFiles()
                                ->panelLayout('grid')
                                ->disk('public')
                                ->directory('products')
                                ->maxSize(5120)
                                ->maxFiles(10)
                                ->imageEditor(),
                        ]),

                    Section::make('Variantes')
                        ->columns(2)
                        ->schema([
                            TagsInput::make('sizes')
                                ->label('Tailles disponibles')
                                ->placeholder('Ajouter une taille')
                                ->suggestions(['S', 'M', 'L', 'XL', 'XXL', '2 ans', '4 ans', '6 ans', '8 ans', '10 ans', '12 ans', 'Pointure 40', 'Pointure 41', 'Pointure 42', 'Pointure 43', 'Pointure 44', 'Pointure 45'])
                                ->helperText('Laisser vide si le produit n\'a pas de taille.'),
                            TagsInput::make('colors')
                                ->label('Couleurs disponibles')
                                ->placeholder('Ajouter une couleur')
                                ->suggestions(['Blanc', 'Noir', 'Bleu ciel', 'Bleu nuit', 'Bordeaux', 'Beige', 'Vert', 'Gris', 'Marron', 'Or'])
                                ->helperText('Laisser vide si une seule couleur.'),
                        ]),
                ])->columnSpan(['lg' => 2]),

                Group::make([
                    Section::make('Prix & stock')
                        ->schema([
                            TextInput::make('price')
                                ->label('Prix')
                                ->numeric()
                                ->minValue(0)
                                ->required()
                                ->suffix('GNF'),
                            TextInput::make('discount_price')
                                ->label('Prix promotionnel')
                                ->numeric()
                                ->minValue(0)
                                ->lt('price')
                                ->suffix('GNF')
                                ->helperText('Renseigner pour afficher le badge PROMO.'),
                            TextInput::make('stock')
                                ->label('Quantité en stock')
                                ->numeric()
                                ->minValue(0)
                                ->default(10)
                                ->required(),
                        ]),

                    Section::make('Visibilité')
                        ->schema([
                            Toggle::make('is_active')->label('En ligne')->default(true),
                            Toggle::make('is_deliverable')->label('Livrable')->default(true),
                            Toggle::make('is_new')->label('Nouveauté'),
                            Toggle::make('is_prestige')->label('Collection Prestige'),
                            Toggle::make('is_trend_week')->label('Tendance de la semaine'),
                            Toggle::make('is_trend_month')->label('Tendance du mois'),
                        ]),
                ])->columnSpan(['lg' => 1]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->paginationPageOptions([25, 50, 100])
            ->columns([
                ImageColumn::make('images')
                    ->label('Photo')
                    ->disk('public')
                    ->limit(1)
                    ->imageHeight(56)
                    ->imageWidth(42),
                TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Product $record) => $record->reference),
                TextColumn::make('category.name')
                    ->label('Catégorie')
                    ->badge()
                    ->color('gray')
                    ->sortable(),
                TextColumn::make('price')
                    ->label('Prix')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => money($state, 'GNF'))
                    ->description(fn (Product $record) => $record->hasDiscount() ? 'Promo : '.money($record->discount_price, 'GNF') : null),
                TextColumn::make('stock')
                    ->label('Stock')
                    ->sortable()
                    ->badge()
                    ->color(fn (int $state) => match (true) {
                        $state === 0 => 'danger',
                        $state < 5 => 'warning',
                        default => 'success',
                    }),
                TextColumn::make('sales_count')
                    ->label('Ventes')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('views')
                    ->label('Vues')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                ToggleColumn::make('is_active')->label('En ligne'),
                ToggleColumn::make('is_trend_week')->label('Tend. semaine')->toggleable(isToggledHiddenByDefault: true),
                ToggleColumn::make('is_trend_month')->label('Tend. mois')->toggleable(),
                ToggleColumn::make('is_prestige')->label('Prestige')->toggleable(isToggledHiddenByDefault: true),
                ToggleColumn::make('is_new')->label('Nouveau')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Ajouté le')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Catégorie')
                    ->relationship('category', 'name')
                    ->preload(),
                TernaryFilter::make('is_active')->label('En ligne'),
                Filter::make('promo')
                    ->label('En promotion')
                    ->query(fn (Builder $query) => $query->whereNotNull('discount_price')->whereColumn('discount_price', '<', 'price')),
                Filter::make('out_of_stock')
                    ->label('En rupture de stock')
                    ->query(fn (Builder $query) => $query->where('stock', 0)),
                TernaryFilter::make('is_prestige')->label('Prestige'),
                TernaryFilter::make('is_trend_month')->label('Tendance du mois'),
            ])
            ->recordActions([
                Action::make('view')
                    ->label('Voir')
                    ->icon(Heroicon::OutlinedEye)
                    ->color('gray')
                    ->url(fn (Product $record) => route('product.show', $record), shouldOpenInNewTab: true),
                EditAction::make(),
                ReplicateAction::make()
                    ->label('Dupliquer')
                    ->excludeAttributes(['slug', 'reference', 'views', 'sales_count'])
                    ->beforeReplicaSaved(function (Product $replica) {
                        $replica->name = $replica->name.' (copie)';
                        $replica->slug = Product::uniqueSlug($replica->name);
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('activate')
                        ->label('Mettre en ligne')
                        ->icon(Heroicon::OutlinedEye)
                        ->action(fn (Collection $records) => $records->each->update(['is_active' => true]))
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('deactivate')
                        ->label('Retirer du site')
                        ->icon(Heroicon::OutlinedEyeSlash)
                        ->action(fn (Collection $records) => $records->each->update(['is_active' => false]))
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'edit' => EditProduct::route('/{record}/edit'),
        ];
    }
}
