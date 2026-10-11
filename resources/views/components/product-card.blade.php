@props(['product'])

@php
    $sizes = $product->sizeList();
    $colors = $product->colorList();
    $url = route('product.show', $product);
    $hoverImage = $product->hoverImageUrl();
    // Quick-add works in one tap when the product has no colour to choose.
    $quickAdd = $product->inStock() && ! $colors;
@endphp

<article {{ $attributes->class(['group/card relative flex h-full flex-col']) }}
         x-data="addToCart({ productId: {{ $product->id }}, needsSize: {{ $sizes ? 'true' : 'false' }}, needsColor: false, instant: true, url: '{{ route('cart.store') }}', cartUrl: '{{ route('cart.index') }}' })"
         @mouseleave="open = false">
    <div class="relative overflow-hidden bg-sand">
        <a href="{{ $url }}" class="block" aria-label="{{ $product->name }}">
            <img src="{{ $product->mainImageUrl() }}" alt="{{ $product->name }}" loading="lazy"
                 @class(['aspect-[3/4] w-full object-cover transition duration-700', 'group-hover/card:opacity-0' => $hoverImage, 'group-hover/card:scale-[1.04]' => ! $hoverImage])>
            @if ($hoverImage)
                <img src="{{ $hoverImage }}" alt="" loading="lazy" class="absolute inset-0 aspect-[3/4] w-full object-cover opacity-0 transition duration-700 group-hover/card:opacity-100">
            @endif
        </a>

        {{-- Badges --}}
        <div class="pointer-events-none absolute left-3 top-3 flex flex-col items-start gap-1.5">
            @if ($product->hasDiscount())
                <span class="bg-brand px-2 py-1 text-[.65rem] font-medium tracking-wider text-white">−{{ $product->discountPercent() }} %</span>
            @endif
            @if ($product->is_new)
                <span class="bg-ivory px-2 py-1 text-[.65rem] font-medium tracking-[.14em] text-ink uppercase">Nouveau</span>
            @endif
            @unless ($product->inStock())
                <span class="bg-ink/80 px-2 py-1 text-[.65rem] tracking-[.14em] text-ivory uppercase">Épuisé</span>
            @endunless
        </div>

        {{-- Favourite --}}
        <button type="button" x-data="wishlistButton('{{ route('wishlist.toggle', $product) }}', {{ \App\Support\Wishlist::has($product->id) ? 'true' : 'false' }})"
                @click="toggle()" :aria-pressed="active" aria-label="Ajouter aux favoris"
                class="absolute right-3 top-3 flex size-9 items-center justify-center rounded-full bg-ivory/90 text-ink transition hover:bg-white">
            <i class="fa-heart text-sm" :class="active ? 'fa-solid text-brand' : 'fa-regular'"></i>
        </button>

        {{-- Quick add (desktop hover) --}}
        @if ($quickAdd)
            <div class="absolute inset-x-3 bottom-3 hidden translate-y-2 opacity-0 transition duration-300 group-hover/card:translate-y-0 group-hover/card:opacity-100 lg:block">
                @if ($sizes)
                    <div x-show="open" x-cloak class="mb-2 bg-white/95 p-3 backdrop-blur">
                        <p class="mb-2 text-[.65rem] tracking-[.16em] text-muted uppercase">Choisissez votre taille</p>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach ($sizes as $size)
                                <button type="button" @click="choose(@js($size))" class="min-w-9 border border-line px-2 py-1.5 text-xs text-ink transition hover:border-ink hover:bg-ink hover:text-ivory">{{ str_replace('Pointure ', '', $size) }}</button>
                            @endforeach
                        </div>
                    </div>
                @endif
                <button type="button" @click="{{ $sizes ? 'open = !open' : 'submit()' }}" :disabled="loading"
                        class="flex w-full items-center justify-center gap-2 bg-ink py-3 text-[.7rem] font-medium tracking-[.16em] text-ivory uppercase transition hover:bg-brand">
                    <i class="fa-solid" :class="loading ? 'fa-spinner fa-spin' : 'fa-plus'"></i> Ajout rapide
                </button>
                <p x-cloak x-show="error" x-text="error" class="mt-1 bg-white px-2 py-1 text-xs text-danger"></p>
            </div>
        @endif
    </div>

    <div class="flex flex-1 flex-col pt-4">
        @if ($product->category)
            <p class="mb-1 text-[.65rem] tracking-[.18em] text-muted uppercase">{{ $product->category->name }}</p>
        @endif
        <h3 class="font-sans text-[.95rem] font-normal leading-snug">
            <a href="{{ $url }}" class="text-ink transition hover:text-brand">{{ $product->name }}</a>
        </h3>
        <div class="mt-1.5 flex flex-wrap items-baseline gap-x-2">
            <span @class(['text-[.95rem] font-medium', 'text-brand' => $product->hasDiscount(), 'text-ink' => ! $product->hasDiscount()])>{{ money($product->finalPrice()) }}</span>
            @if ($product->hasDiscount())
                <del class="text-sm text-muted">{{ money($product->price) }}</del>
            @endif
        </div>
    </div>
</article>
