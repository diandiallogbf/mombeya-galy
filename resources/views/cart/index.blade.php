@extends('layouts.app')

@section('title', 'Panier')

@section('content')
    <x-page-header title="Panier" :breadcrumbs="['Boutique' => route('shop.index'), 'Panier' => null]" />

    <div class="container-shop">
        <div class="relative -mt-20 grid gap-6 lg:grid-cols-12">
            <section class="lg:col-span-8">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-base font-medium text-white">Produits ({{ $lines->sum('quantity') }})</h2>
                    <a href="{{ route('shop.index') }}" class="btn btn-sm border border-white/40 text-white hover:bg-white hover:text-ink"><i class="fa-solid fa-chevron-left text-xs"></i> Poursuivre vos achats</a>
                </div>

                <div class="rounded-lg bg-white p-4 shadow-lg md:p-6">
                    @if ($lines->isEmpty())
                        <div class="py-12 text-center">
                            <i class="fa-solid fa-cart-shopping mb-4 text-5xl text-line"></i>
                            <p class="mb-6 rounded-lg bg-danger/10 px-4 py-3 text-danger">Le panier est vide</p>
                            <a href="{{ route('shop.index') }}" class="btn btn-primary">Découvrir la boutique</a>
                        </div>
                    @else
                        <div class="divide-y divide-line">
                            @foreach ($lines as $line)
                                <div class="flex flex-col gap-4 py-5 first:pt-0 last:pb-0 sm:flex-row sm:items-center">
                                    <a href="{{ route('product.show', $line->product) }}" class="shrink-0">
                                        <img src="{{ $line->product->mainImageUrl() }}" alt="{{ $line->product->name }}" class="h-36 w-28 rounded-lg object-cover">
                                    </a>
                                    <div class="flex-1">
                                        <h3 class="mb-1 text-base"><a href="{{ route('product.show', $line->product) }}" class="text-ink hover:text-brand">{{ $line->product->name }}</a></h3>
                                        @if ($line->size)<div class="text-sm"><span class="text-muted">Taille :</span> {{ $line->size }}</div>@endif
                                        @if ($line->color)<div class="text-sm"><span class="text-muted">Couleur :</span> {{ $line->color }}</div>@endif
                                        <div class="mt-1 text-lg text-brand">{{ money($line->unit_price) }}</div>
                                    </div>
                                    <div class="flex items-center gap-4 sm:flex-col sm:items-end">
                                        <form method="POST" action="{{ route('cart.update', $line->key) }}" class="flex items-center gap-2">
                                            @csrf @method('PATCH')
                                            <label class="text-sm text-muted" for="qty-{{ $line->key }}">Quantité</label>
                                            <input id="qty-{{ $line->key }}" type="number" name="quantity" min="1" max="{{ config('shop.max_quantity') }}" value="{{ $line->quantity }}" class="form-control w-20 py-1.5 text-center" onchange="this.form.submit()">
                                        </form>
                                        <form method="POST" action="{{ route('cart.destroy', $line->key) }}">
                                            @csrf @method('DELETE')
                                            <button class="text-sm text-danger hover:underline"><i class="fa-regular fa-trash-can"></i> Retirer</button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </section>

            @if ($lines->isNotEmpty())
                <aside class="lg:col-span-4 lg:pt-14">
                    <div class="rounded-lg bg-white p-6 shadow-lg lg:sticky lg:top-6">
                        <h3 class="mb-4 text-center text-sm text-muted">Sous-total</h3>
                        <p class="mb-6 text-center text-3xl font-normal text-ink">{{ money($subtotal) }}</p>
                        <p class="mb-6 text-center text-xs text-muted">Frais de livraison calculés à l'étape suivante selon votre zone.</p>
                        <a href="{{ route('checkout.create') }}" class="btn btn-primary btn-shadow w-full"><i class="fa-solid fa-credit-card"></i> Passer la commande</a>
                        <a href="{{ whatsapp_url('Bonjour Mombeya Galy, je souhaite commander : '.$lines->map(fn ($l) => $l->quantity.' × '.$l->product->name.($l->size ? ' ('.$l->size.')' : ''))->join(', ')) }}"
                           target="_blank" rel="noopener" class="btn mt-3 w-full border border-[#25D366] text-[#128C7E] hover:bg-[#25D366] hover:text-white">
                            <i class="fa-brands fa-whatsapp"></i> Commander via WhatsApp
                        </a>
                        <div class="mt-6 space-y-2 border-t border-line pt-4 text-xs text-muted">
                            <p><i class="fa-solid fa-lock mr-1 text-success"></i> Paiement sécurisé</p>
                            <p><i class="fa-solid fa-truck-fast mr-1 text-success"></i> {{ setting('delivery_text') }}</p>
                        </div>
                    </div>
                </aside>
            @endif
        </div>
    </div>
@endsection
