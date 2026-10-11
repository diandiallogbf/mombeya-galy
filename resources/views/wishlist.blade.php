@extends('layouts.app')

@section('title', 'Mes favoris')

@section('content')
    <x-page-header title="Mes favoris" :breadcrumbs="['Favoris' => null]" kicker="Votre sélection"
                   :subtitle="auth()->check() ? 'Vos coups de cœur sont enregistrés dans votre compte.' : 'Connectez-vous pour retrouver vos favoris sur tous vos appareils.'" />

    <div class="container-shop mt-14">
        @if ($products->isEmpty())
            <div class="py-16 text-center">
                <i class="fa-regular fa-heart mb-6 text-5xl text-line"></i>
                <p class="mb-3 font-display text-3xl text-ink">Aucun favori pour le moment</p>
                <p class="mb-8 text-muted">Touchez le cœur sur une pièce pour la retrouver ici.</p>
                <a href="{{ route('shop.index') }}" class="btn btn-dark">Découvrir la boutique</a>
            </div>
        @else
            <p class="mb-8 text-[.72rem] tracking-[.16em] text-muted uppercase">{{ $products->count() }} pièce{{ $products->count() > 1 ? 's' : '' }}</p>
            <div class="grid grid-cols-2 gap-x-4 gap-y-12 md:grid-cols-3 lg:grid-cols-4 lg:gap-x-6">
                @foreach ($products as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
            @guest
                <div class="mt-16 border border-line bg-white p-8 text-center">
                    <p class="mb-4 font-display text-2xl text-ink">Gardez vos favoris pour toujours</p>
                    <a href="{{ route('login') }}" class="btn btn-outline">Se connecter ou créer un compte</a>
                </div>
            @endguest
        @endif
    </div>
@endsection
