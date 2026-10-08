@extends('layouts.app')

@section('title', 'Suivi de commande')

@section('content')
    <x-page-header title="Suivi de commande" :breadcrumbs="['Suivi de commande' => null]" />

    <div class="container-shop">
        <div class="page-card">
            <h2 class="mb-6 text-center text-xl font-medium uppercase tracking-wide text-ink md:text-2xl">Suivi et traçabilité de votre commande</h2>
            <form method="GET" action="{{ route('tracking') }}" class="mx-auto flex max-w-xl overflow-hidden rounded-[.3125rem] border border-[#dae1e7] focus-within:border-azure">
                <span class="flex items-center bg-soft px-4 text-muted"><i class="fa-solid fa-bag-shopping"></i></span>
                <input name="reference" value="{{ $reference }}" required placeholder="Entrez le numéro de référence (ex : MG261008ABCD)" class="flex-1 px-4 py-3 text-[.9375rem] uppercase outline-none placeholder:normal-case">
                <button class="btn btn-primary rounded-none">Rechercher</button>
            </form>

            @if ($searched && ! $order)
                <div class="mx-auto mt-8 max-w-xl rounded-lg bg-danger/10 px-4 py-3 text-center text-sm text-danger">
                    Aucune commande trouvée pour la référence « {{ $reference }} ». Vérifiez la référence ou contactez-nous.
                </div>
            @endif

            @if ($order)
                <div class="mx-auto mt-10 max-w-3xl">
                    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <p class="text-sm text-muted">Commande</p>
                            <p class="font-mono text-lg font-medium text-ink">{{ $order->reference }}</p>
                        </div>
                        <div class="text-sm text-muted">Passée le {{ $order->created_at->translatedFormat('d F Y à H\hi') }}</div>
                    </div>

                    @include('partials.order-timeline', ['order' => $order])

                    <div class="mt-8">
                        @include('partials.order-summary', ['order' => $order])
                    </div>
                    <p class="mt-4 text-sm text-muted">Paiement : {{ $order->payment_method->getLabel() }} – <span class="font-medium">{{ $order->payment_status->getLabel() }}</span></p>
                </div>
            @endif
        </div>
    </div>
@endsection
