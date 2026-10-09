{{-- Items, pick-up details and totals of a pressing order (amounts in GNF). --}}
<div class="overflow-hidden rounded-lg border border-line">
    <div class="divide-y divide-line">
        @foreach ($order->items as $item)
            <div class="flex items-center gap-3 p-3 text-sm">
                <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-azure/15 text-ocean"><i class="fa-solid fa-shirt"></i></span>
                <div class="flex-1">
                    <div class="text-ink">{{ $item->service_name }}</div>
                    <div class="text-xs">{{ $item->quantity }} × {{ money($item->unit_price, 'GNF') }}</div>
                </div>
                <div class="text-ink">{{ money($item->total, 'GNF') }}</div>
            </div>
        @endforeach
    </div>
    <dl class="space-y-1 border-t border-line bg-soft p-4 text-sm">
        <div class="flex justify-between"><dt class="text-muted">Sous-total</dt><dd>{{ money($order->subtotal, 'GNF') }}</dd></div>
        @if ($order->express)
            <div class="flex justify-between"><dt class="text-muted">Option express</dt><dd>{{ money($order->express_fee, 'GNF') }}</dd></div>
        @endif
        <div class="flex justify-between"><dt class="text-muted">{{ $order->mode === 'collecte' ? 'Collecte et livraison' : 'Dépôt en showroom' }}</dt><dd>{{ $order->collection_fee ? money($order->collection_fee, 'GNF') : 'Gratuit' }}</dd></div>
        <div class="flex justify-between border-t border-line pt-2 text-base"><dt class="font-medium text-ink">Total</dt><dd class="font-medium text-brand">{{ money($order->total, 'GNF') }}</dd></div>
    </dl>
</div>

{{-- $showPlace = false on the public tracking page: the address stays private. --}}
<div class="mt-4 grid gap-3 text-sm sm:grid-cols-2">
    <div class="rounded-lg bg-soft p-4">
        <p class="mb-1 font-medium text-ink"><i class="fa-solid fa-location-dot mr-1 text-brand"></i> {{ $order->modeLabel() }}</p>
        <p class="text-body">{{ ($showPlace ?? true) ? $order->placeLabel() : ($order->mode === 'depot' ? $order->showroom_name : $order->zone_name) }}</p>
        @if ($order->pickup_date)
            <p class="text-muted">Le {{ $order->pickup_date->translatedFormat('l d F Y') }} · {{ $order->pickup_slot }}</p>
        @endif
    </div>
    <div class="rounded-lg bg-soft p-4">
        <p class="mb-1 font-medium text-ink"><i class="fa-solid fa-wallet mr-1 text-brand"></i> Paiement</p>
        <p class="text-body">{{ \App\Http\Controllers\PressingController::paymentLabel($order->payment_method) }}</p>
        <p class="text-muted">{{ $order->payment_status->getLabel() }}</p>
    </div>
</div>
