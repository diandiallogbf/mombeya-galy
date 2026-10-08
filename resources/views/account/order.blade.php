@extends('layouts.app')

@section('title', 'Commande '.$order->reference)

@section('content')
    <x-page-header :title="'Commande '.$order->reference" :breadcrumbs="['Mon compte' => route('account.index'), $order->reference => null]" />

    <div class="container-shop">
        <div class="page-card mx-auto max-w-3xl">
            <div class="mb-6 flex flex-wrap items-center justify-between gap-3 text-sm">
                <span class="text-muted">Passée le {{ $order->created_at->translatedFormat('d F Y à H\hi') }}</span>
                @include('partials.status-badge', ['status' => $order->status])
            </div>
            @include('partials.order-timeline', ['order' => $order])
            <div class="mt-8">@include('partials.order-summary', ['order' => $order])</div>
            <div class="mt-6 grid gap-4 text-sm sm:grid-cols-2">
                <div class="rounded-lg bg-soft p-4">
                    <p class="mb-1 font-medium text-ink">Livraison</p>
                    <p>{{ $order->customer_name }} – {{ $order->customer_phone }}</p>
                    <p class="text-muted">{{ $order->delivery_zone_name }}<br>{{ $order->address }}</p>
                </div>
                <div class="rounded-lg bg-soft p-4">
                    <p class="mb-1 font-medium text-ink">Paiement</p>
                    <p>{{ $order->payment_method->getLabel() }}</p>
                    <p class="text-muted">{{ $order->payment_status->getLabel() }}</p>
                </div>
            </div>
            <div class="mt-6 text-center">
                <a href="{{ route('account.index') }}" class="btn btn-sm btn-outline-dark"><i class="fa-solid fa-chevron-left text-xs"></i> Retour à mes commandes</a>
            </div>
        </div>
    </div>
@endsection
