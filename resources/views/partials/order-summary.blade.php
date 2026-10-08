{{-- Items + totals of an order (amounts always in GNF, the order currency). --}}
<div class="overflow-hidden rounded-lg border border-line">
    <div class="divide-y divide-line">
        @foreach ($order->items as $item)
            <div class="flex items-center gap-3 p-3 text-sm">
                <img src="{{ $item->imageUrl() }}" alt="" class="h-16 w-12 rounded object-cover">
                <div class="flex-1">
                    <div class="text-ink">{{ $item->product_name }}</div>
                    <div class="text-xs text-muted">{{ collect([$item->size, $item->color])->filter()->join(' · ') }}</div>
                    <div class="text-xs">{{ $item->quantity }} × {{ money($item->unit_price, 'GNF') }}</div>
                </div>
                <div class="text-ink">{{ money($item->total, 'GNF') }}</div>
            </div>
        @endforeach
    </div>
    <dl class="space-y-1 border-t border-line bg-soft p-4 text-sm">
        <div class="flex justify-between"><dt class="text-muted">Sous-total</dt><dd>{{ money($order->subtotal, 'GNF') }}</dd></div>
        <div class="flex justify-between"><dt class="text-muted">Livraison ({{ $order->delivery_zone_name }})</dt><dd>{{ $order->delivery_fee ? money($order->delivery_fee, 'GNF') : 'Gratuit' }}</dd></div>
        <div class="flex justify-between border-t border-line pt-2 text-base"><dt class="font-medium text-ink">Total</dt><dd class="font-medium text-brand">{{ money($order->total, 'GNF') }}</dd></div>
    </dl>
</div>
