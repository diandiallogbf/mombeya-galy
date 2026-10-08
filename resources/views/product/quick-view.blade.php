@php($images = $product->imageUrls())
<div class="grid gap-6 p-5 md:grid-cols-12 md:p-8">
    <div class="md:col-span-7" x-data="gallery(@js($images))">
        <div class="overflow-hidden rounded-lg bg-soft">
            <img :src="images[active]" src="{{ $images[0] }}" alt="{{ $product->name }}" class="aspect-[3/4] w-full object-cover">
        </div>
        @if (count($images) > 1)
            <div class="mt-3 flex gap-2">
                @foreach ($images as $i => $image)
                    <button type="button" @click="active = {{ $i }}" :class="active === {{ $i }} ? 'border-brand' : 'border-line'" class="size-16 overflow-hidden rounded border-2">
                        <img src="{{ $image }}" alt="" class="size-full object-cover">
                    </button>
                @endforeach
            </div>
        @endif
    </div>
    <div class="md:col-span-5">
        <a href="{{ route('product.show', $product) }}" class="mb-4 flex items-center gap-2 pr-10 text-xl font-medium text-ink hover:text-brand">
            {{ $product->name }} <i class="fa-solid fa-arrow-right text-sm"></i>
        </a>
        @include('product.partials.buy-box')
        <div class="mt-6 border-t border-line pt-4 text-sm">
            <h6 class="mb-1 font-medium text-ink"><i class="fa-solid fa-circle-info mr-1 text-brand"></i> Informations produit</h6>
            <div class="prose-shop">{!! $product->description !!}</div>
        </div>
    </div>
</div>
