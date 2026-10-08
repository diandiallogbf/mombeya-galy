<?php

namespace App\Filament\Widgets;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ShopStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $valid = fn () => Order::where('status', '!=', OrderStatus::Cancelled);

        $monthRevenue = $valid()->where('created_at', '>=', now()->startOfMonth())->sum('total');
        $lastMonthRevenue = $valid()->whereBetween('created_at', [now()->subMonthNoOverflow()->startOfMonth(), now()->subMonthNoOverflow()->endOfMonth()])->sum('total');

        $trend = collect(range(13, 0))
            ->map(fn ($days) => (int) $valid()->whereDate('created_at', today()->subDays($days))->sum('total'))
            ->all();

        return [
            Stat::make('Chiffre d\'affaires du mois', money($monthRevenue, 'GNF'))
                ->description('Mois dernier : '.money($lastMonthRevenue, 'GNF'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->chart($trend)
                ->color('success'),
            Stat::make('Commandes en attente', Order::where('status', OrderStatus::Pending)->count())
                ->description('À confirmer rapidement')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
            Stat::make('Commandes aujourd\'hui', Order::whereDate('created_at', today())->count())
                ->description(Order::where('created_at', '>=', now()->startOfMonth())->count().' ce mois-ci')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('primary'),
            Stat::make('Produits en rupture', Product::where('is_active', true)->where('stock', 0)->count())
                ->description(Product::where('is_active', true)->count().' produits en ligne')
                ->descriptionIcon('heroicon-m-archive-box')
                ->color('danger'),
        ];
    }
}
