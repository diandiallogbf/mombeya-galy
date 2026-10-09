<?php

namespace App\Filament\Resources\PressingOrders\Pages;

use App\Enums\PressingStatus;
use App\Filament\Resources\PressingOrders\PressingOrderResource;
use App\Models\PressingOrder;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListPressingOrders extends ListRecords
{
    protected static string $resource = PressingOrderResource::class;

    public function getTabs(): array
    {
        $tabs = ['toutes' => Tab::make('Toutes')];

        foreach (PressingStatus::cases() as $status) {
            $tabs[$status->value] = Tab::make($status->getLabel())
                ->badge(PressingOrder::where('status', $status)->count() ?: null)
                ->badgeColor($status->getColor())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', $status));
        }

        return $tabs;
    }
}
