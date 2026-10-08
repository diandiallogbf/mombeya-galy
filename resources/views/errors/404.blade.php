@extends('layouts.app')

@section('title', 'Page introuvable')

@section('content')
    <div class="container-shop py-20 text-center">
        <p class="mb-2 text-7xl font-bold text-brand">404</p>
        <h1 class="mb-3 text-2xl">Page introuvable</h1>
        <p class="mb-8 text-muted">La page que vous cherchez n'existe pas ou a été déplacée.</p>
        <div class="flex flex-wrap justify-center gap-3">
            <a href="{{ route('home') }}" class="btn btn-primary"><i class="fa-solid fa-house"></i> Accueil</a>
            <a href="{{ route('shop.index') }}" class="btn btn-outline">Voir la boutique</a>
        </div>
    </div>
@endsection
