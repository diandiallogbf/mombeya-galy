@extends('layouts.app')

@section('title', 'Mombeya Galy Pressing')
@section('description', 'Pressing Mombeya Galy à Conakry : lavage, nettoyage à sec, amidonnage du bazin et repassage. Collecte et livraison à domicile.')

@section('content')
    @php
        $formules = $servicesByCategory->pull('formule');
        $zoneNames = $zones->pluck('name')->join(', ', ' et ');
    @endphp

    {{-- ============ HERO ============ --}}
    <section class="border-b border-line">
        <div class="container-shop grid items-center gap-10 py-12 md:grid-cols-12 md:py-20">
            <div class="md:col-span-6">
                <nav class="mb-6 flex items-center gap-2 text-[.68rem] tracking-[.18em] text-muted uppercase">
                    <a href="{{ route('home') }}" class="hover:text-ink">Accueil</a><span class="text-line">/</span><span class="text-ink">Pressing</span>
                </nav>
                <p class="kicker mb-4">Mombeya Galy Pressing</p>
                <h1 class="display-title mb-6">Vos tenues entre de bonnes mains</h1>
                <p class="mb-9 max-w-lg text-[1.05rem] leading-relaxed text-body">
                    Lavage, nettoyage à sec, amidonnage du bazin et repassage.
                    @if ($zones->isNotEmpty())
                        Collecte et livraison à domicile à {{ $zoneNames }},
                    @endif
                    prêt en {{ setting('pressing_delay_text') }}.
                </p>
                <div class="flex flex-wrap items-center gap-6">
                    <a href="{{ route('pressing.create') }}" class="btn btn-dark">Réserver un enlèvement</a>
                    <a href="{{ whatsapp_url('Bonjour Mombeya Galy Pressing, je souhaite faire nettoyer des vêtements.') }}" target="_blank" rel="noopener" class="link-arrow"><i class="fa-brands fa-whatsapp"></i> WhatsApp</a>
                </div>
            </div>
            <div class="md:col-span-6">
                <div class="grid grid-cols-2 gap-px border border-line bg-line">
                    @foreach ([
                        ['fa-soap', 'Lavage & nettoyage à sec'],
                        ['fa-crown', 'Amidonnage du bazin'],
                        ['fa-shirt', 'Repassage soigné'],
                        ['fa-truck-fast', 'Collecte à domicile'],
                    ] as [$icon, $label])
                        <div class="flex aspect-square flex-col justify-between bg-white p-6 md:p-8">
                            <i class="fa-solid {{ $icon }} text-2xl text-ink"></i>
                            <p class="font-display text-xl leading-tight text-ink md:text-2xl">{{ $label }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ============ HOW IT WORKS ============ --}}
    <section class="container-shop mt-16 md:mt-24">
        <div class="grid gap-px bg-line md:grid-cols-4">
            @foreach ([
                ['01', 'Vous réservez', 'En ligne ou sur WhatsApp, en deux minutes.'],
                ['02', 'On collecte', 'Chez vous au créneau choisi, ou dépôt dans une maison.'],
                ['03', 'On nettoie', 'Lavage, nettoyage à sec, amidon et repassage dans notre atelier.'],
                ['04', 'On vous livre', 'Propre et repassé en '.setting('pressing_delay_text').', 24h en express.'],
            ] as [$number, $title, $text])
                <div class="bg-ivory p-7 md:p-8">
                    <span class="font-display text-4xl text-muted/60">{{ $number }}</span>
                    <h2 class="mb-2 mt-4 font-display text-2xl">{{ $title }}</h2>
                    <p class="text-sm leading-relaxed text-body">{{ $text }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ============ SERVICES & PRICES ============ --}}
    @if ($servicesByCategory->isNotEmpty())
        <section class="container-shop mt-20 md:mt-28" x-data="{ tab: @js($servicesByCategory->keys()->first()) }">
            <div class="mb-10 text-center">
                <p class="kicker mb-3">Tarifs</p>
                <h2 class="section-title">Nos services et tarifs</h2>
                <p class="mt-3 text-muted">Prix par pièce · Option express 24h : +{{ $expressPercent }} %</p>
            </div>

            <div class="mb-10 flex flex-wrap justify-center gap-2">
                @foreach ($servicesByCategory as $key => $group)
                    <button type="button" @click="tab = @js($key)" :class="tab === @js($key) && 'chip-active'" class="chip">{{ \App\Models\PressingService::CATEGORIES[$key] }}</button>
                @endforeach
            </div>

            @foreach ($servicesByCategory as $key => $group)
                <div x-show="tab === @js($key)" @if (! $loop->first) x-cloak @endif class="mx-auto max-w-4xl divide-y divide-line border-y border-line">
                    @foreach ($group as $service)
                        <div class="flex items-center gap-5 py-5">
                            <i class="{{ $service->icon ?: 'fa-solid fa-shirt' }} w-6 text-center text-lg text-ink"></i>
                            <div class="min-w-0 flex-1">
                                <h3 class="font-display text-xl text-ink">{{ $service->name }}</h3>
                                @if ($service->description)<p class="text-sm text-muted">{{ $service->description }}</p>@endif
                            </div>
                            <p class="whitespace-nowrap text-ink">{{ money($service->price) }} <span class="text-xs text-muted">/ {{ $service->unit }}</span></p>
                            <a href="{{ route('pressing.create', ['service' => $service->id]) }}" class="hidden text-[.7rem] tracking-[.16em] text-ink uppercase underline-offset-4 hover:text-brand hover:underline sm:inline" title="Réserver">Réserver</a>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </section>
    @endif

    {{-- ============ FORMULES ============ --}}
    @if ($formules && $formules->isNotEmpty())
        <section class="mt-20 bg-sand py-16 md:mt-28 md:py-20">
            <div class="container-shop">
                <div class="mb-10 text-center">
                    <p class="kicker mb-3">Abonnements</p>
                    <h2 class="section-title">Formules & abonnements</h2>
                </div>
                <div class="grid gap-6 md:grid-cols-3">
                    @foreach ($formules as $formule)
                        <div @class(['flex flex-col bg-white p-8 text-center', 'outline outline-1 outline-ink' => $loop->index === 1])>
                            @if ($loop->index === 1)
                                <span class="kicker mb-3 text-ink">Le plus choisi</span>
                            @endif
                            <h3 class="mb-2 font-display text-2xl">{{ $formule->name }}</h3>
                            <p class="mb-6 flex-1 text-sm text-body">{{ $formule->description }}</p>
                            <p class="mb-6 font-display text-3xl text-ink">{{ money($formule->price) }} <span class="font-sans text-sm text-muted">/ {{ $formule->unit }}</span></p>
                            <a href="{{ route('pressing.create', ['service' => $formule->id]) }}" class="btn btn-outline btn-sm">Choisir cette formule</a>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ============ ZONES & DROP POINTS ============ --}}
    <section class="container-shop mt-20 grid gap-10 md:mt-28 md:grid-cols-2">
        <div>
            <h2 class="mb-5 font-display text-3xl">Collecte à domicile</h2>
            @if ($zones->isEmpty())
                <p class="text-sm text-muted">La collecte à domicile arrive bientôt. En attendant, déposez vos vêtements dans une maison.</p>
            @else
                <ul class="divide-y divide-line border-y border-line text-sm">
                    @foreach ($zones as $zone)
                        <li class="flex justify-between gap-3 py-3">
                            <span class="text-ink">{{ $zone->name }} @if ($zone->note)<span class="text-muted">· {{ $zone->note }}</span>@endif</span>
                            <span class="text-ink">{{ $zone->fee ? money($zone->fee) : 'Gratuit' }}</span>
                        </li>
                    @endforeach
                </ul>
                <p class="mt-3 text-xs text-muted">Frais pour la collecte et la livraison retour. D'autres communes arrivent bientôt.</p>
            @endif
        </div>
        <div>
            <h2 class="mb-5 font-display text-3xl">Dépôt en maison <span class="text-muted">(gratuit)</span></h2>
            @if ($dropPoints->isEmpty())
                <p class="text-sm text-muted">Aucun point de dépôt pour le moment.</p>
            @else
                <ul class="divide-y divide-line border-y border-line text-sm">
                    @foreach ($dropPoints as $point)
                        <li class="flex flex-wrap justify-between gap-2 py-3">
                            <span class="text-ink">{{ $point->name }}</span>
                            <span class="text-muted">{{ $point->address }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </section>

    {{-- ============ FAQ ============ --}}
    <section class="container-shop mt-20 max-w-3xl md:mt-28" x-data="{ open: 0 }">
        <h2 class="section-title mb-8 text-center">Questions fréquentes</h2>
        <div class="divide-y divide-line border-y border-line">
            @foreach ([
                ['Quels sont les délais ?', 'Vos vêtements sont prêts en '.setting('pressing_delay_text').'. Avec l\'option express, comptez 24h (supplément de '.$expressPercent.' %).'],
                ['Prenez-vous soin du bazin et des broderies ?', 'Oui : le bazin est lavé, amidonné et repassé selon les techniques traditionnelles. Les broderies sont protégées et repassées à l\'envers.'],
                ['Comment se passe le paiement ?', 'Vous payez en espèces à la récupération ou à la livraison, ou à l\'avance par Orange Money ou MTN Mobile Money.'],
                ['Le prix final peut-il changer ?', 'Le montant affiché est une estimation. Il est confirmé lors de la collecte, après vérification des articles (taches particulières, tissus délicats).'],
                ['Et si j\'ai oublié quelque chose dans une poche ?', 'Nous vérifions toutes les poches avant le lavage et vous rendons les objets trouvés avec votre commande.'],
            ] as $i => [$question, $answer])
                <div>
                    <button type="button" @click="open = open === {{ $i }} ? null : {{ $i }}" class="flex w-full items-center justify-between gap-4 py-5 text-left font-display text-xl text-ink">
                        {{ $question }}
                        <i class="fa-solid fa-plus text-xs transition" :class="open === {{ $i }} && 'rotate-45'"></i>
                    </button>
                    <div x-show="open === {{ $i }}" x-collapse @if ($i > 0) x-cloak @endif>
                        <p class="pb-5 text-sm text-body">{{ $answer }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ============ CTA ============ --}}
    <section class="container-shop mt-20 md:mt-28">
        <div class="flex flex-col items-center gap-6 bg-night px-6 py-14 text-center text-ivory md:flex-row md:px-14 md:text-left">
            <div class="flex-1">
                <h2 class="font-display text-3xl text-ivory md:text-4xl">Prêt à confier vos tenues ?</h2>
                <p class="mt-2 text-ivory/70">Réservez maintenant, on s'occupe du reste.</p>
            </div>
            <a href="{{ route('pressing.create') }}" class="btn btn-light">Réserver un enlèvement</a>
        </div>
    </section>
@endsection
