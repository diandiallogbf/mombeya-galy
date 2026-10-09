@extends('layouts.app')

@section('title', 'Réserver un pressing')

@section('content')
    <x-page-header title="Réserver un pressing" :breadcrumbs="['Pressing' => route('pressing.index'), 'Réservation' => null]" />

    @php
        $oldItems = old('items', $preselected ? [$preselected => 1] : []);
        $qty = $services->mapWithKeys(fn ($s) => [$s->id => (int) ($oldItems[$s->id] ?? 0)]);
        $prices = $services->mapWithKeys(fn ($s) => [$s->id => $s->price]);
        $names = $services->mapWithKeys(fn ($s) => [$s->id => $s->name]);
        $fees = $zones->mapWithKeys(fn ($z) => [$z->id => $z->fee]);
        $defaultMode = $zones->isEmpty() ? 'depot' : 'collecte';
    @endphp

    <div class="container-shop">
        <form method="POST" action="{{ route('pressing.store') }}" class="relative -mt-20 grid gap-6 lg:grid-cols-12"
              x-data="{
                  mode: @js(old('mode', $defaultMode)),
                  zone: @js((string) old('pressing_zone_id', $zones->count() === 1 ? $zones->first()->id : '')),
                  express: {{ old('express') ? 'true' : 'false' }},
                  qty: @js($qty),
                  prices: @js($prices),
                  names: @js($names),
                  fees: @js($fees),
                  percent: {{ $expressPercent }},
                  get subtotal() { return Object.keys(this.qty).reduce((sum, id) => sum + this.qty[id] * this.prices[id], 0) },
                  get count() { return Object.values(this.qty).reduce((a, b) => a + b, 0) },
                  get expressFee() { return this.express ? Math.round(this.subtotal * this.percent / 100) : 0 },
                  get fee() { return this.mode === 'collecte' && this.zone ? (this.fees[this.zone] || 0) : 0 },
                  get total() { return this.subtotal + this.expressFee + this.fee },
                  fmt(n) { return new Intl.NumberFormat('de-DE').format(n) + ' GNF' },
                  inc(id) { if (this.qty[id] < 50) this.qty[id]++ },
                  dec(id) { if (this.qty[id] > 0) this.qty[id]-- },
              }">
            @csrf

            <div class="space-y-6 lg:col-span-8">
                {{-- 1. Articles --}}
                <section class="rounded-lg bg-white p-5 shadow-lg md:p-7">
                    <h2 class="mb-1 flex items-center gap-3 text-lg"><span class="flex size-8 items-center justify-center rounded-full bg-brand text-sm text-white">1</span> Vos articles</h2>
                    <p class="mb-5 text-sm text-muted">Indiquez le nombre de pièces. Le montant est confirmé à la collecte.</p>
                    @error('items')<p class="form-error mb-3">{{ $message }}</p>@enderror

                    <div class="space-y-6">
                        @foreach ($servicesByCategory as $key => $group)
                            <div>
                                <h3 class="mb-2 text-sm font-medium uppercase tracking-wide text-muted">{{ \App\Models\PressingService::CATEGORIES[$key] }}</h3>
                                <div class="divide-y divide-line rounded-lg border border-line">
                                    @foreach ($group as $service)
                                        <div class="flex items-center gap-3 px-4 py-3" :class="qty[{{ $service->id }}] > 0 && 'bg-brand-light/40'">
                                            <i class="{{ $service->icon ?: 'fa-solid fa-shirt' }} w-6 text-center text-ocean"></i>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-sm text-ink">{{ $service->name }}</p>
                                                <p class="text-xs text-brand">{{ money($service->price, 'GNF') }} / {{ $service->unit }}</p>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <button type="button" @click="dec({{ $service->id }})" class="flex size-8 items-center justify-center rounded-full border border-line text-ink hover:border-brand hover:text-brand" aria-label="Retirer"><i class="fa-solid fa-minus text-xs"></i></button>
                                                <span class="w-6 text-center text-sm font-medium text-ink" x-text="qty[{{ $service->id }}]">0</span>
                                                <button type="button" @click="inc({{ $service->id }})" class="flex size-8 items-center justify-center rounded-full border border-line text-ink hover:border-brand hover:text-brand" aria-label="Ajouter"><i class="fa-solid fa-plus text-xs"></i></button>
                                                <input type="hidden" name="items[{{ $service->id }}]" :value="qty[{{ $service->id }}]">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <label class="mt-5 flex cursor-pointer items-start gap-3 rounded-lg border p-4 transition" :class="express ? 'border-brand bg-brand-light/50' : 'border-line'">
                        <input type="checkbox" name="express" value="1" x-model="express" class="mt-1 accent-[#e0144f]">
                        <span class="text-sm">
                            <span class="block font-medium text-ink"><i class="fa-solid fa-bolt text-[#fea569]"></i> Option express 24h</span>
                            <span class="text-muted">Supplément de {{ $expressPercent }} % sur les articles.</span>
                        </span>
                    </label>
                </section>

                {{-- 2. Collecte ou dépôt --}}
                <section class="rounded-lg bg-white p-5 shadow-lg md:p-7">
                    <h2 class="mb-5 flex items-center gap-3 text-lg"><span class="flex size-8 items-center justify-center rounded-full bg-brand text-sm text-white">2</span> Collecte ou dépôt</h2>
                    <div class="mb-5 grid gap-3 sm:grid-cols-2">
                        <label @class(['flex items-start gap-3 rounded-lg border p-4 transition', 'cursor-pointer' => $zones->isNotEmpty(), 'cursor-not-allowed opacity-50' => $zones->isEmpty()])
                               :class="mode === 'collecte' ? 'border-brand bg-brand-light/50' : 'border-line'">
                            <input type="radio" name="mode" value="collecte" x-model="mode" class="mt-1 accent-[#e0144f]" @disabled($zones->isEmpty())>
                            <span class="text-sm">
                                <span class="block font-medium text-ink"><i class="fa-solid fa-truck-pickup text-brand"></i> Collecte à domicile</span>
                                <span class="text-muted">{{ $zones->isEmpty() ? 'Bientôt disponible' : 'On passe chez vous : '.$zones->pluck('name')->join(', ') }}</span>
                            </span>
                        </label>
                        <label class="flex cursor-pointer items-start gap-3 rounded-lg border p-4 transition" :class="mode === 'depot' ? 'border-brand bg-brand-light/50' : 'border-line'">
                            <input type="radio" name="mode" value="depot" x-model="mode" class="mt-1 accent-[#e0144f]">
                            <span class="text-sm">
                                <span class="block font-medium text-ink"><i class="fa-solid fa-store text-brand"></i> Dépôt en showroom</span>
                                <span class="text-muted">Gratuit, dans le showroom de votre choix</span>
                            </span>
                        </label>
                    </div>
                    @error('mode')<p class="form-error mb-3">{{ $message }}</p>@enderror

                    <div x-show="mode === 'collecte'" x-cloak class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="form-label" for="pressing_zone_id">Zone *</label>
                            <select id="pressing_zone_id" name="pressing_zone_id" x-model="zone" class="form-control" :disabled="mode !== 'collecte'">
                                <option value="">Choisissez votre zone</option>
                                @foreach ($zones as $zone)
                                    <option value="{{ $zone->id }}">{{ $zone->name }} ({{ $zone->fee ? money($zone->fee, 'GNF') : 'gratuit' }})</option>
                                @endforeach
                            </select>
                            @error('pressing_zone_id')<p class="form-error">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="form-label" for="address">Adresse *</label>
                            <input id="address" name="address" value="{{ old('address') }}" class="form-control" placeholder="Quartier, rue, point de repère" :disabled="mode !== 'collecte'">
                            @error('address')<p class="form-error">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="form-label" for="pickup_date">Date de collecte *</label>
                            <input id="pickup_date" type="date" name="pickup_date" value="{{ old('pickup_date', now()->addDay()->toDateString()) }}" min="{{ now()->toDateString() }}" class="form-control" :disabled="mode !== 'collecte'">
                            @error('pickup_date')<p class="form-error">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="form-label" for="pickup_slot">Créneau *</label>
                            <select id="pickup_slot" name="pickup_slot" class="form-control" :disabled="mode !== 'collecte'">
                                @foreach ($slots as $slot)
                                    <option value="{{ $slot }}" @selected(old('pickup_slot') === $slot)>{{ $slot }}</option>
                                @endforeach
                            </select>
                            @error('pickup_slot')<p class="form-error">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div x-show="mode === 'depot'" x-cloak>
                        <label class="form-label" for="showroom_id">Showroom de dépôt *</label>
                        <select id="showroom_id" name="showroom_id" class="form-control" :disabled="mode !== 'depot'">
                            <option value="">Choisissez un showroom</option>
                            @foreach ($dropPoints as $point)
                                <option value="{{ $point->id }}" @selected((int) old('showroom_id') === $point->id)>{{ $point->name }}{{ $point->address ? ' – '.$point->address : '' }}</option>
                            @endforeach
                        </select>
                        @error('showroom_id')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                </section>

                {{-- 3. Coordonnées & paiement --}}
                <section class="rounded-lg bg-white p-5 shadow-lg md:p-7">
                    <h2 class="mb-5 flex items-center gap-3 text-lg"><span class="flex size-8 items-center justify-center rounded-full bg-brand text-sm text-white">3</span> Vos coordonnées et le paiement</h2>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="form-label" for="customer_name">Nom complet *</label>
                            <input id="customer_name" name="customer_name" value="{{ old('customer_name', $user?->name) }}" required class="form-control">
                            @error('customer_name')<p class="form-error">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="form-label" for="customer_phone">Téléphone *</label>
                            <input id="customer_phone" name="customer_phone" type="tel" value="{{ old('customer_phone', $user?->phone) }}" required class="form-control" placeholder="+224 6XX XX XX XX">
                            @error('customer_phone')<p class="form-error">{{ $message }}</p>@enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label class="form-label" for="customer_email">Email <span class="font-normal text-muted">(optionnel)</span></label>
                            <input id="customer_email" name="customer_email" type="email" value="{{ old('customer_email', $user?->email) }}" class="form-control">
                            @error('customer_email')<p class="form-error">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="mt-5 space-y-3">
                        @foreach (\App\Http\Controllers\PressingController::PAYMENT_METHODS as $value => $label)
                            <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-line p-3 text-sm has-[:checked]:border-brand has-[:checked]:bg-brand-light/50">
                                <input type="radio" name="payment_method" value="{{ $value }}" @checked(old('payment_method', 'livraison') === $value) class="accent-[#e0144f]">
                                <span class="font-medium text-ink">{{ $label }}</span>
                            </label>
                        @endforeach
                        @error('payment_method')<p class="form-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="mt-5">
                        <label class="form-label" for="notes">Précisions <span class="font-normal text-muted">(optionnel)</span></label>
                        <textarea id="notes" name="notes" rows="3" class="form-control" placeholder="Taches particulières, tissus délicats, instructions…">{{ old('notes') }}</textarea>
                    </div>
                </section>
            </div>

            {{-- Summary --}}
            <aside class="lg:col-span-4">
                <div class="rounded-lg bg-white p-6 shadow-lg lg:sticky lg:top-6">
                    <h3 class="mb-4 text-lg">Récapitulatif</h3>
                    <div class="max-h-64 space-y-2 overflow-y-auto border-b border-line pb-4 text-sm">
                        <template x-for="id in Object.keys(qty).filter(id => qty[id] > 0)" :key="id">
                            <div class="flex justify-between gap-3">
                                <span class="text-body"><span x-text="qty[id]"></span> × <span x-text="names[id]"></span></span>
                                <span class="whitespace-nowrap text-ink" x-text="fmt(qty[id] * prices[id])"></span>
                            </div>
                        </template>
                        <p x-show="count === 0" class="text-muted">Aucun article pour le moment.</p>
                    </div>
                    <dl class="space-y-2 py-4 text-sm">
                        <div class="flex justify-between"><dt class="text-muted">Sous-total</dt><dd x-text="fmt(subtotal)"></dd></div>
                        <div class="flex justify-between" x-show="express"><dt class="text-muted">Express</dt><dd x-text="fmt(expressFee)"></dd></div>
                        <div class="flex justify-between"><dt class="text-muted">Collecte</dt><dd x-text="mode === 'depot' ? 'Gratuit' : (zone ? (fee ? fmt(fee) : 'Gratuit') : '—')"></dd></div>
                    </dl>
                    <div class="mb-1 flex items-baseline justify-between border-t border-line pt-4">
                        <span class="font-medium text-ink">Total estimé</span>
                        <span class="text-2xl text-brand" x-text="fmt(total)">0 GNF</span>
                    </div>
                    <p class="mb-5 text-xs text-muted">Montant confirmé à la collecte, en francs guinéens (GNF).</p>
                    <button class="btn btn-primary btn-shadow w-full" :disabled="count === 0"><i class="fa-solid fa-check"></i> Confirmer la réservation</button>
                    <a href="{{ whatsapp_url('Bonjour Mombeya Galy Pressing, je souhaite réserver un enlèvement.') }}" target="_blank" rel="noopener" class="btn mt-3 w-full border border-[#25D366] text-[#128C7E] hover:bg-[#25D366] hover:text-white">
                        <i class="fa-brands fa-whatsapp"></i> Réserver via WhatsApp
                    </a>
                </div>
            </aside>
        </form>
    </div>
@endsection
