@props(['title', 'breadcrumbs' => [], 'light' => false, 'mobileTitle' => true])

<div @class(['pt-8 pb-24 lg:pt-10', 'bg-navy text-white' => ! $light, 'bg-soft text-ink' => $light])>
    <div class="container-shop flex flex-col-reverse items-center gap-3 text-center lg:flex-row lg:justify-between lg:text-left">
        <h1 @class(['text-2xl lg:text-[1.75rem]', 'hidden md:block' => ! $mobileTitle, 'text-white' => ! $light, 'text-ink' => $light])>{{ $title }}</h1>
        <nav aria-label="Fil d'Ariane" @class(['flex flex-wrap items-center justify-center gap-2 text-sm', 'text-white/75' => ! $light, 'text-muted' => $light])>
            <a href="{{ route('home') }}" @class(['flex items-center gap-1.5', 'hover:text-white' => ! $light, 'hover:text-brand' => $light])><i class="fa-solid fa-house text-xs"></i> Accueil</a>
            @foreach ($breadcrumbs as $label => $url)
                <i class="fa-solid fa-chevron-right text-[9px] opacity-60"></i>
                @if ($url)
                    <a href="{{ $url }}" @class(['hover:text-white' => ! $light, 'hover:text-brand' => $light])>{{ $label }}</a>
                @else
                    <span @class(['text-white' => ! $light, 'text-ink' => $light])>{{ $label }}</span>
                @endif
            @endforeach
        </nav>
    </div>
    {{ $slot }}
</div>
