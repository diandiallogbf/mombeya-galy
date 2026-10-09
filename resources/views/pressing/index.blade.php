@extends('layouts.app')

@section('title', 'Mombeya Galy Pressing')
@section('description', 'Pressing Mombeya Galy à Conakry : lavage, nettoyage à sec, amidonnage du bazin et repassage. Collecte et livraison à domicile.')

@section('content')
    @php
        $formules = $servicesByCategory->pull('formule');
        $zoneNames = $zones->pluck('name')->join(', ', ' et ');
    @endphp

    {{-- ============ HERO ============ --}}
    <section class="relative overflow-hidden bg-gradient-to-br from-navy via-navy to-ocean text-white">
        <div class="pointer-events-none absolute -right-24 -top-24 size-96 rounded-full bg-azure/20"></div>
        <div class="pointer-events-none absolute -bottom-32 right-1/3 size-72 rounded-full bg-brand/20"></div>
        <div class="container-shop relative grid items-center gap-10 py-14 lg:grid-cols-2 lg:py-20">
            <div>
                <nav class="mb-6 flex items-center gap-2 text-sm text-white/70">
                    <a href="{{ route('home') }}" class="hover:text-white"><i class="fa-solid fa-house text-xs"></i> Accueil</a>
                    <i class="fa-solid fa-chevron-right text-[9px] opacity-60"></i>
                    <span class="text-white">Pressing</span>
                </nav>
                <p class="mb-3 text-sm font-bold uppercase tracking-[.2em] text-azure">Mombeya Galy Pressing</p>
                <h1 class="mb-5 text-4xl font-bold leading-tight text-white lg:text-5xl">Vos tenues entre de bonnes mains</h1>
                <p class="mb-8 max-w-xl text-lg text-white/80">
                    Lavage, nettoyage à sec, amidonnage du bazin et repassage.
                    @if ($zones->isNotEmpty())
                        Collecte et livraison à domicile à {{ $zoneNames }},
                    @endif
                    prêt en {{ setting('pressing_delay_text') }}.
                </p>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('pressing.create') }}" class="btn btn-primary btn-shadow"><i class="fa-solid fa-calendar-check"></i> Réserver un enlèvement</a>
                    <a href="{{ whatsapp_url('Bonjour Mombeya Galy Pressing, je souhaite faire nettoyer des vêtements.') }}" target="_blank" rel="noopener" class="btn bg-[#25D366] text-white hover:bg-[#1da851]"><i class="fa-brands fa-whatsapp text-lg"></i> WhatsApp</a>
                </div>
            </div>
            <div class="relative mx-auto hidden size-80 lg:block">
                <div class="absolute inset-0 rounded-full border-2 border-dashed border-white/20"></div>
                <div class="absolute inset-8 flex items-center justify-center rounded-full bg-white/10 backdrop-blur">
                    <i class="fa-solid fa-shirt text-[7rem] text-white"></i>
                </div>
                <span class="absolute left-2 top-10 flex size-16 items-center justify-center rounded-full bg-brand text-2xl shadow-lg"><i class="fa-solid fa-soap"></i></span>
                <span class="absolute bottom-8 right-0 flex size-16 items-center justify-center rounded-full bg-azure text-2xl shadow-lg"><i class="fa-solid fa-truck-fast"></i></span>
                <span class="absolute -bottom-2 left-10 flex size-14 items-center justify-center rounded-full bg-white text-xl text-navy shadow-lg"><i class="fa-solid fa-star"></i></span>
            </div>
        </div>
    </section>

    {{-- ============ HOW IT WORKS ============ --}}
    <section class="container-shop relative z-10 -mt-8">
        <div class="grid gap-4 rounded-xl bg-white p-6 shadow-lg sm:grid-cols-2 lg:grid-cols-4 lg:p-8">
            @foreach ([
                ['fa-calendar-check', '1. Vous réservez', 'En ligne ou sur WhatsApp, en 2 minutes.'],
                ['fa-truck-pickup', '2. On collecte', 'Chez vous au créneau choisi, ou dépôt dans un showroom.'],
                ['fa-soap', '3. On nettoie', 'Lavage, nettoyage à sec, amidon et repassage dans notre atelier.'],
                ['fa-house-circle-check', '4. On vous livre', 'Propre et repassé en '.setting('pressing_delay_text').' (24h en express).'],
            ] as [$icon, $title, $text])
                <div class="flex gap-4">
                    <span class="flex size-12 shrink-0 items-center justify-center rounded-full bg-brand-light text-xl text-brand"><i class="fa-solid {{ $icon }}"></i></span>
                    <div>
                        <h2 class="mb-1 text-base font-medium text-ink">{{ $title }}</h2>
                        <p class="text-sm text-muted">{{ $text }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ============ SERVICES & PRICES ============ --}}
    @if ($servicesByCategory->isNotEmpty())
        <section class="container-shop mt-16" x-data="{ tab: @js($servicesByCategory->keys()->first()) }">
            <h2 class="section-title">Nos services et tarifs</h2>
            <p class="mt-1 text-center text-muted">Prix par pièce · Option express 24h : +{{ $expressPercent }} %</p>
            <hr class="my-6 border-line">

            <div class="mb-8 flex flex-wrap justify-center gap-2">
                @foreach ($servicesByCategory as $key => $group)
                    <button type="button" @click="tab = @js($key)"
                            :class="tab === @js($key) ? 'bg-brand text-white border-brand' : 'bg-white text-body border-line hover:border-brand hover:text-brand'"
                            class="rounded-full border px-5 py-2 text-sm font-medium transition">{{ \App\Models\PressingService::CATEGORIES[$key] }}</button>
                @endforeach
            </div>

            @foreach ($servicesByCategory as $key => $group)
                <div x-show="tab === @js($key)" @if (! $loop->first) x-cloak @endif class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($group as $service)
                        <div class="flex items-center gap-4 rounded-lg border border-line p-4 transition hover:border-brand hover:shadow-card">
                            <span class="flex size-12 shrink-0 items-center justify-center rounded-full bg-azure/15 text-xl text-ocean"><i class="{{ $service->icon ?: 'fa-solid fa-shirt' }}"></i></span>
                            <div class="min-w-0 flex-1">
                                <h3 class="text-[.95rem] font-medium text-ink">{{ $service->name }}</h3>
                                @if ($service->description)
                                    <p class="text-xs text-muted">{{ $service->description }}</p>
                                @endif
                                <p class="mt-1 text-brand">{{ money($service->price) }} <span class="text-xs text-muted">/ {{ $service->unit }}</span></p>
                            </div>
                            <a href="{{ route('pressing.create', ['service' => $service->id]) }}" class="btn btn-sm btn-outline shrink-0" title="Réserver"><i class="fa-solid fa-plus"></i></a>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </section>
    @endif

    {{-- ============ FORMULES ============ --}}
    @if ($formules && $formules->isNotEmpty())
        <section class="mt-16 bg-soft py-14">
            <div class="container-shop">
                <h2 class="section-title">Formules & abonnements</h2>
                <p class="mt-1 mb-8 text-center text-muted">Pour les familles, les professionnels et les habitués</p>
                <div class="grid gap-6 md:grid-cols-3">
                    @foreach ($formules as $formule)
                        <div @class(['flex flex-col rounded-xl bg-white p-6 text-center shadow-card', 'ring-2 ring-brand' => $loop->index === 1])>
                            @if ($loop->index === 1)
                                <span class="mx-auto -mt-9 mb-3 rounded-full bg-brand px-3 py-1 text-xs font-medium text-white">Le plus choisi</span>
                            @endif
                            <i class="{{ $formule->icon ?: 'fa-solid fa-box' }} mb-3 text-3xl text-ocean"></i>
                            <h3 class="mb-1 text-lg font-medium text-ink">{{ $formule->name }}</h3>
                            <p class="mb-4 flex-1 text-sm text-muted">{{ $formule->description }}</p>
                            <p class="mb-4 text-2xl text-brand">{{ money($formule->price) }} <span class="text-sm text-muted">/ {{ $formule->unit }}</span></p>
                            <a href="{{ route('pressing.create', ['service' => $formule->id]) }}" class="btn btn-primary btn-sm">Choisir cette formule</a>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ============ ZONES & DROP POINTS ============ --}}
    <section class="container-shop mt-16 grid gap-6 md:grid-cols-2">
        <div class="rounded-xl border border-line p-6">
            <h2 class="mb-4 flex items-center gap-2 text-lg"><i class="fa-solid fa-truck-pickup text-brand"></i> Collecte à domicile</h2>
            @if ($zones->isEmpty())
                <p class="text-sm text-muted">La collecte à domicile arrive bientôt. En attendant, déposez vos vêtements dans un showroom.</p>
            @else
                <ul class="divide-y divide-line text-sm">
                    @foreach ($zones as $zone)
                        <li class="flex justify-between gap-3 py-2">
                            <span class="text-ink">{{ $zone->name }} @if ($zone->note)<span class="text-muted">· {{ $zone->note }}</span>@endif</span>
                            <span class="font-medium text-brand">{{ $zone->fee ? money($zone->fee) : 'Gratuit' }}</span>
                        </li>
                    @endforeach
                </ul>
                <p class="mt-3 text-xs text-muted">Frais pour la collecte et la livraison retour. D'autres communes arrivent bientôt.</p>
            @endif
        </div>
        <div class="rounded-xl border border-line p-6">
            <h2 class="mb-4 flex items-center gap-2 text-lg"><i class="fa-solid fa-store text-brand"></i> Dépôt en showroom (gratuit)</h2>
            @if ($dropPoints->isEmpty())
                <p class="text-sm text-muted">Aucun point de dépôt pour le moment.</p>
            @else
                <ul class="divide-y divide-line text-sm">
                    @foreach ($dropPoints as $point)
                        <li class="flex flex-wrap justify-between gap-2 py-2">
                            <span class="text-ink">{{ $point->name }}</span>
                            <span class="text-muted">{{ $point->address }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </section>

    {{-- ============ FAQ ============ --}}
    <section class="container-shop mt-16 max-w-3xl" x-data="{ open: 0 }">
        <h2 class="section-title mb-6">Questions fréquentes</h2>
        <div class="divide-y divide-line rounded-xl border border-line">
            @foreach ([
                ['Quels sont les délais ?', 'Vos vêtements sont prêts en '.setting('pressing_delay_text').'. Avec l\'option express, comptez 24h (supplément de '.$expressPercent.' %).'],
                ['Prenez-vous soin du bazin et des broderies ?', 'Oui : le bazin est lavé, amidonné et repassé selon les techniques traditionnelles. Les broderies sont protégées et repassées à l\'envers.'],
                ['Comment se passe le paiement ?', 'Vous payez en espèces à la récupération ou à la livraison, ou à l\'avance par Orange Money ou MTN Mobile Money.'],
                ['Le prix final peut-il changer ?', 'Le montant affiché est une estimation. Il est confirmé lors de la collecte, après vérification des articles (taches particulières, tissus délicats).'],
                ['Et si j\'ai oublié quelque chose dans une poche ?', 'Nous vérifions toutes les poches avant le lavage et vous rendons les objets trouvés avec votre commande.'],
            ] as $i => [$question, $answer])
                <div>
                    <button type="button" @click="open = open === {{ $i }} ? null : {{ $i }}" class="flex w-full items-center justify-between gap-4 px-5 py-4 text-left font-medium text-ink">
                        {{ $question }}
                        <i class="fa-solid fa-chevron-down text-xs transition" :class="open === {{ $i }} && 'rotate-180'"></i>
                    </button>
                    <div x-show="open === {{ $i }}" x-collapse @if ($i > 0) x-cloak @endif>
                        <p class="px-5 pb-4 text-sm text-body">{{ $answer }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ============ CTA ============ --}}
    <section class="container-shop mt-16">
        <div class="flex flex-col items-center gap-5 rounded-xl bg-gradient-to-r from-brand to-brand-dark px-6 py-10 text-center text-white md:flex-row md:text-left">
            <i class="fa-solid fa-shirt text-5xl text-white/80"></i>
            <div class="flex-1">
                <h2 class="text-2xl font-medium text-white">Prêt à confier vos tenues ?</h2>
                <p class="text-white/80">Réservez maintenant, on s'occupe du reste.</p>
            </div>
            <a href="{{ route('pressing.create') }}" class="btn bg-white text-brand hover:bg-white/90"><i class="fa-solid fa-calendar-check"></i> Réserver un enlèvement</a>
        </div>
    </section>
@endsection
