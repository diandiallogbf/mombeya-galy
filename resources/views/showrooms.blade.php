@extends('layouts.app')

@section('title', 'Nos showrooms')

@section('content')
    <x-page-header title="Nos showrooms" :breadcrumbs="['Nos showrooms' => null]" light />

    <div class="container-shop relative -mt-16">
        @if ($showrooms->isEmpty())
            <div class="rounded-lg bg-white p-10 text-center text-muted shadow-lg">Nos showrooms seront bientôt listés ici.</div>
        @else
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($showrooms as $showroom)
                    <article class="flex flex-col overflow-hidden rounded-lg bg-white shadow-[0_.5rem_2rem_-.5rem_rgba(0,0,0,.18)]">
                        <img src="{{ $showroom->imageUrl() }}" alt="{{ $showroom->name }}" loading="lazy" class="h-64 w-full object-cover lg:h-72">
                        <div class="flex flex-1 flex-col p-5">
                            <h2 class="mb-1 text-lg">{{ $showroom->name }}</h2>
                            @if ($showroom->address)
                                <p class="mb-1 text-sm text-muted"><i class="fa-solid fa-location-dot mr-1 text-brand"></i>{{ $showroom->address }}</p>
                            @endif
                            @if ($showroom->phone)
                                <a href="tel:{{ preg_replace('/\s+/', '', $showroom->phone) }}" class="mb-3 text-sm text-body hover:text-brand"><i class="fa-solid fa-phone mr-1 text-brand"></i>{{ $showroom->phone }}</a>
                            @endif
                            <ul class="mb-4 space-y-1 text-sm">
                                @foreach (\App\Models\Showroom::DAYS as $key => $day)
                                    <li class="flex justify-between">
                                        <span class="text-ink"><i class="fa-solid fa-chevron-right mr-1 text-[9px] text-muted"></i>{{ $day }}</span>
                                        <span class="text-[.8125rem] text-muted">{{ $showroom->opening_hours[$key] ?? '—' }}</span>
                                    </li>
                                @endforeach
                            </ul>
                            @if ($showroom->mapUrl())
                                <hr class="mb-4 mt-auto border-line">
                                <a href="{{ $showroom->mapUrl() }}" target="_blank" rel="noopener" class="btn btn-primary btn-sm self-center"><i class="fa-solid fa-map-location-dot"></i> Localiser la boutique</a>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        @endif

        <div class="mt-10 grid gap-5 rounded-lg bg-soft p-6 md:grid-cols-3">
            <div class="flex items-center gap-4">
                <span class="flex size-12 items-center justify-center rounded-full bg-white text-xl text-brand"><i class="fa-solid fa-phone"></i></span>
                <div><p class="text-sm text-muted">Téléphone</p><a href="tel:{{ preg_replace('/\s+/', '', setting('phone')) }}" class="font-medium text-ink">{{ setting('phone') }}</a></div>
            </div>
            <div class="flex items-center gap-4">
                <span class="flex size-12 items-center justify-center rounded-full bg-white text-xl text-[#25D366]"><i class="fa-brands fa-whatsapp"></i></span>
                <div><p class="text-sm text-muted">WhatsApp</p><a href="{{ whatsapp_url() }}" target="_blank" rel="noopener" class="font-medium text-ink">Écrivez-nous</a></div>
            </div>
            <div class="flex items-center gap-4">
                <span class="flex size-12 items-center justify-center rounded-full bg-white text-xl text-ocean"><i class="fa-regular fa-envelope"></i></span>
                <div><p class="text-sm text-muted">Email</p><a href="mailto:{{ setting('email') }}" class="font-medium text-ink">{{ setting('email') }}</a></div>
            </div>
        </div>
    </div>
@endsection
