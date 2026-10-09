@extends('layouts.app')

@section('title', 'Réservation confirmée')

@section('content')
    <x-page-header title="Merci pour votre réservation !" :breadcrumbs="['Pressing' => route('pressing.index'), 'Réservation '.$order->reference => null]" />

    <div class="container-shop">
        <div class="page-card mx-auto max-w-3xl">
            <div class="mb-8 text-center">
                <div class="mx-auto mb-4 flex size-16 items-center justify-center rounded-full bg-success/15 text-3xl text-success"><i class="fa-solid fa-check"></i></div>
                <h2 class="mb-2 text-2xl">Réservation enregistrée</h2>
                <p class="text-muted">Votre numéro de référence :</p>
                <p class="mt-1 inline-block rounded-lg bg-soft px-4 py-2 font-mono text-xl font-medium tracking-wider text-ink">{{ $order->reference }}</p>
                <p class="mt-3 text-sm text-muted">
                    {{ $order->mode === 'collecte' ? 'Un conseiller vous appelle pour confirmer la collecte.' : 'Présentez cette référence lors du dépôt au showroom.' }}
                    <a href="{{ route('tracking', ['reference' => $order->reference]) }}" class="text-brand underline">Suivre ma réservation</a>
                </p>
            </div>

            @if ($order->payment_method->settingKey())
                <div class="mb-8 rounded-lg border border-azure/40 bg-azure/10 p-5 text-sm">
                    <h3 class="mb-2 font-medium text-ocean"><i class="fa-solid fa-circle-info mr-1"></i> Paiement : {{ $order->payment_method->getLabel() }}</h3>
                    <p class="text-body">Numéro marchand : <strong class="text-ink">{{ setting($order->payment_method->settingKey()) }}</strong> – Montant estimé : <strong class="text-ink">{{ money($order->total, 'GNF') }}</strong> – Motif : <strong class="text-ink">{{ $order->reference }}</strong></p>
                    <p class="mt-1 text-muted">Vous pouvez aussi payer après confirmation du montant à la collecte.</p>
                </div>
            @endif

            <h3 class="mb-3 font-medium">Détail de la réservation</h3>
            @include('partials.pressing-summary', ['order' => $order])

            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                <a href="{{ whatsapp_url('Bonjour Mombeya Galy Pressing, je viens de réserver ('.$order->reference.', '.money($order->total, 'GNF').').') }}" target="_blank" rel="noopener"
                   class="btn flex-1 bg-[#25D366] text-white hover:bg-[#1da851]"><i class="fa-brands fa-whatsapp"></i> Confirmer sur WhatsApp</a>
                <a href="{{ route('shop.index') }}" class="btn btn-outline flex-1">Découvrir la boutique</a>
            </div>
        </div>
    </div>
@endsection
