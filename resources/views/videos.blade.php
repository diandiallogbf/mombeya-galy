@extends('layouts.app')

@section('title', 'Vidéos')

@section('content')
    <x-page-header title="Vidéos Mombeya Galy" :breadcrumbs="['Vidéos' => null]" />

    <div class="container-shop" x-data="{ playing: null }" @keydown.escape.window="playing = null">
        <div class="page-card">
            @if ($videos->isEmpty())
                <div class="py-16 text-center">
                    <i class="fa-brands fa-youtube mb-4 text-5xl text-line"></i>
                    <p class="text-muted">Nos vidéos arrivent très bientôt. Suivez-nous sur les réseaux sociaux !</p>
                </div>
            @else
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($videos as $video)
                        <button type="button" @click="playing = @js($video->embedUrl())" class="group/video text-left">
                            <div class="relative mb-3 overflow-hidden rounded-lg bg-navy">
                                <img src="{{ $video->thumbnailUrl() }}" alt="{{ $video->title }}" loading="lazy" class="aspect-video w-full object-cover transition duration-500 group-hover/video:scale-105">
                                <span class="absolute inset-0 flex items-center justify-center bg-black/25">
                                    <span class="flex size-16 items-center justify-center rounded-full bg-brand text-2xl text-white shadow-lg transition group-hover/video:scale-110"><i class="fa-solid fa-play ml-1"></i></span>
                                </span>
                            </div>
                            <h2 class="text-base text-ink group-hover/video:text-brand">{{ $video->title }}</h2>
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        <div x-cloak x-show="playing" x-transition.opacity class="fixed inset-0 z-[70] flex items-center justify-center bg-black/85 p-4" @click.self="playing = null">
            <button type="button" @click="playing = null" class="absolute right-5 top-5 text-3xl text-white" aria-label="Fermer"><i class="fa-solid fa-xmark"></i></button>
            <div class="aspect-video w-full max-w-5xl">
                <template x-if="playing">
                    <iframe :src="playing" class="size-full rounded-lg" allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen></iframe>
                </template>
            </div>
        </div>
    </div>
@endsection
