@extends('layouts.app')

@section('title', $product->name)
@section('description', \Illuminate\Support\Str::limit(strip_tags($product->description), 155))
@section('og_image', $product->mainImageUrl())

@section('content')
    @php
        $images = $product->imageUrls();
        $sizes = $product->sizeList();
        $colors = $product->colorList();
        $guideType = $product->sizeGuideType();
        $isFavorite = \App\Support\Wishlist::has($product->id);
        $reviewErrors = $errors->getBag('review');
    @endphp

    <div class="container-shop" x-data="{ guide: false }" @keydown.escape.window="guide = false">
        {{-- Breadcrumb --}}
        <nav aria-label="Fil d'Ariane" class="flex flex-wrap items-center gap-2 py-6 text-[.68rem] tracking-[.18em] text-muted uppercase">
            <a href="{{ route('home') }}" class="hover:text-ink">Accueil</a>
            <span class="text-line">/</span>
            <a href="{{ route('shop.index') }}" class="hover:text-ink">Boutique</a>
            @if ($product->category)
                <span class="text-line">/</span>
                <a href="{{ route('shop.category', $product->category) }}" class="hover:text-ink">{{ $product->category->name }}</a>
            @endif
            <span class="text-line">/</span>
            <span class="text-ink">{{ $product->name }}</span>
        </nav>

        <div class="grid gap-10 lg:grid-cols-12 lg:gap-14">
            {{-- ============ GALLERY ============ --}}
            <div class="lg:col-span-7" x-data="gallery(@js($images))" @keydown.window.arrow-right="viewer && next()" @keydown.window.arrow-left="viewer && prev()" @keydown.escape.window="close()">
                {{-- Mobile: swipe --}}
                <div class="-mx-4 flex snap-x snap-mandatory gap-2 overflow-x-auto px-4 [scrollbar-width:none] md:hidden">
                    @foreach ($images as $i => $image)
                        <button type="button" @click="open({{ $i }})" class="w-[88%] shrink-0 snap-center bg-sand">
                            <img src="{{ $image }}" alt="{{ $product->name }}" class="aspect-[3/4] w-full object-cover">
                        </button>
                    @endforeach
                </div>
                {{-- Desktop: editorial grid --}}
                <div class="hidden gap-3 md:grid md:grid-cols-2">
                    @foreach ($images as $i => $image)
                        <button type="button" @click="open({{ $i }})" @class(['group relative cursor-zoom-in overflow-hidden bg-sand', 'col-span-2' => $loop->first])>
                            @if ($loop->first && $product->hasDiscount())
                                <span class="absolute left-4 top-4 z-10 bg-brand px-2.5 py-1 text-xs text-white">−{{ $product->discountPercent() }} %</span>
                            @endif
                            <img src="{{ $image }}" alt="{{ $product->name }}" @if (! $loop->first) loading="lazy" @endif class="aspect-[3/4] w-full object-cover transition duration-700 group-hover:scale-[1.02]">
                        </button>
                    @endforeach
                </div>

                {{-- Fullscreen viewer --}}
                <div x-cloak x-show="viewer" x-transition.opacity class="fixed inset-0 z-[70] flex items-center justify-center bg-ivory" @click.self="close()">
                    <button type="button" @click="close()" class="absolute right-5 top-5 text-2xl text-ink" aria-label="Fermer"><i class="fa-solid fa-xmark"></i></button>
                    <img :src="images[active]" alt="{{ $product->name }}" class="max-h-[90vh] max-w-[92vw] object-contain">
                    <template x-if="images.length > 1">
                        <div>
                            <button type="button" @click="prev()" class="absolute left-4 top-1/2 flex size-12 -translate-y-1/2 items-center justify-center rounded-full border border-ink bg-ivory text-ink" aria-label="Précédente"><i class="fa-solid fa-arrow-left"></i></button>
                            <button type="button" @click="next()" class="absolute right-4 top-1/2 flex size-12 -translate-y-1/2 items-center justify-center rounded-full border border-ink bg-ivory text-ink" aria-label="Suivante"><i class="fa-solid fa-arrow-right"></i></button>
                            <p class="absolute bottom-6 left-1/2 -translate-x-1/2 font-display text-lg text-ink"><span x-text="active + 1"></span> / <span x-text="images.length"></span></p>
                        </div>
                    </template>
                </div>
            </div>

            {{-- ============ BUY BOX ============ --}}
            <div class="lg:col-span-5">
                <div class="lg:sticky lg:top-36"
                     x-data="addToCart({ productId: {{ $product->id }}, needsSize: {{ $sizes ? 'true' : 'false' }}, needsColor: {{ $colors ? 'true' : 'false' }}, url: '{{ route('cart.store') }}', cartUrl: '{{ route('cart.index') }}' })">
                    @if ($product->category)
                        <a href="{{ route('shop.category', $product->category) }}" class="kicker hover:text-ink">{{ $product->category->name }}</a>
                    @endif
                    <h1 class="mt-3 font-display text-4xl leading-tight text-ink md:text-5xl">{{ $product->name }}</h1>

                    <div class="mt-4 flex flex-wrap items-center gap-x-5 gap-y-2">
                        <div class="flex items-baseline gap-3">
                            <span @class(['text-2xl', 'text-brand' => $product->hasDiscount(), 'text-ink' => ! $product->hasDiscount()])>{{ money($product->finalPrice()) }}</span>
                            @if ($product->hasDiscount())
                                <del class="text-muted">{{ money($product->price) }}</del>
                            @endif
                        </div>
                        @if ($rating)
                            <a href="#avis" class="flex items-center gap-2 text-sm text-body hover:text-ink">
                                <span class="flex gap-0.5 text-xs text-gold">@for ($i = 1; $i <= 5; $i++)<i @class(['fa-star', 'fa-solid' => $i <= round($rating), 'fa-regular' => $i > round($rating)])></i>@endfor</span>
                                {{ number_format($rating, 1, ',', '') }} ({{ $reviews->count() }} avis)
                            </a>
                        @endif
                    </div>
                    <p class="mt-2 text-xs text-muted">Réf. {{ $product->reference }}</p>

                    <div class="my-7 h-px bg-line"></div>

                    @if ($product->inStock())
                        @if ($colors)
                            <div class="mb-6">
                                <p class="form-label">Couleur <span class="ml-1 font-normal normal-case tracking-normal text-muted" x-text="color"></span></p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($colors as $color)
                                        <button type="button" @click="color = @js($color)" :class="color === @js($color) ? 'border-ink bg-ink text-ivory' : ''" class="size-pill">{{ $color }}</button>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if ($sizes)
                            <div class="mb-6">
                                <div class="mb-1.5 flex items-center justify-between">
                                    <p class="form-label mb-0">Taille <span class="ml-1 font-normal normal-case tracking-normal text-muted" x-text="size"></span></p>
                                    @if ($guideType)
                                        <button type="button" @click="guide = true" class="text-xs text-body underline underline-offset-4 hover:text-brand"><i class="fa-solid fa-ruler mr-1"></i>Guide des tailles</button>
                                    @endif
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($sizes as $size)
                                        <button type="button" @click="size = @js($size)" :class="size === @js($size) ? 'border-ink bg-ink text-ivory' : ''" class="size-pill">{{ str_replace('Pointure ', '', $size) }}</button>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="flex gap-3">
                            <div class="flex items-center border border-line bg-white">
                                <button type="button" @click="quantity > 1 && quantity--" class="flex size-12 items-center justify-center text-ink" aria-label="Moins"><i class="fa-solid fa-minus text-xs"></i></button>
                                <span class="w-6 text-center text-sm text-ink" x-text="quantity">1</span>
                                <button type="button" @click="quantity < {{ min(config('shop.max_quantity'), $product->stock) }} && quantity++" class="flex size-12 items-center justify-center text-ink" aria-label="Plus"><i class="fa-solid fa-plus text-xs"></i></button>
                            </div>
                            <button type="button" @click="submit()" :disabled="loading" class="btn btn-primary flex-1">
                                <i class="fa-solid" :class="loading ? 'fa-spinner fa-spin' : 'fa-bag-shopping'"></i> Ajouter au panier
                            </button>
                            <button type="button" x-data="wishlistButton('{{ route('wishlist.toggle', $product) }}', {{ $isFavorite ? 'true' : 'false' }})" @click="toggle()"
                                    class="flex size-12 shrink-0 items-center justify-center border border-line bg-white text-ink transition hover:border-ink" aria-label="Ajouter aux favoris">
                                <i class="fa-heart" :class="active ? 'fa-solid text-brand' : 'fa-regular'"></i>
                            </button>
                        </div>
                        <p x-cloak x-show="error" x-text="error" class="form-error"></p>
                    @else
                        <div class="border border-line bg-white p-4 text-sm">
                            <p class="font-medium text-ink">Cette pièce est épuisée.</p>
                            <p class="text-muted">Contactez-nous sur WhatsApp ou faites-la confectionner sur mesure.</p>
                        </div>
                        <button type="button" x-data="wishlistButton('{{ route('wishlist.toggle', $product) }}', {{ $isFavorite ? 'true' : 'false' }})" @click="toggle()" class="btn btn-outline mt-3 w-full">
                            <i class="fa-heart" :class="active ? 'fa-solid text-brand' : 'fa-regular'"></i> <span x-text="active ? 'Dans vos favoris' : 'Ajouter aux favoris'"></span>
                        </button>
                    @endif

                    <a href="{{ whatsapp_url('Bonjour Mombeya Galy, je suis intéressé(e) par : '.$product->name.' (réf. '.$product->reference.') – '.route('product.show', $product)) }}"
                       target="_blank" rel="noopener" class="mt-3 flex items-center justify-center gap-2 py-2 text-[.72rem] tracking-[.16em] text-ink uppercase hover:text-[#128C7E]">
                        <i class="fa-brands fa-whatsapp text-base"></i> Commander sur WhatsApp
                    </a>

                    <ul class="mt-6 space-y-2.5 border-t border-line pt-6 text-sm text-body">
                        @if ($product->is_deliverable)
                            <li class="flex gap-3"><i class="fa-solid fa-truck-fast mt-0.5 w-4 text-ink"></i> {{ setting('delivery_text') }}</li>
                        @endif
                        <li class="flex gap-3"><i class="fa-solid fa-store mt-0.5 w-4 text-ink"></i> Retrait gratuit dans nos maisons</li>
                        @if ($product->category?->slug !== 'accessoires')
                            <li class="flex gap-3"><i class="fa-solid fa-soap mt-0.5 w-4 text-ink"></i> <span>Entretien conseillé : <a href="{{ route('pressing.index') }}" class="underline underline-offset-4 hover:text-brand">Mombeya Galy Pressing</a></span></li>
                        @endif
                    </ul>

                    {{-- Accordions --}}
                    <div class="mt-6 divide-y divide-line border-y border-line" x-data="{ panel: 'description' }">
                        @foreach ([
                            'description' => 'Description',
                            'delivery' => 'Livraison & paiement',
                            'stores' => 'Disponible en maison',
                        ] as $key => $label)
                            <div>
                                <button type="button" @click="panel = panel === @js($key) ? null : @js($key)" class="flex w-full items-center justify-between py-4 text-left text-[.74rem] font-medium tracking-[.16em] text-ink uppercase">
                                    {{ $label }}
                                    <i class="fa-solid fa-plus text-xs transition" :class="panel === @js($key) && 'rotate-45'"></i>
                                </button>
                                <div x-show="panel === @js($key)" x-collapse @if ($key !== 'description') x-cloak @endif>
                                    <div class="pb-5 text-sm text-body">
                                        @if ($key === 'description')
                                            <div class="prose-shop">{!! $product->description !!}</div>
                                        @elseif ($key === 'delivery')
                                            <p class="mb-2">{{ setting('delivery_text') }}.</p>
                                            <p class="mb-2">Paiement : {{ setting('payment_text') }}.</p>
                                            <p>Une question sur votre commande ? <a href="{{ whatsapp_url() }}" target="_blank" rel="noopener" class="underline underline-offset-4 hover:text-brand">Écrivez-nous sur WhatsApp</a>.</p>
                                        @else
                                            <ul class="divide-y divide-line">
                                                @foreach ($showrooms as $showroom)
                                                    <li class="flex flex-wrap justify-between gap-2 py-2">
                                                        <span class="text-ink">{{ $showroom->name }}</span>
                                                        @if ($showroom->phone)<a href="tel:{{ preg_replace('/\s+/', '', $showroom->phone) }}" class="hover:text-brand">{{ $showroom->phone }}</a>@endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Size guide modal --}}
        @if ($guideType)
            <div x-cloak x-show="guide" x-transition.opacity class="fixed inset-0 z-[70] flex justify-end bg-black/40" @click.self="guide = false">
                <div x-show="guide" x-transition:enter="transition duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                     class="h-full w-full max-w-xl overflow-y-auto bg-ivory p-6 md:p-10">
                    <div class="mb-8 flex items-center justify-between">
                        <h2 class="font-display text-3xl">Guide des tailles</h2>
                        <button type="button" @click="guide = false" class="text-2xl text-ink" aria-label="Fermer"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                    @include('partials.size-tables', ['only' => $guideType])
                    <p class="mt-8 text-sm text-body">Entre deux tailles ? Prenez la plus grande, ou <a href="{{ route('appointments.create') }}" class="underline underline-offset-4 hover:text-brand">faites-la confectionner sur mesure</a>.</p>
                    <a href="{{ route('size-guide') }}" class="link-arrow mt-6">Comment prendre ses mesures <i class="fa-solid fa-arrow-right text-[.6rem]"></i></a>
                </div>
            </div>
        @endif

        {{-- ============ REVIEWS ============ --}}
        <section id="avis" class="mt-24 scroll-mt-32 border-t border-line pt-16">
            <div class="grid gap-12 lg:grid-cols-12">
                <div class="lg:col-span-4">
                    <p class="kicker mb-3">Avis clients</p>
                    <h2 class="section-title mb-4">Ce qu'en disent nos clients</h2>
                    @if ($rating)
                        <div class="flex items-center gap-4">
                            <span class="font-display text-6xl text-ink">{{ number_format($rating, 1, ',', '') }}</span>
                            <div>
                                <div class="flex gap-0.5 text-gold">@for ($i = 1; $i <= 5; $i++)<i @class(['fa-star', 'fa-solid' => $i <= round($rating), 'fa-regular' => $i > round($rating)])></i>@endfor</div>
                                <p class="text-sm text-muted">{{ $reviews->count() }} avis</p>
                            </div>
                        </div>
                    @else
                        <p class="text-body">Aucun avis pour le moment. Soyez le premier à partager votre expérience.</p>
                    @endif

                    <form method="POST" action="{{ route('reviews.store', $product) }}" class="panel mt-8 space-y-4" x-data="{ rating: {{ (int) old('rating', 0) }}, hover: 0 }">
                        @csrf
                        <h3 class="font-display text-2xl">Donner mon avis</h3>
                        <div>
                            <span class="form-label">Votre note</span>
                            <div class="flex gap-1 text-xl text-gold" @mouseleave="hover = 0">
                                @for ($i = 1; $i <= 5; $i++)
                                    <button type="button" @click="rating = {{ $i }}" @mouseenter="hover = {{ $i }}" aria-label="{{ $i }} étoile{{ $i > 1 ? 's' : '' }}">
                                        <i class="fa-star" :class="(hover || rating) >= {{ $i }} ? 'fa-solid' : 'fa-regular'"></i>
                                    </button>
                                @endfor
                            </div>
                            <input type="hidden" name="rating" :value="rating || ''">
                            @if ($reviewErrors->has('rating'))<p class="form-error">{{ $reviewErrors->first('rating') }}</p>@endif
                        </div>
                        <div>
                            <label class="form-label" for="review-name">Prénom</label>
                            <input id="review-name" name="name" value="{{ old('name', auth()->user()?->name) }}" required maxlength="80" class="form-control">
                            @if ($reviewErrors->has('name'))<p class="form-error">{{ $reviewErrors->first('name') }}</p>@endif
                        </div>
                        <div>
                            <label class="form-label" for="review-comment">Votre avis</label>
                            <textarea id="review-comment" name="comment" rows="4" required minlength="10" maxlength="1000" class="form-control" placeholder="Qualité, coupe, livraison…">{{ old('comment') }}</textarea>
                            @if ($reviewErrors->has('comment'))<p class="form-error">{{ $reviewErrors->first('comment') }}</p>@endif
                        </div>
                        <button class="btn btn-dark w-full">Publier mon avis</button>
                        <p class="text-xs text-muted">Votre avis sera publié après validation. Seul votre prénom et l'initiale de votre nom apparaissent.</p>
                    </form>
                </div>

                <div class="lg:col-span-8">
                    @forelse ($reviews as $review)
                        <article class="border-b border-line py-7 first:pt-0">
                            <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                                <div class="flex gap-0.5 text-sm text-gold">@for ($i = 1; $i <= 5; $i++)<i @class(['fa-star', 'fa-solid' => $i <= $review->rating, 'fa-regular' => $i > $review->rating])></i>@endfor</div>
                                <span class="text-xs text-muted">{{ $review->created_at->translatedFormat('d F Y') }}</span>
                            </div>
                            <p class="mb-3 font-display text-xl leading-snug text-ink">« {{ $review->comment }} »</p>
                            <p class="text-sm font-medium text-ink">{{ $review->displayName() }}</p>
                        </article>
                    @empty
                        <div class="flex h-full items-center justify-center border border-dashed border-line p-12 text-center">
                            <div>
                                <i class="fa-regular fa-comment-dots mb-4 text-4xl text-line"></i>
                                <p class="text-muted">Les avis de nos clients apparaîtront ici.</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        {{-- ============ RELATED ============ --}}
        @if ($related->isNotEmpty())
            <section class="mt-24">
                <div class="mb-10 flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <p class="kicker mb-3">Suggestions</p>
                        <h2 class="section-title">Complétez votre tenue</h2>
                    </div>
                    <a href="{{ route('shop.index') }}" class="link-arrow">Toute la boutique <i class="fa-solid fa-arrow-right text-[.6rem]"></i></a>
                </div>
                <div class="grid grid-cols-2 gap-x-4 gap-y-12 md:grid-cols-3 lg:grid-cols-4 lg:gap-x-6">
                    @foreach ($related as $item)
                        <x-product-card :product="$item" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>
@endsection
