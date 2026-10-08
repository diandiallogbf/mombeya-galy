@extends('layouts.app')

@section('content')

    {{-- ============ HERO SLIDER ============ --}}
    @if ($slides->isNotEmpty())
        <section class="swiper group/hero relative" data-hero>
            <div class="swiper-wrapper">
                @foreach ($slides as $slide)
                    {{-- "!" needed: Swiper's unlayered CSS sets .swiper-slide { height: 100% } --}}
                    <div class="swiper-slide relative h-[300px]! overflow-hidden bg-navy sm:h-[400px]! lg:h-[520px]!">
                        <img src="{{ $slide->imageUrl() }}" alt="{{ $slide->title }}" class="absolute inset-0 size-full object-cover" @if (! $loop->first) loading="lazy" @endif>
                        @if ($slide->title || $slide->subtitle)
                            <div class="absolute inset-0 bg-gradient-to-r from-black/45 via-black/15 to-transparent"></div>
                            <div class="container-shop relative flex h-full items-center">
                                <div class="max-w-xl text-white">
                                    @if ($slide->title)
                                        <h2 class="mb-3 text-3xl font-bold leading-tight text-white sm:text-4xl lg:text-[3.25rem]">{{ $slide->title }}</h2>
                                    @endif
                                    @if ($slide->subtitle)
                                        <p class="mb-6 text-base text-white/85 sm:text-lg">{{ $slide->subtitle }}</p>
                                    @endif
                                    @if ($slide->button_text && $slide->button_link)
                                        <a href="{{ $slide->button_link }}" class="btn btn-primary btn-shadow">{{ $slide->button_text }} <i class="fa-solid fa-arrow-right text-sm"></i></a>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
            <button type="button" data-prev aria-label="Précédent" class="absolute left-6 top-1/2 z-10 hidden size-12 -translate-y-1/2 items-center justify-center rounded-full bg-white/90 text-ink opacity-0 shadow-lg transition group-hover/hero:opacity-100 hover:bg-brand hover:text-white lg:flex">
                <i class="fa-solid fa-chevron-left"></i>
            </button>
            <button type="button" data-next aria-label="Suivant" class="absolute right-6 top-1/2 z-10 hidden size-12 -translate-y-1/2 items-center justify-center rounded-full bg-white/90 text-ink opacity-0 shadow-lg transition group-hover/hero:opacity-100 hover:bg-brand hover:text-white lg:flex">
                <i class="fa-solid fa-chevron-right"></i>
            </button>
            <div class="swiper-pagination bottom-4! flex justify-center gap-1 lg:hidden!"></div>
        </section>
    @endif

    {{-- ============ CATEGORY TILES (desktop: overlapping card, 6 per row) ============ --}}
    @if ($homeCategories->isNotEmpty())
        <section class="container-shop relative z-10 hidden md:block lg:-mt-16">
            <div class="mt-8 rounded-lg bg-white p-6 shadow-lg lg:mt-0">
                <div class="grid grid-cols-4 gap-x-5 gap-y-6 lg:grid-cols-6">
                    @foreach ($homeCategories as $homeCategory)
                        <a href="{{ $homeCategory->slug === 'promotion' ? route('shop.promotions') : route('shop.category', $homeCategory) }}" class="group/tile block text-center">
                            <div class="mb-3 overflow-hidden rounded-lg">
                                <img src="{{ $homeCategory->imageUrl() }}" alt="{{ $homeCategory->name }}" loading="lazy" class="h-[220px] w-full object-cover transition duration-500 group-hover/tile:scale-105">
                            </div>
                            <h3 class="text-[.95rem] font-medium text-ink transition group-hover/tile:text-brand">{{ $homeCategory->name }}</h3>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Mobile: horizontally scrollable tiles --}}
        <section class="mt-6 md:hidden">
            <div class="flex snap-x gap-3 overflow-x-auto px-4 pb-2 [scrollbar-width:none]">
                @foreach ($homeCategories as $homeCategory)
                    <a href="{{ $homeCategory->slug === 'promotion' ? route('shop.promotions') : route('shop.category', $homeCategory) }}" class="w-28 shrink-0 snap-start text-center">
                        <img src="{{ $homeCategory->imageUrl() }}" alt="{{ $homeCategory->name }}" loading="lazy" class="mb-2 h-36 w-full rounded-lg object-cover">
                        <span class="line-clamp-2 text-xs font-medium text-ink">{{ $homeCategory->name }}</span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ============ TENDANCE DE LA SEMAINE ============ --}}
    @if ($trendWeek->isNotEmpty())
        <section class="container-shop mt-10">
            <h2 class="mb-5 flex items-center gap-2 text-lg font-medium text-ink"><i class="fa-solid fa-bullhorn text-brand"></i> Tendance de la semaine</h2>
            <x-product-carousel :products="$trendWeek" />
        </section>
    @endif

    {{-- ============ INFO ROW: showrooms + online order / DHL ============ --}}
    <section class="container-shop mt-10 grid gap-5 md:grid-cols-3">
        <a href="{{ route('showrooms') }}" class="group/sr flex items-center gap-5 rounded-lg border border-line p-6 transition hover:border-brand hover:shadow-card">
            <span class="flex size-16 shrink-0 items-center justify-center rounded-full bg-brand-light text-3xl text-brand"><i class="fa-solid fa-location-dot"></i></span>
            <span>
                <span class="mb-1 block text-lg font-bold text-ink">Nos showrooms</span>
                <span class="block text-sm text-body"><i class="fa-regular fa-eye mr-1 text-brand"></i> Voir la liste de nos showrooms</span>
                <span class="block text-sm text-body"><i class="fa-solid fa-map mr-1 text-brand"></i> Sur la carte</span>
            </span>
        </a>

        <div class="flex flex-col items-center gap-5 overflow-hidden rounded-lg bg-soft p-6 sm:flex-row md:col-span-2">
            <div class="hidden flex-1 lg:block">
                <h4 class="mb-4 text-xl font-light text-ink">Commandez et payez vos articles en ligne.</h4>
                <a href="{{ route('shop.index') }}" class="btn btn-primary btn-shadow btn-sm">Commander maintenant</a>
            </div>
            <div class="relative flex w-full flex-1 items-center gap-4 overflow-hidden rounded-lg bg-gradient-to-r from-navy to-ocean px-5 py-4 text-white lg:max-w-md">
                <i class="fa-solid fa-plane-departure text-4xl text-azure"></i>
                <div class="leading-tight">
                    <div class="text-xs font-bold uppercase tracking-wider text-azure">Livraison internationale</div>
                    <div class="text-xl font-black italic">en moins de 5 jours</div>
                    <div class="text-xs text-white/70">Hors Guinée · Envoi express par DHL</div>
                </div>
            </div>
        </div>

        <a href="{{ route('shop.prestige') }}" class="flex items-center gap-4 rounded-lg bg-navy p-5 text-white md:hidden">
            <i class="fa-solid fa-award text-3xl text-azure"></i>
            <span>
                <span class="block font-bold text-white">MOMBEYA GALY PRESTIGE</span>
                <span class="text-sm text-white/70"><i class="fa-regular fa-eye"></i> Voir les produits prestige</span>
            </span>
        </a>
    </section>

    {{-- ============ TENDANCE DU MOIS ============ --}}
    @if ($trendMonth->isNotEmpty())
        <section class="container-shop mt-14">
            <h2 class="section-title">Tendance du mois</h2>
            <p class="mt-1 text-center text-[.95rem] text-muted">Découvrez ici les dernières créations de Mombeya Galy</p>
            <hr class="my-6 border-line">
            <div class="grid grid-cols-2 gap-x-2 gap-y-8 sm:grid-cols-3 lg:grid-cols-4 lg:gap-x-6">
                @foreach ($trendMonth as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
            <div class="mt-10 text-center">
                <a href="{{ route('shop.index') }}" class="btn btn-outline">Voir la boutique <i class="fa-solid fa-arrow-right text-sm"></i></a>
            </div>
        </section>
    @endif

    {{-- ============ ACCESSOIRES ============ --}}
    @if ($accessories->isNotEmpty())
        <section class="container-shop mt-16">
            <h2 class="section-title mb-6">Accessoires Mombeya Galy</h2>
            <x-product-carousel :products="$accessories" />
        </section>
    @endif

@endsection
