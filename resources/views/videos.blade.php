@extends('layouts.app')

@section('title', 'Vidéos')

@section('content')
    <x-page-header title="Vidéos" :breadcrumbs="['Vidéos' => null]" kicker="Dans les coulisses" subtitle="Défilés, ateliers et collections en images." />

    <div class="container-shop mt-14" x-data="{ playing: null }" @keydown.escape.window="playing = null">
        @if ($videos->isEmpty())
            <div class="py-16 text-center">
                <i class="fa-brands fa-youtube mb-6 text-5xl text-line"></i>
                <p class="mb-3 font-display text-3xl text-ink">Nos vidéos arrivent très bientôt</p>
                <p class="text-muted">Suivez-nous sur les réseaux sociaux pour ne rien manquer.</p>
            </div>
        @else
            <div class="grid gap-x-6 gap-y-12 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($videos as $video)
                    <button type="button" @click="playing = @js($video->embedUrl())" class="group text-left">
                        <div class="relative mb-4 overflow-hidden bg-night">
                            <img src="{{ $video->thumbnailUrl() }}" alt="{{ $video->title }}" loading="lazy" class="aspect-video w-full object-cover transition duration-700 group-hover:scale-105">
                            <span class="absolute inset-0 flex items-center justify-center bg-black/20">
                                <span class="flex size-16 items-center justify-center rounded-full bg-ivory text-xl text-ink transition group-hover:scale-110"><i class="fa-solid fa-play ml-1"></i></span>
                            </span>
                        </div>
                        <h2 class="font-display text-2xl text-ink group-hover:text-brand">{{ $video->title }}</h2>
                    </button>
                @endforeach
            </div>
        @endif

        <div x-cloak x-show="playing" x-transition.opacity class="fixed inset-0 z-[70] flex items-center justify-center bg-black/90 p-4" @click.self="playing = null">
            <button type="button" @click="playing = null" class="absolute right-5 top-5 text-3xl text-white" aria-label="Fermer"><i class="fa-solid fa-xmark"></i></button>
            <div class="aspect-video w-full max-w-5xl">
                <template x-if="playing">
                    <iframe :src="playing" class="size-full" allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen></iframe>
                </template>
            </div>
        </div>
    </div>
@endsection
