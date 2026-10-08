@extends('layouts.app')

@section('title', 'Finaliser la commande')

@section('content')
    <x-page-header title="Finaliser la commande" :breadcrumbs="['Panier' => route('cart.index'), 'Commande' => null]" />

    @php
        $zoneFees = $zones->mapWithKeys(fn ($z) => [$z->id => $z->fee]);
        $zoneLabels = $zones->mapWithKeys(fn ($z) => [$z->id => money($z->fee, 'GNF')]);
    @endphp

    <div class="container-shop">
        <form method="POST" action="{{ route('checkout.store') }}" class="relative -mt-20 grid gap-6 lg:grid-cols-12"
              x-data="{ zone: @js((string) old('delivery_zone_id', '')), method: @js(old('payment_method', 'orange_money')), fees: @js($zoneFees), labels: @js($zoneLabels), subtotal: {{ $subtotal }} }">
            @csrf

            <div class="space-y-6 lg:col-span-8">
                @if ($errors->has('cart'))
                    <div class="rounded-lg bg-danger/10 px-4 py-3 text-sm text-danger">{{ $errors->first('cart') }}</div>
                @endif

                {{-- Customer --}}
                <section class="rounded-lg bg-white p-5 shadow-lg md:p-7">
                    <h2 class="mb-5 flex items-center gap-3 text-lg"><span class="flex size-8 items-center justify-center rounded-full bg-brand text-sm text-white">1</span> Vos coordonnées</h2>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="form-label" for="customer_name">Nom complet *</label>
                            <input id="customer_name" name="customer_name" value="{{ old('customer_name', $user?->name) }}" required class="form-control" placeholder="Ex : Mamadou Diallo">
                            @error('customer_name')<p class="form-error">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="form-label" for="customer_phone">Téléphone *</label>
                            <input id="customer_phone" name="customer_phone" type="tel" value="{{ old('customer_phone', $user?->phone) }}" required class="form-control" placeholder="+224 6XX XX XX XX">
                            @error('customer_phone')<p class="form-error">{{ $message }}</p>@enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label class="form-label" for="customer_email">Email <span class="font-normal text-muted">(optionnel)</span></label>
                            <input id="customer_email" name="customer_email" type="email" value="{{ old('customer_email', $user?->email) }}" class="form-control" placeholder="vous@exemple.com">
                            @error('customer_email')<p class="form-error">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </section>

                {{-- Delivery --}}
                <section class="rounded-lg bg-white p-5 shadow-lg md:p-7">
                    <h2 class="mb-5 flex items-center gap-3 text-lg"><span class="flex size-8 items-center justify-center rounded-full bg-brand text-sm text-white">2</span> Livraison</h2>
                    <div class="mb-4 grid gap-3 sm:grid-cols-2">
                        @foreach ($zones as $zone)
                            <label class="flex cursor-pointer items-start gap-3 rounded-lg border p-3 transition"
                                   :class="zone === '{{ $zone->id }}' ? 'border-brand bg-brand-light/50' : 'border-line hover:border-muted'">
                                <input type="radio" name="delivery_zone_id" value="{{ $zone->id }}" x-model="zone" class="mt-1 accent-[#e0144f]" required>
                                <span class="flex-1 text-sm">
                                    <span class="block font-medium text-ink">{{ $zone->name }}</span>
                                    <span class="text-muted">{{ $zone->delay }}</span>
                                </span>
                                <span class="text-sm font-medium text-brand">{{ $zone->fee ? money($zone->fee, 'GNF') : 'Gratuit' }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('delivery_zone_id')<p class="form-error mb-3">{{ $message }}</p>@enderror
                    <div>
                        <label class="form-label" for="address">Adresse de livraison *</label>
                        <input id="address" name="address" value="{{ old('address') }}" required class="form-control" placeholder="Quartier, rue, point de repère (ou showroom de retrait)">
                        @error('address')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="mt-4">
                        <label class="form-label" for="notes">Note pour la commande <span class="font-normal text-muted">(optionnel)</span></label>
                        <textarea id="notes" name="notes" rows="3" class="form-control" placeholder="Retouches, mesures, horaires de livraison…">{{ old('notes') }}</textarea>
                    </div>
                </section>

                {{-- Payment --}}
                <section class="rounded-lg bg-white p-5 shadow-lg md:p-7">
                    <h2 class="mb-5 flex items-center gap-3 text-lg"><span class="flex size-8 items-center justify-center rounded-full bg-brand text-sm text-white">3</span> Mode de paiement</h2>
                    <div class="space-y-3">
                        @foreach ($methods as $method)
                            <label class="flex cursor-pointer items-start gap-3 rounded-lg border p-4 transition"
                                   :class="method === '{{ $method->value }}' ? 'border-brand bg-brand-light/50' : 'border-line hover:border-muted'">
                                <input type="radio" name="payment_method" value="{{ $method->value }}" x-model="method" class="mt-1 accent-[#e0144f]">
                                <span class="text-sm">
                                    <span class="block font-medium text-ink">{{ $method->getLabel() }}</span>
                                    <span class="text-muted">{{ $method->description() }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                    @error('payment_method')<p class="form-error">{{ $message }}</p>@enderror
                </section>
            </div>

            {{-- Summary --}}
            <aside class="lg:col-span-4">
                <div class="rounded-lg bg-white p-6 shadow-lg lg:sticky lg:top-6">
                    <h3 class="mb-4 text-lg">Récapitulatif</h3>
                    <div class="max-h-72 space-y-3 overflow-y-auto border-b border-line pb-4">
                        @foreach ($lines as $line)
                            <div class="flex gap-3 text-sm">
                                <img src="{{ $line->product->mainImageUrl() }}" alt="" class="h-16 w-12 rounded object-cover">
                                <div class="flex-1">
                                    <div class="text-ink">{{ $line->product->name }}</div>
                                    <div class="text-xs text-muted">{{ collect([$line->size, $line->color])->filter()->join(' · ') }}</div>
                                    <div class="text-xs">{{ $line->quantity }} × {{ money($line->unit_price, 'GNF') }}</div>
                                </div>
                                <div class="text-ink">{{ money($line->total, 'GNF') }}</div>
                            </div>
                        @endforeach
                    </div>
                    <dl class="space-y-2 py-4 text-sm">
                        <div class="flex justify-between"><dt class="text-muted">Sous-total</dt><dd>{{ money($subtotal, 'GNF') }}</dd></div>
                        <div class="flex justify-between"><dt class="text-muted">Livraison</dt><dd x-text="zone ? (fees[zone] ? labels[zone] : 'Gratuit') : '—'">—</dd></div>
                    </dl>
                    <div class="mb-1 flex items-baseline justify-between border-t border-line pt-4">
                        <span class="font-medium text-ink">Total</span>
                        <span class="text-2xl text-brand" x-text="new Intl.NumberFormat('de-DE').format(subtotal + (fees[zone] || 0)) + ' GNF'">{{ money($subtotal, 'GNF') }}</span>
                    </div>
                    <p class="mb-5 text-xs text-muted">Montant facturé en francs guinéens (GNF).</p>
                    <button class="btn btn-primary btn-shadow w-full"><i class="fa-solid fa-check"></i> Confirmer la commande</button>
                    <p class="mt-3 text-center text-xs text-muted">Un conseiller vous contacte pour confirmer la commande.</p>
                </div>
            </aside>
        </form>
    </div>
@endsection
