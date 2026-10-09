@extends('layouts.app')

@section('title', 'Pressing '.$order->reference)

@section('content')
    <x-page-header :title="'Pressing '.$order->reference" :breadcrumbs="['Mon compte' => route('account.index'), $order->reference => null]" />

    <div class="container-shop">
        <div class="page-card mx-auto max-w-3xl">
            <div class="mb-6 flex flex-wrap items-center justify-between gap-3 text-sm">
                <span class="text-muted">Réservée le {{ $order->created_at->translatedFormat('d F Y à H\hi') }}</span>
                @include('partials.status-badge', ['status' => $order->status])
            </div>
            @include('partials.order-timeline', ['order' => $order])
            <div class="mt-8">@include('partials.pressing-summary', ['order' => $order])</div>
            <div class="mt-6 text-center">
                <a href="{{ route('account.index') }}" class="btn btn-sm btn-outline-dark"><i class="fa-solid fa-chevron-left text-xs"></i> Retour à mon compte</a>
            </div>
        </div>
    </div>
@endsection
