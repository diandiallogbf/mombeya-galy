<?php

namespace App\Filament\Resources\Orders;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Filament\Resources\Orders\Pages\EditOrder;
use App\Filament\Resources\Orders\Pages\ListOrders;
use App\Filament\Resources\Orders\RelationManagers\ItemsRelationManager;
use App\Models\Order;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingCart;

    protected static string|UnitEnum|null $navigationGroup = 'Ventes';

    protected static ?string $navigationLabel = 'Commandes';

    protected static ?string $modelLabel = 'commande';

    protected static ?string $pluralModelLabel = 'commandes';

    protected static ?string $recordTitleAttribute = 'reference';

    protected static ?int $navigationSort = 1;

    public static function getGloballySearchableAttributes(): array
    {
        return ['reference', 'customer_name', 'customer_phone'];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = Order::where('status', OrderStatus::Pending)->count();

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
                    Section::make('Client & livraison')
                        ->columns(2)
                        ->schema([
                            TextInput::make('customer_name')->label('Nom')->required(),
                            TextInput::make('customer_phone')->label('Téléphone')->required(),
                            TextInput::make('customer_email')->label('Email')->email(),
                            TextInput::make('delivery_zone_name')->label('Zone de livraison')->disabled(),
                            TextInput::make('address')->label('Adresse')->columnSpanFull(),
                            Textarea::make('notes')->label('Note du client')->rows(2)->disabled()->columnSpanFull(),
                        ]),
                ])->columnSpan(['lg' => 2]),

                Group::make([
                    Section::make('Suivi')
                        ->schema([
                            Select::make('status')->label('Statut de la commande')->options(OrderStatus::class)->required()->native(false),
                            Select::make('payment_status')->label('Paiement')->options(PaymentStatus::class)->required()->native(false),
                            TextEntry::make('payment_method')->label('Mode de paiement'),
                            Textarea::make('admin_notes')->label('Notes internes')->rows(3)->helperText('Visible uniquement par l\'équipe.'),
                        ]),
                    Section::make('Montants')
                        ->schema([
                            TextEntry::make('subtotal')->label('Sous-total')->formatStateUsing(fn ($state) => money($state, 'GNF')),
                            TextEntry::make('delivery_fee')->label('Livraison')->formatStateUsing(fn ($state) => $state ? money($state, 'GNF') : 'Gratuit'),
                            TextEntry::make('total')->label('Total')->weight('bold')->color('primary')->formatStateUsing(fn ($state) => money($state, 'GNF')),
                            TextEntry::make('created_at')->label('Passée le')->dateTime('d/m/Y à H:i'),
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
                TextColumn::make('created_at')->label('Date')->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('customer_name')->label('Client')->searchable()->description(fn (Order $record) => $record->customer_phone),
                TextColumn::make('delivery_zone_name')->label('Livraison')->limit(25)->toggleable(),
                TextColumn::make('total')->label('Total')->sortable()->formatStateUsing(fn ($state) => money($state, 'GNF')),
                TextColumn::make('payment_method')->label('Paiement')->badge()->color('gray')->toggleable(),
                SelectColumn::make('status')->label('Statut')->options(OrderStatus::class)->selectablePlaceholder(false),
                TextColumn::make('payment_status')->label('Payé ?')->badge(),
            ])
            ->filters([
                SelectFilter::make('status')->label('Statut')->options(OrderStatus::class),
                SelectFilter::make('payment_status')->label('Paiement')->options(PaymentStatus::class),
                SelectFilter::make('payment_method')->label('Mode de paiement')->options(PaymentMethod::class),
                Filter::make('today')->label('Aujourd\'hui')->query(fn (Builder $query) => $query->whereDate('created_at', today())),
            ])
            ->recordActions([
                Action::make('whatsapp')
                    ->label('WhatsApp')
                    ->icon(Heroicon::OutlinedChatBubbleLeftRight)
                    ->color('success')
                    ->url(fn (Order $record) => static::whatsappUrl($record), shouldOpenInNewTab: true),
                EditAction::make()->label('Gérer'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function whatsappUrl(Order $order): string
    {
        $phone = preg_replace('/\D+/', '', $order->customer_phone);
        if (strlen($phone) === 9) {
            $phone = '224'.$phone; // local Guinean number
        }
        $text = "Bonjour {$order->customer_name}, Mombeya Galy vous contacte au sujet de votre commande {$order->reference} ({$order->status->getLabel()}).";

        return 'https://wa.me/'.$phone.'?text='.rawurlencode($text);
    }

    public static function getRelations(): array
    {
        return [ItemsRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
            'edit' => EditOrder::route('/{record}/edit'),
        ];
    }
}
