<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestOrders extends TableWidget
{
    protected static ?int $sort = 3;

    protected static ?string $heading = 'Dernières commandes';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Order::query()->latest()->limit(8))
            ->paginated(false)
            ->columns([
                TextColumn::make('reference')->label('Référence')->fontFamily('mono'),
                TextColumn::make('created_at')->label('Date')->since(),
                TextColumn::make('customer_name')->label('Client')->description(fn (Order $record) => $record->customer_phone),
                TextColumn::make('total')->label('Total')->formatStateUsing(fn ($state) => money($state, 'GNF')),
                TextColumn::make('status')->label('Statut')->badge(),
                TextColumn::make('payment_status')->label('Paiement')->badge(),
            ])
            ->recordActions([
                Action::make('open')
                    ->label('Gérer')
                    ->url(fn (Order $record) => OrderResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}
