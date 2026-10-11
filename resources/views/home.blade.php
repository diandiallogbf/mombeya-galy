@extends('layouts.app')

@section('content')
    @php
        $atelierImage = setting('atelier_image') ? \App\Support\Media::url(setting('atelier_image')) : asset('images/atelier.svg');
        $bento = $universes->reject(fn ($c) => $c->slug === 'promotion')->take(5)->values();
        $otherUniverses = $universes->reject(fn ($c) => $bento->contains('id', $c->id));
        $communityUrl = setting('whatsapp_community_url') ?: whatsapp_url('Bonjour, je souhaite rejoindre la communauté Mombeya Galy.');
    @endphp

    {{-- ============ HERO (split screen) ============ --}}
    @if ($slides->isNotEmpty())
        <section class="swiper border-b border-line" data-hero>
            <div class="swiper-wrapper">
                @foreach ($slides as $slide)
                    <div class="swiper-slide bg-ivory">
                        <div class="container-shop grid items-center gap-8 py-10 md:grid-cols-12 md:gap-12 md:py-14">
                            <div class="order-2 md:order-1 md:col-span-5">
                                <p class="kicker mb-5">Mombeya Galy · Conakry</p>
                                <h2 class="display-title mb-6 lg:text-7xl">{{ $slide->title ?: setting('shop_name') }}</h2>
                                @if ($slide->subtitle)
                                    <p class="mb-9 max-w-md text-[1.05rem] leading-relaxed text-body">{{ $slide->subtitle }}</p>
                                @endif
                                <div class="flex flex-wrap items-center gap-6">
                                    @if ($slide->button_text && $slide->button_link)
                                        <a href="{{ $slide->button_link }}" class="btn btn-dark">{{ $slide->button_text }}</a>
                                    @endif
                                    <a href="{{ route('appointments.create') }}" class="link-arrow">Créer sur mesure <i class="fa-solid fa-arrow-right text-[.6rem]"></i></a>
                                </div>
                            </div>
                            <div class="order-1 md:order-2 md:col-span-7">
                                <div class="overflow-hidden">
                                    <img src="{{ $slide->imageUrl() }}" alt="{{ $slide->title }}" class="aspect-[4/3] w-full object-cover md:aspect-[5/4] lg:h-[620px] lg:aspect-auto" @if (! $loop->first) loading="lazy" @endif>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            @if ($slides->count() > 1)
                <div class="container-shop pointer-events-none absolute inset-x-0 bottom-6 z-10 hidden md:block">
                    <div class="pointer-events-auto flex items-center gap-5 md:w-5/12">
                        <span class="font-display text-lg text-ink"><span data-counter>01</span> <span class="text-muted">/ {{ str_pad($slides->count(), 2, '0', STR_PAD_LEFT) }}</span></span>
                        <div class="swiper-pagination static! flex w-auto! gap-1"></div>
                        <div class="ml-auto flex gap-2">
                            <button type="button" data-prev aria-label="Précédent" class="flex size-10 items-center justify-center rounded-full border border-ink text-ink hover:bg-ink hover:text-ivory"><i class="fa-solid fa-arrow-left text-xs"></i></button>
                            <button type="button" data-next aria-label="Suivant" class="flex size-10 items-center justify-center rounded-full border border-ink text-ink hover:bg-ink hover:text-ivory"><i class="fa-solid fa-arrow-right text-xs"></i></button>
                        </div>
                    </div>
                </div>
            @endif
        </section>
    @endif

    {{-- ============ REASSURANCE ============ --}}
    <section class="border-b border-line bg-white">
        <div class="container-shop grid grid-cols-2 divide-line md:grid-cols-4 md:divide-x">
            @foreach ([
                ['fa-earth-africa', 'Livraison internationale', 'Conakry en 24h, monde entier'],
                ['fa-store', 'Retrait gratuit', 'Dans nos maisons de Conakry'],
                ['fa-mobile-screen', 'Paiement mobile', 'Orange Money, MTN MoMo'],
                ['fa-scissors', 'Sur mesure & pressing', 'Toute la chaîne, une seule maison'],
            ] as [$icon, $title, $text])
                <div class="flex items-center gap-4 px-2 py-6 md:px-6">
                    <i class="fa-solid {{ $icon }} text-xl text-ink"></i>
                    <div>
                        <p class="text-[.72rem] font-medium tracking-[.14em] text-ink uppercase">{{ $title }}</p>
                        <p class="text-xs text-muted">{{ $text }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ============ NOS UNIVERS (bento) ============ --}}
    @if ($bento->isNotEmpty())
        <section class="container-shop mt-20 md:mt-28" data-reveal>
            <div class="mb-10 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="kicker mb-3">Collections</p>
                    <h2 class="section-title">Nos univers</h2>
                </div>
                <a href="{{ route('shop.index') }}" class="link-arrow">Toute la boutique <i class="fa-solid fa-arrow-right text-[.6rem]"></i></a>
            </div>
            <div class="grid grid-cols-2 gap-3 md:gap-4 lg:h-[680px] lg:grid-cols-4 lg:grid-rows-2">
                @foreach ($bento as $universe)
                    <a href="{{ route('shop.category', $universe) }}" @class([
                        'group relative block overflow-hidden bg-sand',
                        'col-span-2 aspect-[4/3] lg:row-span-2 lg:aspect-auto' => $loop->first,
                        'aspect-[3/4] lg:aspect-auto' => ! $loop->first,
                    ])>
                        <img src="{{ $universe->imageUrl() }}" alt="{{ $universe->name }}" loading="lazy" class="absolute inset-0 size-full object-cover transition duration-[1.2s] group-hover:scale-105">
                        <span class="absolute inset-0 bg-gradient-to-t from-black/55 via-black/5 to-transparent"></span>
                        <span class="absolute bottom-5 left-5 right-5 text-ivory">
                            <span @class(['block font-display leading-tight', 'text-3xl md:text-4xl' => $loop->first, 'text-xl md:text-2xl' => ! $loop->first])>{{ $universe->name }}</span>
                            <span class="mt-1 inline-block text-[.65rem] tracking-[.22em] uppercase opacity-90">Découvrir →</span>
                        </span>
                    </a>
                @endforeach
            </div>
            @if ($otherUniverses->isNotEmpty())
                <div class="mt-6 flex flex-wrap gap-2">
                    @foreach ($otherUniverses as $universe)
                        <a href="{{ $universe->slug === 'promotion' ? route('shop.promotions') : route('shop.category', $universe) }}" class="chip">{{ $universe->name }}</a>
                    @endforeach
                </div>
            @endif
        </section>
    @endif

    {{-- ============ LES INCONTOURNABLES ============ --}}
    @if ($essentials->isNotEmpty())
        <section class="mt-24 overflow-hidden md:mt-32" data-reveal>
            <div class="container-shop">
                <div class="mb-10 flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <p class="kicker mb-3">Les plus aimés</p>
                        <h2 class="section-title">Les incontournables</h2>
                    </div>
                    <a href="{{ route('shop.index', ['tri' => 'populaire']) }}" class="link-arrow">Tout voir <i class="fa-solid fa-arrow-right text-[.6rem]"></i></a>
                </div>
                <x-product-carousel :products="$essentials" />
            </div>
        </section>
    @endif

    {{-- ============ LA CHAÎNE COMPLÈTE ============ --}}
    <section class="mt-24 bg-night py-20 text-ivory md:mt-32 md:py-28" data-reveal>
        <div class="container-shop">
            <div class="mx-auto mb-14 max-w-2xl text-center">
                <p class="mb-4 text-[.7rem] tracking-[.28em] text-ivory/60 uppercase">Une maison, trois savoir-faire</p>
                <h2 class="font-display text-4xl font-semibold text-ivory md:text-5xl">La chaîne complète</h2>
                <p class="mt-5 text-ivory/70">De la prise de mesures à l'entretien de vos tenues, Mombeya Galy vous accompagne à chaque étape.</p>
            </div>
            <div class="grid gap-px bg-ivory/15 md:grid-cols-3">
                @foreach ([
                    ['01', 'Confection sur mesure', 'Nos tailleurs créent la tenue de vos grands moments : prise de mesures, choix du tissu, essayages.', route('appointments.create'), 'Prendre rendez-vous'],
                    ['02', 'La boutique', 'Grands boubous, bazin, costumes africains et accessoires, prêts à porter et livrés partout.', route('shop.index'), 'Découvrir la boutique'],
                    ['03', 'Le pressing', 'Lavage, amidonnage du bazin et repassage. Collecte et livraison à domicile.', route('pressing.index'), 'Voir les tarifs'],
                ] as [$number, $title, $text, $link, $cta])
                    <div class="flex flex-col bg-night p-8 md:p-10">
                        <span class="mb-8 font-display text-5xl text-ivory/30">{{ $number }}</span>
                        <h3 class="mb-3 font-display text-3xl text-ivory">{{ $title }}</h3>
                        <p class="mb-8 flex-1 text-sm leading-relaxed text-ivory/70">{{ $text }}</p>
                        <a href="{{ $link }}" class="inline-flex items-center gap-2 self-start border-b border-ivory/50 pb-0.5 text-[.72rem] tracking-[.18em] text-ivory uppercase transition hover:border-brand hover:text-brand">{{ $cta }} <i class="fa-solid fa-arrow-right text-[.6rem]"></i></a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ NOUVELLE COLLECTION ============ --}}
    @if ($newCollection->isNotEmpty())
        <section class="container-shop mt-24 md:mt-32" data-reveal>
            <div class="mb-12 text-center">
                <p class="kicker mb-3">Fraîchement sorties de l'atelier</p>
                <h2 class="section-title">Nouvelle collection</h2>
            </div>
            <div class="grid grid-cols-2 gap-x-4 gap-y-12 md:grid-cols-3 lg:grid-cols-4 lg:gap-x-6">
                @foreach ($newCollection as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
            <div class="mt-14 text-center">
                <a href="{{ route('shop.news') }}" class="btn btn-outline">Voir toute la collection</a>
            </div>
        </section>
    @endif

    {{-- ============ NOTRE ATELIER ============ --}}
    <section class="container-shop mt-24 grid items-center gap-10 md:mt-32 md:grid-cols-2 lg:gap-20" data-reveal>
        <div class="overflow-hidden bg-sand">
            <img src="{{ $atelierImage }}" alt="L'atelier Mombeya Galy" loading="lazy" class="aspect-[4/5] w-full object-cover">
        </div>
        <div>
            <p class="kicker mb-4">Notre atelier</p>
            <h2 class="section-title mb-6">{{ setting('atelier_title') }}</h2>
            <p class="mb-10 text-[1.02rem] leading-relaxed text-body">{{ setting('atelier_text') }}</p>
            <dl class="mb-10 grid grid-cols-3 gap-4 border-y border-line py-8">
                <div><dt class="font-display text-4xl text-ink md:text-5xl">{{ setting('atelier_years') }}</dt><dd class="mt-1 text-xs tracking-[.12em] text-muted uppercase">ans de savoir-faire</dd></div>
                <div><dt class="font-display text-4xl text-ink md:text-5xl">{{ setting('atelier_pieces') }}</dt><dd class="mt-1 text-xs tracking-[.12em] text-muted uppercase">pièces confectionnées</dd></div>
                <div><dt class="font-display text-4xl text-ink md:text-5xl">{{ $showroomsCount }}</dt><dd class="mt-1 text-xs tracking-[.12em] text-muted uppercase">maisons</dd></div>
            </dl>
            <div class="flex flex-wrap items-center gap-6">
                <a href="{{ route('appointments.create') }}" class="btn btn-dark">Prendre rendez-vous</a>
                <a href="{{ route('showrooms') }}" class="link-arrow">Nos maisons <i class="fa-solid fa-arrow-right text-[.6rem]"></i></a>
            </div>
        </div>
    </section>

    {{-- ============ ACCESSOIRES ============ --}}
    @if ($accessories->isNotEmpty())
        <section class="mt-24 overflow-hidden md:mt-32" data-reveal>
            <div class="container-shop">
                <div class="mb-10 flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <p class="kicker mb-3">Accessoires</p>
                        <h2 class="section-title">Pour parfaire l'allure</h2>
                    </div>
                    <a href="{{ route('shop.category', 'accessoires') }}" class="link-arrow">Tous les accessoires <i class="fa-solid fa-arrow-right text-[.6rem]"></i></a>
                </div>
                <x-product-carousel :products="$accessories" />
            </div>
        </section>
    @endif

    {{-- ============ AVIS CLIENTS ============ --}}
    @if ($testimonials->isNotEmpty())
        <section class="mt-24 bg-sand py-20 md:mt-32" data-reveal>
            <div class="container-shop">
                <div class="mb-12 text-center">
                    <p class="kicker mb-3">Avis clients</p>
                    <h2 class="section-title">Ils nous font confiance</h2>
                </div>
                <div class="swiper" data-testimonials>
                    <div class="swiper-wrapper">
                        @foreach ($testimonials as $review)
                            <figure class="swiper-slide h-auto! bg-white p-8">
                                <div class="mb-5 flex gap-1 text-sm text-gold">
                                    @for ($i = 1; $i <= 5; $i++)<i @class(['fa-star', 'fa-solid' => $i <= $review->rating, 'fa-regular' => $i > $review->rating])></i>@endfor
                                </div>
                                <blockquote class="mb-6 font-display text-xl leading-snug text-ink">« {{ \Illuminate\Support\Str::limit($review->comment, 220) }} »</blockquote>
                                <figcaption class="text-sm">
                                    <span class="font-medium text-ink">{{ $review->displayName() }}</span>
                                    @if ($review->product)
                                        <span class="text-muted"> · <a href="{{ route('product.show', $review->product) }}" class="hover:text-brand">{{ $review->product->name }}</a></span>
                                    @endif
                                </figcaption>
                            </figure>
                        @endforeach
                    </div>
                    <div class="swiper-pagination static! mt-10 flex justify-center gap-1"></div>
                </div>
            </div>
        </section>
    @endif

    {{-- ============ PORTÉS PAR VOUS ============ --}}
    @if ($gallery->isNotEmpty())
        <section class="container-shop mt-24 md:mt-32" data-reveal>
            <div class="mb-10 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="kicker mb-3">#MombeyaGaly</p>
                    <h2 class="section-title">Portés par vous</h2>
                </div>
                @if (setting('instagram_url'))
                    <a href="{{ setting('instagram_url') }}" target="_blank" rel="noopener" class="link-arrow"><i class="fa-brands fa-instagram"></i> Suivez-nous</a>
                @endif
            </div>
            <div class="grid grid-cols-2 gap-3 md:grid-cols-4 md:gap-4">
                @foreach ($gallery as $photo)
                    <a href="{{ $photo->link ?: (setting('instagram_url') ?: '#') }}" @if ($photo->link || setting('instagram_url')) target="_blank" rel="noopener" @endif class="group relative block aspect-square overflow-hidden bg-sand">
                        <img src="{{ $photo->imageUrl() }}" alt="{{ $photo->caption }}" loading="lazy" class="size-full object-cover transition duration-700 group-hover:scale-105">
                        @if ($photo->caption)
                            <span class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/60 to-transparent p-4 text-sm text-ivory opacity-0 transition group-hover:opacity-100">{{ $photo->caption }}</span>
                        @endif
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ============ COMMUNAUTÉ ============ --}}
    <section class="container-shop mt-24 md:mt-32" data-reveal>
        <div class="grid items-center gap-8 border border-line bg-white px-6 py-12 text-center md:grid-cols-2 md:px-14 md:text-left">
            <div>
                <p class="kicker mb-3">Communauté</p>
                <h2 class="section-title">Rejoignez le cercle Mombeya Galy</h2>
            </div>
            <div class="md:justify-self-end">
                <p class="mb-6 text-body">Nouvelles collections, offres privées et conseils de style, directement sur WhatsApp.</p>
                <a href="{{ $communityUrl }}" target="_blank" rel="noopener" class="btn bg-[#25D366] text-white hover:bg-[#1da851]"><i class="fa-brands fa-whatsapp text-base"></i> Rejoindre la communauté</a>
            </div>
        </div>
    </section>
@endsection
