@extends('layouts.app')

@section('title', $post->title)

@section('content')
    <x-page-header :title="$post->title" :breadcrumbs="['Fondation' => route('foundation.index'), $post->title => null]" />

    <div class="container-shop">
        <article class="page-card mx-auto max-w-4xl">
            @if ($post->published_at)
                <p class="mb-4 text-sm text-muted"><i class="fa-regular fa-calendar mr-1"></i> {{ $post->published_at->format('d/m/Y') }}</p>
            @endif
            <img src="{{ $post->coverUrl() }}" alt="{{ $post->title }}" class="mb-6 max-h-[520px] w-full rounded-lg object-cover">
            <div class="prose-shop text-body">{!! $post->content !!}</div>

            @if ($post->galleryUrls())
                <div class="mt-8 grid gap-3 sm:grid-cols-3">
                    @foreach ($post->galleryUrls() as $image)
                        <a href="{{ $image }}" target="_blank"><img src="{{ $image }}" alt="" loading="lazy" class="h-56 w-full rounded-lg object-cover"></a>
                    @endforeach
                </div>
            @endif

            <div class="mt-10 rounded-lg bg-soft p-5 text-center text-sm">
                Vous aussi, soutenez la fondation : <strong class="text-ink">{{ setting('foundation_donation_number') }}</strong> (Orange Money, MTN MoMo…)
            </div>
        </article>

        @if ($others->isNotEmpty())
            <section class="mt-12">
                <h2 class="section-title mb-6">Autres actions</h2>
                <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                    @foreach ($others as $other)
                        <a href="{{ route('foundation.show', $other) }}" class="group/post block">
                            <img src="{{ $other->coverUrl() }}" alt="" loading="lazy" class="mb-2 h-48 w-full rounded-lg object-cover">
                            <h3 class="text-sm text-ink group-hover/post:text-brand">{{ $other->title }}</h3>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
@endsection
