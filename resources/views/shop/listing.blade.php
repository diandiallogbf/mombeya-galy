@extends('layouts.app')

@section('title', $title)

@section('content')
    @php
        $crumbs = [];
        if ($category || request()->routeIs('shop.news', 'shop.promotions', 'shop.prestige')) {
            $crumbs['Boutique'] = route('shop.index');
        }
        $crumbs[$title] = null;
    @endphp

    {{-- Mobile category banner --}}
    @if ($banner)
        <div class="relative h-40 overflow-hidden md:hidden">
            <img src="{{ $banner }}" alt="" class="absolute inset-0 size-full object-cover">
            <div class="absolute inset-0 flex items-center justify-center bg-black/50 px-4 text-center">
                <h2 class="text-2xl font-medium text-white">{{ $title }}</h2>
            </div>
        </div>
    @endif

    <x-page-header :title="$title" :breadcrumbs="$crumbs" :light="$prestige" :mobile-title="! $banner" />

    <div class="container-shop">
        <div class="page-card">
            {{-- Toolbar: search + sort --}}
            <form method="GET" class="mb-6 flex flex-col gap-3 border-b border-line pb-5 md:flex-row md:items-center md:justify-between">
                <div class="flex items-center gap-2 text-sm text-muted">
                    <i class="fa-solid fa-layer-group text-brand"></i>
                    <span><strong class="text-ink">{{ $products->total() }}</strong> article{{ $products->total() > 1 ? 's' : '' }}</span>
                    @if ($subtitle)
                        <span class="hidden lg:inline">· {{ $subtitle }}</span>
                    @endif
                </div>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <div class="relative">
                        <input type="search" name="q" value="{{ $search }}" placeholder="Rechercher…" class="form-control py-2 pr-10 text-sm sm:w-56">
                        <button class="absolute right-3 top-1/2 -translate-y-1/2 text-muted hover:text-brand" aria-label="Rechercher"><i class="fa-solid fa-magnifying-glass"></i></button>
                    </div>
                    <label class="flex items-center gap-2 text-sm">
                        <span class="whitespace-nowrap text-ink">Trier par :</span>
                        <select name="tri" class="form-control py-2 text-sm" onchange="this.form.submit()">
                            @foreach (\App\Models\Product::SORTS as $value => $label)
                                <option value="{{ $value }}" @selected($sort === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </label>
                </div>
            </form>

            @if ($products->isEmpty())
                <div class="py-16 text-center">
                    <i class="fa-solid fa-box-open mb-4 text-5xl text-line"></i>
                    <p class="mb-6 text-muted">Aucun produit pour le moment dans cette sélection.</p>
                    <a href="{{ route('shop.index') }}" class="btn btn-outline">Voir toute la boutique</a>
                </div>
            @else
                <div class="grid grid-cols-2 gap-x-2 gap-y-8 pb-6 sm:grid-cols-3 lg:grid-cols-4 lg:gap-x-6">
                    @foreach ($products as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>
                <div class="mt-6 border-t border-line pt-6">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
