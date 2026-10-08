@extends('layouts.app')

@section('title', 'Fondation Mombeya Galy')

@section('content')
    <x-page-header title="Fondation Mombeya Galy" :breadcrumbs="['Fondation' => null]" />

    <div class="container-shop">
        <div class="page-card">
            <div class="mb-4 rounded-lg border border-azure/40 bg-azure/10 p-4 text-sm text-ocean">
                <i class="fa-solid fa-hand-holding-heart mr-1"></i>
                Participez aux dons en envoyant par Orange Money, MTN MoMo, Western Union, Ria ou MoneyGram au numéro ci-dessous :
                <strong class="text-ink">{{ setting('foundation_donation_name') }} – {{ setting('foundation_donation_number') }}</strong>
            </div>
            <div class="mb-8 rounded-lg border border-success/40 bg-success/10 p-5 text-center">
                <p class="mb-3 font-bold text-[#1f7a52]">Demande d'aide</p>
                <a href="{{ route('foundation.aid') }}" class="btn btn-primary btn-sm">Faire une demande</a>
            </div>

            @if ($posts->isEmpty())
                <p class="py-10 text-center text-muted">Les actions de la fondation seront bientôt publiées ici.</p>
            @else
                <div class="grid grid-cols-2 gap-x-3 gap-y-8 lg:grid-cols-4 lg:gap-x-6">
                    @foreach ($posts as $post)
                        <a href="{{ route('foundation.show', $post) }}" class="group/post block overflow-hidden rounded-lg bg-white transition hover:shadow-card">
                            <img src="{{ $post->coverUrl() }}" alt="{{ $post->title }}" loading="lazy" class="h-60 w-full object-cover transition duration-500 group-hover/post:scale-105 lg:h-80">
                            <div class="p-3">
                                @if ($post->published_at)
                                    <p class="mb-1 text-xs text-muted">{{ $post->published_at->format('d/m/Y') }}</p>
                                @endif
                                <h2 class="text-sm font-medium text-ink group-hover/post:text-brand">{{ $post->title }}</h2>
                            </div>
                        </a>
                    @endforeach
                </div>
                <div class="mt-8">{{ $posts->links() }}</div>
            @endif
        </div>
    </div>
@endsection
