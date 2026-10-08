@php
    $sizes = $product->sizeList();
    $colors = $product->colorList();
@endphp

<div x-data="addToCart({ productId: {{ $product->id }}, needsSize: {{ $sizes ? 'true' : 'false' }}, needsColor: {{ $colors ? 'true' : 'false' }}, url: '{{ route('cart.store') }}' })">
    <div class="mb-3 flex items-center justify-between">
        <div class="flex items-center gap-1 text-sm text-[#fea569]">
            @for ($i = 0; $i < 5; $i++)<i class="fa-solid fa-star"></i>@endfor
        </div>
        <span class="text-xs text-muted">Réf. {{ $product->reference }}</span>
    </div>

    <div class="mb-3 flex flex-wrap items-center gap-3">
        @if ($product->hasDiscount())
            <del class="text-lg text-muted">{{ money($product->price) }}</del>
        @endif
        <span class="text-[1.75rem] font-normal text-brand">{{ money($product->finalPrice()) }}</span>
        @if ($product->hasDiscount())
            <span class="rounded bg-danger px-2 py-0.5 text-xs font-medium text-white">PROMO -{{ $product->discountPercent() }}%</span>
        @endif
    </div>

    <div class="mb-5 flex flex-wrap gap-2 text-xs">
        @if ($product->is_deliverable)
            <span class="inline-flex items-center gap-1.5 rounded-full bg-success/15 px-3 py-1 font-medium text-[#1f7a52]"><i class="fa-solid fa-shield-halved"></i> Livrable</span>
        @endif
        @if ($product->inStock())
            <span class="inline-flex items-center gap-1.5 rounded-full bg-azure/15 px-3 py-1 font-medium text-ocean"><i class="fa-solid fa-check"></i> En stock</span>
        @else
            <span class="inline-flex items-center gap-1.5 rounded-full bg-danger/15 px-3 py-1 font-medium text-danger"><i class="fa-solid fa-xmark"></i> Fin de stock</span>
        @endif
    </div>

    @if ($product->inStock())
        @if ($colors)
            <div class="mb-4">
                <span class="form-label">Couleur : <span class="font-normal text-muted" x-text="color"></span></span>
                <div class="flex flex-wrap gap-2">
                    @foreach ($colors as $color)
                        <button type="button" @click="color = @js($color)"
                                :class="color === @js($color) ? 'border-brand bg-brand-light text-brand' : 'border-line text-body hover:border-muted'"
                                class="rounded-full border px-3 py-1 text-sm transition">{{ $color }}</button>
                    @endforeach
                </div>
            </div>
        @endif

        @if ($sizes)
            <div class="mb-4">
                <label class="form-label" for="size-{{ $product->id }}">Taille :</label>
                <select id="size-{{ $product->id }}" x-model="size" class="form-control">
                    <option value="">Choisissez une taille</option>
                    @foreach ($sizes as $size)
                        <option value="{{ $size }}">{{ $size }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        <div class="mb-2 flex gap-3">
            <input type="number" min="1" max="{{ min(config('shop.max_quantity'), $product->stock) }}" x-model.number="quantity" aria-label="Quantité" class="form-control w-20 text-center">
            <button type="button" @click="submit()" :disabled="loading" class="btn btn-primary btn-shadow flex-1">
                <i class="fa-solid" :class="loading ? 'fa-spinner fa-spin' : 'fa-cart-shopping'"></i> Ajouter au panier
            </button>
        </div>
        <p x-cloak x-show="error" x-text="error" class="form-error mb-2"></p>
    @endif

    <a href="{{ whatsapp_url('Bonjour Mombeya Galy, je suis intéressé(e) par : '.$product->name.' (réf. '.$product->reference.') – '.route('product.show', $product)) }}"
       target="_blank" rel="noopener" class="btn mt-2 w-full border border-[#25D366] text-[#128C7E] hover:bg-[#25D366] hover:text-white">
        <i class="fa-brands fa-whatsapp text-lg"></i> Commander sur WhatsApp
    </a>
</div>
