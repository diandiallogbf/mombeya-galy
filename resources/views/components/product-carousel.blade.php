@props(['products'])

<div class="relative" data-carousel-wrapper>
    <div class="swiper overflow-visible!" data-carousel>
        <div class="swiper-wrapper">
            @foreach ($products as $product)
                <div class="swiper-slide h-auto!">
                    <x-product-card :product="$product" />
                </div>
            @endforeach
        </div>
    </div>
    <div class="mt-8 flex items-center justify-between gap-6">
        <div class="swiper-pagination static! flex w-auto! flex-1 gap-1"></div>
        <div class="flex gap-2">
            <button type="button" data-prev aria-label="Précédent" class="flex size-11 items-center justify-center rounded-full border border-ink text-ink transition hover:bg-ink hover:text-ivory disabled:opacity-30"><i class="fa-solid fa-arrow-left text-sm"></i></button>
            <button type="button" data-next aria-label="Suivant" class="flex size-11 items-center justify-center rounded-full border border-ink text-ink transition hover:bg-ink hover:text-ivory disabled:opacity-30"><i class="fa-solid fa-arrow-right text-sm"></i></button>
        </div>
    </div>
</div>
