@extends('layouts.app')

@section('title', 'Panier')

@section('content')
    <x-page-header title="Votre panier" :breadcrumbs="['Panier' => null]" />

    <div class="container-shop mt-12">
        @if ($lines->isEmpty())
            <div class="py-16 text-center">
                <i class="fa-solid fa-bag-shopping mb-6 text-5xl text-line"></i>
                <p class="mb-3 font-display text-3xl text-ink">Le panier est vide</p>
                <p class="mb-8 text-muted">Découvrez nos collections et laissez-vous inspirer.</p>
                <a href="{{ route('shop.index') }}" class="btn btn-dark">Découvrir la boutique</a>
            </div>
        @else
            <div class="grid gap-12 lg:grid-cols-12">
                <section class="lg:col-span-8">
                    <div class="mb-2 flex items-center justify-between border-b border-ink pb-3 text-[.7rem] tracking-[.16em] text-ink uppercase">
                        <span>{{ $lines->sum('quantity') }} article{{ $lines->sum('quantity') > 1 ? 's' : '' }}</span>
                        <a href="{{ route('shop.index') }}" class="hover:text-brand"><i class="fa-solid fa-arrow-left mr-1"></i> Poursuivre vos achats</a>
                    </div>
                    <div class="divide-y divide-line">
                        @foreach ($lines as $line)
                            <div class="flex gap-5 py-6">
                                <a href="{{ route('product.show', $line->product) }}" class="shrink-0 bg-sand">
                                    <img src="{{ $line->product->mainImageUrl() }}" alt="{{ $line->product->name }}" class="h-40 w-30 object-cover md:h-44 md:w-33">
                                </a>
                                <div class="flex flex-1 flex-col">
                                    <div class="flex flex-wrap justify-between gap-3">
                                        <div>
                                            @if ($line->product->category)<p class="mb-1 text-[.65rem] tracking-[.18em] text-muted uppercase">{{ $line->product->category->name }}</p>@endif
                                            <h3 class="font-display text-xl"><a href="{{ route('product.show', $line->product) }}" class="text-ink hover:text-brand">{{ $line->product->name }}</a></h3>
                                            <p class="mt-1 text-sm text-muted">{{ collect([$line->size ? 'Taille '.$line->size : null, $line->color])->filter()->join(' · ') }}</p>
                                        </div>
                                        <p class="text-ink">{{ money($line->total) }}</p>
                                    </div>
                                    <div class="mt-auto flex flex-wrap items-center justify-between gap-4 pt-4">
                                        <form method="POST" action="{{ route('cart.update', $line->key) }}" class="flex items-center gap-3">
                                            @csrf @method('PATCH')
                                            <label class="text-[.7rem] tracking-[.14em] text-muted uppercase" for="qty-{{ $line->key }}">Quantité</label>
                                            <input id="qty-{{ $line->key }}" type="number" name="quantity" min="1" max="{{ config('shop.max_quantity') }}" value="{{ $line->quantity }}" class="form-control w-20 py-2 text-center" onchange="this.form.submit()">
                                        </form>
                                        <form method="POST" action="{{ route('cart.destroy', $line->key) }}">
                                            @csrf @method('DELETE')
                                            <button class="text-[.7rem] tracking-[.14em] text-muted uppercase underline-offset-4 hover:text-danger hover:underline">Retirer</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>

                <aside class="lg:col-span-4">
                    <div class="panel lg:sticky lg:top-36">
                        <h2 class="mb-6 font-display text-2xl">Récapitulatif</h2>
                        <div class="flex items-baseline justify-between border-b border-line pb-4">
                            <span class="text-sm text-body">Sous-total</span>
                            <span class="text-xl text-ink">{{ money($subtotal) }}</span>
                        </div>
                        <p class="mt-4 mb-6 text-xs text-muted">Frais de livraison calculés à l'étape suivante selon votre zone. Retrait gratuit en maison.</p>
                        <a href="{{ route('checkout.create') }}" class="btn btn-primary w-full">Passer la commande</a>
                        <a href="{{ whatsapp_url('Bonjour Mombeya Galy, je souhaite commander : '.$lines->map(fn ($l) => $l->quantity.' × '.$l->product->name.($l->size ? ' ('.$l->size.')' : ''))->join(', ')) }}"
                           target="_blank" rel="noopener" class="mt-3 flex items-center justify-center gap-2 py-2 text-[.72rem] tracking-[.16em] text-ink uppercase hover:text-[#128C7E]">
                            <i class="fa-brands fa-whatsapp text-base"></i> Commander via WhatsApp
                        </a>
                        <ul class="mt-6 space-y-2 border-t border-line pt-5 text-xs text-muted">
                            <li><i class="fa-solid fa-lock mr-2 text-ink"></i> Paiement sécurisé</li>
                            <li><i class="fa-solid fa-truck-fast mr-2 text-ink"></i> {{ setting('delivery_text') }}</li>
                        </ul>
                    </div>
                </aside>
            </div>
        @endif
    </div>
@endsection
