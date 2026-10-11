@if ($lines->isEmpty())
    <div class="py-6 text-center">
        <p class="font-display text-xl text-ink">Votre panier est vide</p>
        <a href="{{ route('shop.index') }}" class="link-arrow mt-3">Découvrir la boutique</a>
    </div>
@else
    <div class="max-h-64 space-y-4 overflow-y-auto pr-1">
        @foreach ($lines as $line)
            <div class="flex items-center gap-3">
                <a href="{{ route('product.show', $line->product) }}" class="shrink-0 bg-sand">
                    <img src="{{ $line->product->mainImageUrl() }}" alt="" class="h-16 w-12 object-cover">
                </a>
                <div class="min-w-0 flex-1 text-sm">
                    <a href="{{ route('product.show', $line->product) }}" class="block truncate text-ink hover:text-brand">{{ $line->product->name }}</a>
                    <span class="text-xs text-muted">{{ collect([$line->size, $line->color])->filter()->join(' · ') }}</span>
                    <div class="text-body">{{ $line->quantity }} × {{ money($line->unit_price) }}</div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="mt-5 flex items-center justify-between border-t border-line pt-4 text-sm">
        <span class="text-muted">Sous-total</span>
        <span class="font-medium text-ink">{{ money($subtotal) }}</span>
    </div>
    <div class="mt-4 grid grid-cols-2 gap-2">
        <a href="{{ route('cart.index') }}" class="btn btn-sm btn-outline">Panier</a>
        <a href="{{ route('checkout.create') }}" class="btn btn-sm btn-primary">Commander</a>
    </div>
@endif
