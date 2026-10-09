<?php

namespace App\Filament\Resources\PressingOrders;

use App\Enums\PaymentStatus;
use App\Enums\PressingStatus;
use App\Filament\Resources\PressingOrders\Pages\EditPressingOrder;
use App\Filament\Resources\PressingOrders\Pages\ListPressingOrders;
use App\Filament\Resources\PressingOrders\RelationManagers\ItemsRelationManager;
use App\Http\Controllers\PressingController;
use App\Models\PressingOrder;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class PressingOrderResource extends Resource
{
    protected static ?string $model = PressingOrder::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static string|UnitEnum|null $navigationGroup = 'Pressing';

    protected static ?string $navigationLabel = 'Commandes pressing';

    protected static ?string $modelLabel = 'commande pressing';

    protected static ?string $pluralModelLabel = 'commandes pressing';

    protected static ?string $recordTitleAttribute = 'reference';

    protected static ?int $navigationSort = 1;

    public static function getGloballySearchableAttributes(): array
    {
        return ['reference', 'customer_name', 'customer_phone'];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = PressingOrder::where('status', PressingStatus::Requested)->count();

        return $count ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Group::make([
                    Section::make('Client')
                        ->columns(2)
                        ->schema([
                            TextInput::make('customer_name')->label('Nom')->required(),
                            TextInput::make('customer_phone')->label('Téléphone')->required(),
                            TextInput::make('customer_email')->label('Email')->email(),
                            TextEntry::make('mode')->label('Mode')->formatStateUsing(fn ($state) => PressingOrder::MODES[$state] ?? $state),
                        ]),
                    Section::make('Collecte')
                        ->columns(2)
                        ->schema([
                            TextEntry::make('zone_name')->label('Zone')->placeholder('—'),
                            TextEntry::make('showroom_name')->label('Showroom de dépôt')->placeholder('—'),
                            TextInput::make('address')->label('Adresse')->columnSpanFull(),
                            DatePicker::make('pickup_date')->label('Date de collecte')->native(false)->displayFormat('d/m/Y'),
                            TextInput::make('pickup_slot')->label('Créneau'),
                            Textarea::make('notes')->label('Précisions du client')->rows(2)->disabled()->columnSpanFull(),
                        ]),
                ])->columnSpan(['lg' => 2]),

                Group::make([
                    Section::make('Suivi')
                        ->schema([
                            Select::make('status')->label('Statut')->options(PressingStatus::class)->required()->native(false),
                            Select::make('payment_status')->label('Paiement')->options(PaymentStatus::class)->required()->native(false),
                            TextEntry::make('payment_method')->label('Mode de paiement')->formatStateUsing(fn ($state) => PressingController::paymentLabel($state)),
                            Textarea::make('admin_notes')->label('Notes internes')->rows(3)->helperText('Ex : montant ajusté, tache signalée…'),
                        ]),
                    Section::make('Montants')
                        ->schema([
                            TextEntry::make('subtotal')->label('Articles')->formatStateUsing(fn ($state) => money($state, 'GNF')),
                            TextEntry::make('express_fee')->label('Express')->formatStateUsing(fn ($state) => $state ? money($state, 'GNF') : 'Non'),
                            TextEntry::make('collection_fee')->label('Collecte')->formatStateUsing(fn ($state) => $state ? money($state, 'GNF') : 'Gratuit'),
                            TextEntry::make('total')->label('Total estimé')->weight('bold')->color('primary')->formatStateUsing(fn ($state) => money($state, 'GNF')),
                            TextEntry::make('created_at')->label('Réservée le')->dateTime('d/m/Y à H:i'),
                        ]),
                ])->columnSpan(['lg' => 1]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('reference')->label('Référence')->searchable()->copyable()->weight('medium')->fontFamily('mono'),
                TextColumn::make('created_at')->label('Reçue le')->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('customer_name')->label('Client')->searchable()->description(fn (PressingOrder $record) => $record->customer_phone),
                TextColumn::make('mode')
                    ->label('Collecte')
                    ->formatStateUsing(fn ($state, PressingOrder $record) => $state === 'depot' ? 'Dépôt : '.$record->showroom_name : ($record->zone_name ?? 'Collecte'))
                    ->description(fn (PressingOrder $record) => $record->pickup_date ? $record->pickup_date->format('d/m').' · '.$record->pickup_slot : null),
                IconColumn::make('express')->label('Express')->boolean()->trueIcon(Heroicon::Bolt)->falseIcon(Heroicon::Minus)->trueColor('warning'),
                TextColumn::make('total')->label('Total')->sortable()->formatStateUsing(fn ($state) => money($state, 'GNF')),
                SelectColumn::make('status')->label('Statut')->options(PressingStatus::class)->selectablePlaceholder(false),
                TextColumn::make('payment_status')->label('Payé ?')->badge(),
            ])
            ->filters([
                SelectFilter::make('status')->label('Statut')->options(PressingStatus::class),
                SelectFilter::make('mode')->label('Mode')->options(PressingOrder::MODES),
                Filter::make('pickup_today')->label('Collecte aujourd\'hui')->query(fn (Builder $query) => $query->whereDate('pickup_date', today())),
                Filter::make('express')->label('Express')->query(fn (Builder $query) => $query->where('express', true)),
            ])
            ->recordActions([
                Action::make('whatsapp')
                    ->label('WhatsApp')
                    ->icon(Heroicon::OutlinedChatBubbleLeftRight)
                    ->color('success')
                    ->url(fn (PressingOrder $record) => static::whatsappUrl($record), shouldOpenInNewTab: true),
                EditAction::make()->label('Gérer'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function whatsappUrl(PressingOrder $order): string
    {
        $phone = preg_replace('/\D+/', '', $order->customer_phone);
        if (strlen($phone) === 9) {
            $phone = '224'.$phone; // local Guinean number
        }
        $text = "Bonjour {$order->customer_name}, Mombeya Galy Pressing vous contacte au sujet de votre réservation {$order->reference} ({$order->status->getLabel()}).";

        return 'https://wa.me/'.$phone.'?text='.rawurlencode($text);
    }

    public static function getRelations(): array
    {
        return [ItemsRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPressingOrders::route('/'),
            'edit' => EditPressingOrder::route('/{record}/edit'),
        ];
    }
}
