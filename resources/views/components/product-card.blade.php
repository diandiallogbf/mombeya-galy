@props(['product', 'hover' => true])

@php
    $sizes = $product->sizeList();
    $colors = $product->colorList();
    $url = route('product.show', $product);
@endphp

<div {{ $attributes->class(['group/card relative flex h-full flex-col rounded-lg bg-white transition duration-150 hover:z-20 hover:shadow-card']) }}
     x-data="addToCart({ productId: {{ $product->id }}, needsSize: {{ $sizes ? 'true' : 'false' }}, needsColor: {{ $colors ? 'true' : 'false' }}, url: '{{ route('cart.store') }}' })">

    @if ($product->hasDiscount())
        <span class="promo-badge">Promo</span>
    @elseif ($product->is_new)
        <span class="absolute top-3 left-3 z-10 rounded bg-azure px-2 py-0.5 text-[.65rem] font-medium uppercase tracking-wide text-white">Nouveau</span>
    @endif

    <button type="button" title="Aperçu"
            @click="$dispatch('quick-view', '{{ route('product.quick-view', $product) }}')"
            class="absolute top-3 right-3 z-10 hidden size-9 items-center justify-center rounded-full bg-white text-ink opacity-0 shadow transition group-hover/card:opacity-100 hover:text-brand lg:flex">
        <i class="fa-solid fa-magnifying-glass-plus"></i>
    </button>

    <a href="{{ $url }}" class="block overflow-hidden rounded-t-lg bg-soft">
        <img src="{{ $product->mainImageUrl() }}" alt="{{ $product->name }}" loading="lazy"
             class="aspect-[3/4] w-full object-cover transition duration-500 group-hover/card:scale-[1.03]">
    </a>

    <div class="flex flex-1 flex-col px-3 pt-3 pb-3">
        @if ($product->category)
            <a href="{{ route('shop.category', $product->category) }}" class="mb-1 truncate text-xs text-muted hover:text-brand">{{ $product->category->name }}</a>
        @endif
        @unless ($product->inStock())
            <div class="mb-1 rounded bg-danger/10 px-2 py-1 text-center text-xs font-medium text-danger">Fin de stock</div>
        @endunless
        <h3 class="mb-1 text-sm font-normal leading-snug">
            <a href="{{ $url }}" class="text-ink transition hover:text-brand">{{ $product->name }}</a>
        </h3>
        <div class="mt-auto flex flex-wrap items-baseline gap-x-2">
            @if ($product->hasDiscount())
                <del class="text-sm text-muted">{{ money($product->price) }}</del>
            @endif
            <span class="text-brand">{{ money($product->finalPrice()) }}</span>
        </div>
    </div>

    {{-- Hover panel (desktop) --}}
    @if ($hover)
        <div class="invisible absolute inset-x-0 top-full -mt-2 hidden rounded-b-lg bg-white px-3 pb-3 pt-1 opacity-0 shadow-card transition duration-150 group-hover/card:visible group-hover/card:opacity-100 lg:block"
             style="clip-path: inset(0 -2rem -2rem -2rem);">
            @if ($product->inStock())
                @if ($colors)
                    <div class="mb-2 flex flex-wrap gap-1.5">
                        @foreach ($colors as $color)
                            <button type="button" @click="color = @js($color)" :class="color === @js($color) ? 'border-brand text-brand' : 'border-line text-body'"
                                    class="rounded-full border px-2 py-0.5 text-[.7rem] transition">{{ $color }}</button>
                        @endforeach
                    </div>
                @endif
                <div class="flex gap-2">
                    @if ($sizes)
                        <select x-model="size" class="form-control flex-1 px-2 py-1.5 text-xs" aria-label="Taille">
                            <option value="">Taille</option>
                            @foreach ($sizes as $size)
                                <option value="{{ $size }}">{{ $size }}</option>
                            @endforeach
                        </select>
                    @endif
                    <button type="button" @click="submit()" :disabled="loading" class="btn btn-primary btn-sm flex-1 whitespace-nowrap">
                        <i class="fa-solid" :class="loading ? 'fa-spinner fa-spin' : 'fa-cart-plus'"></i> Ajouter
                    </button>
                </div>
                <p x-cloak x-show="error" x-text="error" class="mt-1 text-xs text-danger"></p>
            @else
                <div class="rounded bg-danger/10 px-2 py-1.5 text-center text-xs text-danger">Ce produit est en rupture de stock</div>
            @endif
            <button type="button" @click="$dispatch('quick-view', '{{ route('product.quick-view', $product) }}')" class="mt-2 block w-full text-center text-xs text-muted hover:text-brand">
                <i class="fa-regular fa-eye"></i> Aperçu
            </button>
        </div>
    @endif
</div>
