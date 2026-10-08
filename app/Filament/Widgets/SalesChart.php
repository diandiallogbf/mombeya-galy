<?php

namespace App\Filament\Widgets;

use App\Enums\OrderStatus;
use App\Models\Order;
use Filament\Widgets\ChartWidget;

class SalesChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Ventes des 30 derniers jours (GNF)';

    protected ?string $maxHeight = '260px';

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $days = collect(range(29, 0))->map(fn ($d) => today()->subDays($d));

        $totals = Order::where('status', '!=', OrderStatus::Cancelled)
            ->where('created_at', '>=', today()->subDays(29))
            ->get(['total', 'created_at'])
            ->groupBy(fn (Order $order) => $order->created_at->toDateString())
            ->map(fn ($orders) => $orders->sum('total'));

        return [
            'datasets' => [
                [
                    'label' => 'Ventes',
                    'data' => $days->map(fn ($day) => $totals[$day->toDateString()] ?? 0)->all(),
                    'borderColor' => '#e0144f',
                    'backgroundColor' => 'rgba(224, 20, 79, 0.12)',
                    'fill' => true,
                    'tension' => 0.35,
                ],
            ],
            'labels' => $days->map(fn ($day) => $day->format('d/m'))->all(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
