@extends('layouts.app')

@section('title', $product->name)
@section('description', \Illuminate\Support\Str::limit(strip_tags($product->description), 155))
@section('og_image', $product->mainImageUrl())

@section('content')
    @php
        $crumbs = ['Boutique' => route('shop.index')];
        if ($product->category) {
            $crumbs[$product->category->name] = route('shop.category', $product->category);
        }
        $crumbs[$product->name] = null;
        $images = $product->imageUrls();
    @endphp

    <x-page-header :title="$product->name" :breadcrumbs="$crumbs" />

    <div class="container-shop">
        <div class="page-card">
            <div class="grid gap-8 lg:grid-cols-12">
                {{-- Gallery --}}
                <div class="lg:col-span-7" x-data="gallery(@js($images))">
                    <div class="flex flex-col-reverse gap-3 sm:flex-row">
                        @if (count($images) > 1)
                            <div class="flex gap-2 sm:flex-col">
                                @foreach ($images as $i => $image)
                                    <button type="button" @click="active = {{ $i }}" :class="active === {{ $i }} ? 'border-brand' : 'border-line'"
                                            class="size-20 shrink-0 overflow-hidden rounded-[.3125rem] border-2 bg-soft transition">
                                        <img src="{{ $image }}" alt="" class="size-full object-cover">
                                    </button>
                                @endforeach
                            </div>
                        @endif
                        <div class="relative flex-1 overflow-hidden rounded-lg bg-soft"
                             @mouseenter="zoom = true" @mouseleave="zoom = false" @mousemove="move($event)">
                            @if ($product->hasDiscount())
                                <span class="promo-badge">Promo</span>
                            @endif
                            <img :src="images[active]" src="{{ $images[0] }}" alt="{{ $product->name }}"
                                 class="aspect-[3/4] w-full cursor-zoom-in object-cover transition-transform duration-200"
                                 :style="zoom ? `transform: scale(1.8); transform-origin: ${origin}` : ''">
                        </div>
                    </div>
                </div>

                {{-- Details --}}
                <div class="lg:col-span-5">
                    @if ($product->category)
                        <a href="{{ route('shop.category', $product->category) }}" class="mb-1 inline-block text-sm text-muted hover:text-brand">{{ $product->category->name }}</a>
                    @endif
                    <h2 class="mb-3 text-2xl text-ink">{{ $product->name }}</h2>

                    @include('product.partials.buy-box')

                    @if ($product->category?->slug !== 'accessoires')
                        <a href="{{ route('pressing.index') }}" class="mt-4 flex items-center gap-3 rounded-lg bg-azure/10 p-3 text-sm text-ocean transition hover:bg-azure/20">
                            <i class="fa-solid fa-soap text-lg"></i>
                            <span><strong>Entretien conseillé :</strong> confiez cette tenue à Mombeya Galy Pressing.</span>
                            <i class="fa-solid fa-chevron-right ml-auto text-xs"></i>
                        </a>
                    @endif

                    {{-- Accordion --}}
                    <div class="mt-6 divide-y divide-line border-y border-line" x-data="{ panel: 'info' }">
                        <div>
                            <button type="button" @click="panel = panel === 'info' ? null : 'info'" class="flex w-full items-center justify-between py-4 text-left font-medium text-ink">
                                <span><i class="fa-solid fa-bullhorn mr-2 text-brand"></i>Informations produit</span>
                                <i class="fa-solid fa-chevron-down text-xs transition" :class="panel === 'info' && 'rotate-180'"></i>
                            </button>
                            <div x-show="panel === 'info'" x-collapse>
                                <div class="pb-4 text-sm">
                                    <h6 class="mb-1 text-ink">Composition</h6>
                                    <div class="prose-shop">{!! $product->description !!}</div>
                                    <p class="mt-3 text-muted">Référence : {{ $product->reference }}</p>
                                </div>
                            </div>
                        </div>
                        <div>
                            <button type="button" @click="panel = panel === 'stores' ? null : 'stores'" class="flex w-full items-center justify-between py-4 text-left font-medium text-ink">
                                <span><i class="fa-solid fa-location-dot mr-2 text-brand"></i>Trouver dans un showroom</span>
                                <i class="fa-solid fa-chevron-down text-xs transition" :class="panel === 'stores' && 'rotate-180'"></i>
                            </button>
                            <div x-cloak x-show="panel === 'stores'" x-collapse>
                                <ul class="pb-4 text-sm">
                                    @foreach ($showrooms as $showroom)
                                        <li class="flex flex-wrap justify-between gap-2 border-b border-line py-2 last:border-0">
                                            <span class="font-medium uppercase text-ink">{{ $showroom->name }}</span>
                                            @if ($showroom->phone)
                                                <a href="tel:{{ preg_replace('/\s+/', '', $showroom->phone) }}" class="text-brand">{{ $showroom->phone }}</a>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <div>
                            <button type="button" @click="panel = panel === 'delivery' ? null : 'delivery'" class="flex w-full items-center justify-between py-4 text-left font-medium text-ink">
                                <span><i class="fa-solid fa-truck-fast mr-2 text-brand"></i>Livraison & paiement</span>
                                <i class="fa-solid fa-chevron-down text-xs transition" :class="panel === 'delivery' && 'rotate-180'"></i>
                            </button>
                            <div x-cloak x-show="panel === 'delivery'" x-collapse>
                                <div class="space-y-2 pb-4 text-sm">
                                    <p><i class="fa-solid fa-check mr-2 text-success"></i>{{ setting('delivery_text') }}.</p>
                                    <p><i class="fa-solid fa-check mr-2 text-success"></i>Paiement : {{ setting('payment_text') }}.</p>
                                    <p><i class="fa-solid fa-check mr-2 text-success"></i>Retrait gratuit dans nos showrooms.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Related --}}
        @if ($related->isNotEmpty())
            <section class="mt-14">
                <h2 class="section-title mb-8">Vous pourriez aussi aimer</h2>
                <div class="grid grid-cols-2 gap-x-2 gap-y-8 sm:grid-cols-3 lg:grid-cols-4 lg:gap-x-6">
                    @foreach ($related as $item)
                        <x-product-card :product="$item" />
                    @endforeach
                </div>
                <div class="mt-10 text-center">
                    <a href="{{ route('shop.index') }}" class="btn btn-outline">Voir la boutique <i class="fa-solid fa-arrow-right text-sm"></i></a>
                </div>
            </section>
        @endif
    </div>
@endsection
