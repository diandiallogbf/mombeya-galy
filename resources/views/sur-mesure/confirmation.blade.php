@extends('layouts.app')

@section('title', 'Rendez-vous demandé')

@section('content')
    <x-page-header title="Merci, à très bientôt" :breadcrumbs="['Sur mesure' => route('appointments.create'), 'Confirmation' => null]" kicker="Rendez-vous sur mesure" />

    <div class="container-shop mt-14">
        <div class="page-card mx-auto max-w-2xl text-center">
            <i class="fa-solid fa-scissors mb-6 text-4xl text-ink"></i>
            <p class="mb-2 text-body">Votre demande a bien été enregistrée sous la référence</p>
            <p class="mb-6 inline-block border border-line px-5 py-2 font-mono text-xl tracking-wider text-ink">{{ $appointment->reference }}</p>
            <dl class="mx-auto mb-8 grid max-w-md gap-3 text-left text-sm">
                <div class="flex justify-between border-b border-line pb-2"><dt class="text-muted">Tenue</dt><dd class="text-ink">{{ $appointment->outfit_type }}</dd></div>
                <div class="flex justify-between border-b border-line pb-2"><dt class="text-muted">Date souhaitée</dt><dd class="text-ink">{{ $appointment->preferred_date->translatedFormat('l d F Y') }}</dd></div>
                <div class="flex justify-between border-b border-line pb-2"><dt class="text-muted">Créneau</dt><dd class="text-ink">{{ $appointment->preferred_slot }}</dd></div>
                <div class="flex justify-between border-b border-line pb-2"><dt class="text-muted">Maison</dt><dd class="text-ink">{{ $appointment->showroom_name }}</dd></div>
            </dl>
            <p class="mb-8 text-sm text-body">Un conseiller vous appelle au <strong class="text-ink">{{ $appointment->customer_phone }}</strong> pour confirmer le rendez-vous.</p>
            <div class="flex flex-col justify-center gap-3 sm:flex-row">
                <a href="{{ whatsapp_url('Bonjour Mombeya Galy, je viens de demander un rendez-vous sur mesure ('.$appointment->reference.').') }}" target="_blank" rel="noopener" class="btn bg-[#25D366] text-white hover:bg-[#1da851]"><i class="fa-brands fa-whatsapp"></i> Confirmer sur WhatsApp</a>
                <a href="{{ route('shop.index') }}" class="btn btn-outline">Découvrir la boutique</a>
            </div>
        </div>
    </div>
@endsection
