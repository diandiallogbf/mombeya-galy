<?php

namespace App\Filament\Resources\AidRequests;

use App\Filament\Resources\AidRequests\Pages\ManageAidRequests;
use App\Models\AidRequest;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class AidRequestResource extends Resource
{
    protected static ?string $model = AidRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHandRaised;

    protected static string|UnitEnum|null $navigationGroup = 'Fondation';

    protected static ?string $navigationLabel = 'Demandes d\'aide';

    protected static ?string $modelLabel = 'demande d\'aide';

    protected static ?string $pluralModelLabel = 'demandes d\'aide';

    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        $count = AidRequest::where('status', 'nouvelle')->count();

        return $count ? (string) $count : null;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('full_name')->label('Prénom et nom')->required(),
                TextInput::make('phone')->label('Téléphone')->required(),
                TextInput::make('transfer_method')->label('Moyen de transfert'),
                TextInput::make('email')->label('Email')->email(),
                Textarea::make('message')->label('Message')->rows(5)->columnSpanFull(),
                Select::make('status')->label('Statut')->options(AidRequest::STATUSES)->required()->default('nouvelle'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')->label('Reçue le')->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('full_name')->label('Nom')->searchable(),
                TextColumn::make('phone')->label('Téléphone')->searchable(),
                TextColumn::make('transfer_method')->label('Transfert'),
                TextColumn::make('message')->label('Message')->limit(50)->wrap(),
                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn ($state) => AidRequest::STATUSES[$state] ?? $state)
                    ->color(fn ($state) => match ($state) {
                        'nouvelle' => 'warning',
                        'acceptee' => 'success',
                        'refusee' => 'danger',
                        default => 'info',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')->label('Statut')->options(AidRequest::STATUSES),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageAidRequests::route('/')];
    }
}
