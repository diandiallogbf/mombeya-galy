@extends('layouts.app')

@section('title', 'Guide des tailles')

@section('content')
    <x-page-header title="Guide des tailles" :breadcrumbs="['Guide des tailles' => null]" kicker="Bien choisir"
                   subtitle="Nos tenues sont coupées pour une silhouette ample et élégante. Comparez vos mesures avec nos tableaux pour trouver la taille idéale." />

    <div class="container-shop mt-14 grid gap-12 lg:grid-cols-12">
        <aside class="lg:col-span-4">
            <div class="panel lg:sticky lg:top-32">
                <h2 class="mb-5 font-display text-2xl">Prendre vos mesures</h2>
                <ol class="space-y-4 text-sm">
                    @foreach ([
                        ['Poitrine', 'Mesurez horizontalement à l\'endroit le plus fort de la poitrine, sous les bras.'],
                        ['Taille', 'Mesurez au creux de la taille, au-dessus du nombril, sans serrer.'],
                        ['Hanches', 'Mesurez à l\'endroit le plus large des hanches, pieds joints.'],
                        ['Stature', 'Votre taille debout, pieds nus, du sommet du crâne au sol.'],
                        ['Pied', 'Posez le pied sur une feuille, tracez le talon et l\'orteil le plus long, mesurez.'],
                    ] as $i => [$label, $text])
                        <li class="flex gap-4">
                            <span class="font-display text-2xl leading-none text-muted">{{ $i + 1 }}</span>
                            <span><strong class="block font-medium text-ink">{{ $label }}</strong>{{ $text }}</span>
                        </li>
                    @endforeach
                </ol>
                <div class="mt-8 border-t border-line pt-6 text-sm">
                    <p class="mb-4 text-body">Entre deux tailles ? Prenez la plus grande, ou faites confectionner votre tenue à vos mesures exactes.</p>
                    <a href="{{ route('appointments.create') }}" class="btn btn-dark btn-sm w-full">Prendre rendez-vous sur mesure</a>
                </div>
            </div>
        </aside>
        <div class="lg:col-span-8">
            @include('partials.size-tables')
        </div>
    </div>
@endsection
