@props(['title', 'breadcrumbs' => [], 'kicker' => null, 'subtitle' => null, 'image' => null])

{{-- Editorial page header: breadcrumb, kicker, large serif title, optional subtitle / image. --}}
<section class="border-b border-line">
    <div @class(['container-shop', 'grid items-center gap-8 py-10 md:grid-cols-2 md:py-14' => $image, 'py-10 text-center md:py-16' => ! $image])>
        <div>
            <nav aria-label="Fil d'Ariane" @class(['mb-5 flex flex-wrap items-center gap-2 text-[.68rem] tracking-[.18em] text-muted uppercase', 'justify-center' => ! $image])>
                <a href="{{ route('home') }}" class="hover:text-ink">Accueil</a>
                @foreach ($breadcrumbs as $label => $url)
                    <span class="text-line">/</span>
                    @if ($url)
                        <a href="{{ $url }}" class="hover:text-ink">{{ $label }}</a>
                    @else
                        <span class="text-ink">{{ $label }}</span>
                    @endif
                @endforeach
            </nav>
            @if ($kicker)
                <p class="kicker mb-3">{{ $kicker }}</p>
            @endif
            <h1 class="display-title">{{ $title }}</h1>
            @if ($subtitle)
                <p @class(['mt-5 max-w-2xl text-[1.02rem] leading-relaxed text-body', 'mx-auto' => ! $image])>{{ $subtitle }}</p>
            @endif
            {{ $slot }}
        </div>
        @if ($image)
            <div class="overflow-hidden">
                <img src="{{ $image }}" alt="" class="aspect-[4/3] w-full object-cover">
            </div>
        @endif
    </div>
</section>
