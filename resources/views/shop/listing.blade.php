@extends('layouts.app')

@section('title', $title)

@section('content')
    @php
        $crumbs = [];
        if ($category || request()->routeIs('shop.news', 'shop.promotions', 'shop.prestige')) {
            $crumbs['Boutique'] = route('shop.index');
        }
        $crumbs[$title] = null;
        $kicker = match (true) {
            (bool) $category => 'Univers',
            request()->routeIs('shop.news') => 'Nouvelle collection',
            request()->routeIs('shop.promotions') => 'Offres du moment',
            request()->routeIs('shop.prestige') => 'Collection',
            default => 'La boutique',
        };
        $chips = \App\Models\Category::active()->where('show_in_menu', true)->get();
    @endphp

    <x-page-header :title="$title" :breadcrumbs="$crumbs" :kicker="$kicker" :subtitle="$subtitle" :image="$banner" />

    <div class="container-shop">
        {{-- Category chips --}}
        <div class="-mx-4 flex gap-2 overflow-x-auto px-4 py-6 [scrollbar-width:none] md:mx-0 md:flex-wrap md:px-0">
            <a href="{{ route('shop.index') }}" @class(['chip shrink-0', 'chip-active' => request()->routeIs('shop.index') && ! $search])>Tout</a>
            @foreach ($chips as $chip)
                <a href="{{ route('shop.category', $chip) }}" @class(['chip shrink-0', 'chip-active' => $category?->is($chip)])>{{ $chip->name }}</a>
            @endforeach
        </div>

        {{-- Toolbar --}}
        <form method="GET" class="mb-10 flex flex-col gap-4 border-y border-line py-4 md:flex-row md:items-center md:justify-between">
            <p class="text-[.72rem] tracking-[.16em] text-muted uppercase">
                {{ $products->total() }} pièce{{ $products->total() > 1 ? 's' : '' }}
                @if ($search) · « {{ $search }} » @endif
            </p>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <label class="flex items-center border-b border-ink">
                    <span class="sr-only">Rechercher</span>
                    <input type="search" name="q" value="{{ $search }}" placeholder="Rechercher…" class="w-full bg-transparent py-2 text-sm outline-none sm:w-52">
                    <button class="text-ink" aria-label="Rechercher"><i class="fa-solid fa-magnifying-glass text-sm"></i></button>
                </label>
                <label class="flex items-center gap-3">
                    <span class="text-[.7rem] tracking-[.16em] text-muted uppercase">Trier</span>
                    <select name="tri" class="border-0 border-b border-ink bg-transparent py-2 pr-8 text-sm text-ink outline-none" onchange="this.form.submit()">
                        @foreach (\App\Models\Product::SORTS as $value => $label)
                            <option value="{{ $value }}" @selected($sort === $value)>{{ str_replace('Tri ', '', $label) }}</option>
                        @endforeach
                    </select>
                </label>
            </div>
        </form>

        @if ($products->isEmpty())
            <div class="py-20 text-center">
                <p class="mb-3 font-display text-3xl text-ink">Aucune pièce pour le moment</p>
                <p class="mb-8 text-muted">Cette sélection sera bientôt enrichie. Découvrez le reste de nos collections.</p>
                <a href="{{ route('shop.index') }}" class="btn btn-outline">Voir toute la boutique</a>
            </div>
        @else
            <div class="grid grid-cols-2 gap-x-4 gap-y-12 md:grid-cols-3 lg:grid-cols-4 lg:gap-x-6">
                @foreach ($products as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
            <div class="mt-16 border-t border-line pt-8">
                {{ $products->links() }}
            </div>
        @endif
    </div>
@endsection
