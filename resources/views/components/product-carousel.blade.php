@props(['products', 'arrows' => true])

<div class="relative" data-carousel-wrapper>
    <div class="swiper" data-carousel>
        <div class="swiper-wrapper">
            @foreach ($products as $product)
                <div class="swiper-slide h-auto! pb-2">
                    <x-product-card :product="$product" :hover="false" class="border border-line/70 hover:border-transparent" />
                </div>
            @endforeach
        </div>
    </div>
    @if ($arrows)
        <button type="button" data-prev aria-label="Précédent"
                class="absolute -left-4 top-[40%] z-10 hidden size-11 -translate-y-1/2 items-center justify-center rounded-full bg-white text-ink shadow-lg transition hover:bg-brand hover:text-white xl:flex">
            <i class="fa-solid fa-chevron-left"></i>
        </button>
        <button type="button" data-next aria-label="Suivant"
                class="absolute -right-4 top-[40%] z-10 hidden size-11 -translate-y-1/2 items-center justify-center rounded-full bg-white text-ink shadow-lg transition hover:bg-brand hover:text-white xl:flex">
            <i class="fa-solid fa-chevron-right"></i>
        </button>
    @endif
    <div class="swiper-pagination static! mt-4 flex justify-center gap-1"></div>
</div>
