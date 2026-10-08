<?php

namespace App\Filament\Resources\Showrooms;

use App\Filament\Resources\Showrooms\Pages\ManageShowrooms;
use App\Models\Showroom;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use UnitEnum;

class ShowroomResource extends Resource
{
    protected static ?string $model = Showroom::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;

    protected static string|UnitEnum|null $navigationGroup = 'Contenu du site';

    protected static ?string $navigationLabel = 'Showrooms';

    protected static ?string $modelLabel = 'showroom';

    protected static ?string $pluralModelLabel = 'showrooms';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        $days = [];
        foreach (Showroom::DAYS as $key => $label) {
            $days[] = TextInput::make("opening_hours.{$key}")->label($label)->placeholder('09:00 – 21:00 ou Fermé');
        }

        return $schema
            ->columns(2)
            ->components([
                TextInput::make('name')->label('Nom')->required()->maxLength(255)->columnSpanFull(),
                FileUpload::make('image')->label('Photo')->image()->disk('public')->directory('showrooms')->maxSize(5120)->imageEditor()->columnSpanFull(),
                TextInput::make('address')->label('Adresse')->maxLength(255),
                TextInput::make('phone')->label('Téléphone')->tel()->maxLength(30),
                TextInput::make('latitude')->label('Latitude')->numeric()->helperText('Clic droit sur Google Maps → copier les coordonnées.'),
                TextInput::make('longitude')->label('Longitude')->numeric(),
                Fieldset::make('Horaires d\'ouverture')->columns(2)->columnSpanFull()->schema($days),
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
                ImageColumn::make('image')->label('Photo')->disk('public')->imageHeight(56)->imageWidth(45),
                TextColumn::make('name')->label('Nom')->searchable()->description(fn (Showroom $record) => $record->address),
                TextColumn::make('phone')->label('Téléphone'),
                ToggleColumn::make('is_active')->label('Visible'),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageShowrooms::route('/')];
    }
}
