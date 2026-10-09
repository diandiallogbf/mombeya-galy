@extends('layouts.app')

@section('title', 'Commande confirmée')

@section('content')
    <x-page-header title="Merci pour votre commande !" :breadcrumbs="['Commande '.$order->reference => null]" />

    <div class="container-shop">
        <div class="page-card mx-auto max-w-3xl">
            <div class="mb-8 text-center">
                <div class="mx-auto mb-4 flex size-16 items-center justify-center rounded-full bg-success/15 text-3xl text-success"><i class="fa-solid fa-check"></i></div>
                <h2 class="mb-2 text-2xl">Commande enregistrée</h2>
                <p class="text-muted">Votre numéro de référence :</p>
                <p class="mt-1 inline-block rounded-lg bg-soft px-4 py-2 font-mono text-xl font-medium tracking-wider text-ink">{{ $order->reference }}</p>
                <p class="mt-3 text-sm text-muted">Conservez-le pour <a href="{{ route('tracking', ['reference' => $order->reference]) }}" class="text-brand underline">suivre votre commande</a>.</p>
            </div>

            {{-- Payment instructions --}}
            <div class="mb-8 rounded-lg border border-azure/40 bg-azure/10 p-5 text-sm">
                <h3 class="mb-2 font-medium text-ocean"><i class="fa-solid fa-circle-info mr-1"></i> Paiement : {{ $order->payment_method->getLabel() }}</h3>
                <p class="text-body">{{ $order->payment_method->description() }}</p>
                @if ($order->payment_method->settingKey())
                    <p class="mt-2 text-body">Numéro marchand : <strong class="text-ink">{{ setting($order->payment_method->settingKey()) }}</strong> – Montant : <strong class="text-ink">{{ money($order->total, 'GNF') }}</strong> – Motif : <strong class="text-ink">{{ $order->reference }}</strong></p>
                @endif
            </div>

            <h3 class="mb-3 font-medium">Détail de la commande</h3>
            @include('partials.order-summary', ['order' => $order])

            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                <a href="{{ whatsapp_url('Bonjour Mombeya Galy, je viens de passer la commande '.$order->reference.' d\'un montant de '.money($order->total, 'GNF').'.') }}" target="_blank" rel="noopener"
                   class="btn flex-1 bg-[#25D366] text-white hover:bg-[#1da851]"><i class="fa-brands fa-whatsapp"></i> Confirmer sur WhatsApp</a>
                <a href="{{ route('shop.index') }}" class="btn btn-outline flex-1">Continuer mes achats</a>
            </div>

            @if (setting('pressing_offer_text'))
                <a href="{{ route('pressing.index') }}" class="mt-6 flex items-center gap-4 rounded-lg bg-gradient-to-r from-navy to-ocean p-5 text-sm text-white">
                    <i class="fa-solid fa-soap text-3xl text-azure"></i>
                    <span class="flex-1">{{ setting('pressing_offer_text') }}</span>
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </a>
            @endif
        </div>
    </div>
@endsection
