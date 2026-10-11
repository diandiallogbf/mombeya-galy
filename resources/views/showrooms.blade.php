@extends('layouts.app')

@section('title', 'Nos maisons')

@section('content')
    <x-page-header title="Nos maisons" :breadcrumbs="['Nos maisons' => null]" kicker="Venez nous rencontrer"
                   subtitle="Essayez nos pièces, rencontrez nos tailleurs et déposez vos vêtements au pressing dans l'une de nos maisons." />

    <div class="container-shop mt-14">
        @if ($showrooms->isEmpty())
            <p class="py-16 text-center text-muted">Nos maisons seront bientôt présentées ici.</p>
        @else
            <div class="grid gap-x-6 gap-y-14 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($showrooms as $showroom)
                    <article class="flex flex-col">
                        <div class="mb-5 overflow-hidden bg-sand">
                            <img src="{{ $showroom->imageUrl() }}" alt="{{ $showroom->name }}" loading="lazy" class="aspect-[4/3] w-full object-cover">
                        </div>
                        <div class="mb-2 flex flex-wrap items-center gap-2">
                            <h2 class="font-display text-2xl">{{ $showroom->name }}</h2>
                            @if ($showroom->accepts_pressing)
                                <span class="border border-line px-2 py-0.5 text-[.6rem] tracking-[.14em] text-muted uppercase">Dépôt pressing</span>
                            @endif
                        </div>
                        @if ($showroom->address)<p class="text-sm text-body">{{ $showroom->address }}</p>@endif
                        @if ($showroom->phone)
                            <a href="tel:{{ preg_replace('/\s+/', '', $showroom->phone) }}" class="text-sm text-body hover:text-brand">{{ $showroom->phone }}</a>
                        @endif
                        <details class="group mt-4 border-y border-line">
                            <summary class="flex cursor-pointer list-none items-center justify-between py-3 text-[.7rem] tracking-[.16em] text-ink uppercase">
                                Horaires <i class="fa-solid fa-plus text-xs transition group-open:rotate-45"></i>
                            </summary>
                            <ul class="space-y-1 pb-4 text-sm">
                                @foreach (\App\Models\Showroom::DAYS as $key => $day)
                                    <li class="flex justify-between"><span class="text-ink">{{ $day }}</span><span class="text-muted">{{ $showroom->opening_hours[$key] ?? '—' }}</span></li>
                                @endforeach
                            </ul>
                        </details>
                        @if ($showroom->mapUrl())
                            <a href="{{ $showroom->mapUrl() }}" target="_blank" rel="noopener" class="link-arrow mt-5 self-start">Localiser la maison <i class="fa-solid fa-arrow-right text-[.6rem]"></i></a>
                        @endif
                    </article>
                @endforeach
            </div>
        @endif

        <div class="mt-20 grid gap-px border border-line bg-line md:grid-cols-3">
            @foreach ([
                ['fa-phone', 'Téléphone', setting('phone'), 'tel:'.preg_replace('/\s+/', '', (string) setting('phone'))],
                ['fa-whatsapp', 'WhatsApp', 'Écrivez-nous', whatsapp_url()],
                ['fa-envelope', 'Email', setting('email'), 'mailto:'.setting('email')],
            ] as [$icon, $label, $value, $href])
                <a href="{{ $href }}" @if (str_starts_with($href, 'http')) target="_blank" rel="noopener" @endif class="flex items-center gap-4 bg-white p-6 transition hover:bg-ivory">
                    <i class="{{ $icon === 'fa-whatsapp' ? 'fa-brands' : 'fa-solid' }} {{ $icon }} text-xl text-ink"></i>
                    <span><span class="kicker block">{{ $label }}</span><span class="text-ink">{{ $value }}</span></span>
                </a>
            @endforeach
        </div>
    </div>
@endsection
