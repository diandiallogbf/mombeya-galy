@extends('layouts.app')

@section('title', 'Sur mesure')
@section('description', 'Faites confectionner votre tenue sur mesure par les tailleurs Mombeya Galy à Conakry : grands boubous, costumes africains, tenues de mariage.')

@section('content')
    <x-page-header title="L'art du sur mesure" :breadcrumbs="['Sur mesure' => null]" kicker="Confection Mombeya Galy"
                   subtitle="Une tenue pensée pour vous, coupée à vos mesures et brodée dans nos ateliers de Conakry. Prenez rendez-vous avec nos tailleurs."
                   :image="setting('atelier_image') ? \App\Support\Media::url(setting('atelier_image')) : asset('images/atelier.svg')" />

    {{-- Steps --}}
    <section class="container-shop mt-16 md:mt-24">
        <div class="grid gap-px bg-line md:grid-cols-4">
            @foreach ([
                ['01', 'Le rendez-vous', 'Échange avec un tailleur dans la maison de votre choix : style, occasion, budget.'],
                ['02', 'Les mesures', 'Prise de mesures complète et choix du tissu : bazin, super italien, wax, brocart.'],
                ['03', 'L\'essayage', 'Un premier essayage pour ajuster chaque détail de la coupe et de la broderie.'],
                ['04', 'La remise', 'Votre tenue est prête, repassée et livrée, ou à retirer en maison.'],
            ] as [$number, $title, $text])
                <div class="bg-ivory p-7 md:p-8">
                    <span class="font-display text-4xl text-muted/60">{{ $number }}</span>
                    <h2 class="mb-2 mt-4 font-display text-2xl">{{ $title }}</h2>
                    <p class="text-sm leading-relaxed text-body">{{ $text }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Booking form --}}
    <section id="rendez-vous" class="container-shop mt-20 grid gap-12 md:mt-28 lg:grid-cols-12">
        <div class="lg:col-span-4">
            <p class="kicker mb-3">Rendez-vous</p>
            <h2 class="section-title mb-5">Réservez votre séance</h2>
            <p class="mb-8 text-body">Choisissez un créneau et la maison qui vous convient : un conseiller vous appelle pour confirmer le rendez-vous.</p>
            <div class="space-y-3 text-sm">
                <p class="flex gap-3"><i class="fa-solid fa-ruler mt-1 text-ink"></i> <a href="{{ route('size-guide') }}" class="underline underline-offset-4 hover:text-brand">Comment prendre ses mesures</a></p>
                <p class="flex gap-3"><i class="fa-brands fa-whatsapp mt-1 text-ink"></i> <a href="{{ whatsapp_url('Bonjour Mombeya Galy, je souhaite un rendez-vous sur mesure.') }}" target="_blank" rel="noopener" class="underline underline-offset-4 hover:text-brand">Ou prenez rendez-vous sur WhatsApp</a></p>
            </div>
        </div>

        <form method="POST" action="{{ route('appointments.store') }}" enctype="multipart/form-data" class="panel grid gap-5 sm:grid-cols-2 lg:col-span-8">
            @csrf
            <div class="sm:col-span-2">
                <span class="form-label">Type de tenue *</span>
                <div class="flex flex-wrap gap-2">
                    @foreach ($types as $type)
                        <label class="size-pill has-[:checked]:border-ink has-[:checked]:bg-ink has-[:checked]:text-ivory">
                            <input type="radio" name="outfit_type" value="{{ $type }}" @checked(old('outfit_type', request('type')) === $type) class="sr-only" required>
                            {{ $type }}
                        </label>
                    @endforeach
                </div>
                @error('outfit_type')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div class="sm:col-span-2">
                <label class="form-label" for="occasion">Occasion <span class="font-normal normal-case tracking-normal text-muted">(optionnel)</span></label>
                <input id="occasion" name="occasion" value="{{ old('occasion') }}" class="form-control" placeholder="Mariage, baptême, Tabaski, cérémonie…">
            </div>
            <div>
                <label class="form-label" for="preferred_date">Date souhaitée *</label>
                <input id="preferred_date" type="date" name="preferred_date" value="{{ old('preferred_date', now()->addDays(2)->toDateString()) }}" min="{{ now()->toDateString() }}" required class="form-control">
                @error('preferred_date')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label" for="preferred_slot">Créneau *</label>
                <select id="preferred_slot" name="preferred_slot" required class="form-control">
                    @foreach ($slots as $slot)
                        <option value="{{ $slot }}" @selected(old('preferred_slot') === $slot)>{{ $slot }}</option>
                    @endforeach
                </select>
                @error('preferred_slot')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label" for="showroom_id">Maison *</label>
                <select id="showroom_id" name="showroom_id" required class="form-control">
                    <option value="">Choisissez une maison</option>
                    @foreach ($showrooms as $showroom)
                        <option value="{{ $showroom->id }}" @selected((int) old('showroom_id') === $showroom->id)>{{ $showroom->name }}</option>
                    @endforeach
                </select>
                @error('showroom_id')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label" for="budget">Budget <span class="font-normal normal-case tracking-normal text-muted">(optionnel)</span></label>
                <select id="budget" name="budget" class="form-control">
                    <option value="">Je ne sais pas encore</option>
                    @foreach ($budgets as $budget)
                        <option value="{{ $budget }}" @selected(old('budget') === $budget)>{{ $budget }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label" for="customer_name">Nom complet *</label>
                <input id="customer_name" name="customer_name" value="{{ old('customer_name', $user?->name) }}" required class="form-control">
                @error('customer_name')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label" for="customer_phone">Téléphone *</label>
                <input id="customer_phone" type="tel" name="customer_phone" value="{{ old('customer_phone', $user?->phone) }}" required class="form-control" placeholder="+224 6XX XX XX XX">
                @error('customer_phone')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div class="sm:col-span-2">
                <label class="form-label" for="customer_email">Email <span class="font-normal normal-case tracking-normal text-muted">(optionnel)</span></label>
                <input id="customer_email" type="email" name="customer_email" value="{{ old('customer_email', $user?->email) }}" class="form-control">
                @error('customer_email')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div class="sm:col-span-2">
                <label class="form-label" for="message">Votre projet <span class="font-normal normal-case tracking-normal text-muted">(optionnel)</span></label>
                <textarea id="message" name="message" rows="4" class="form-control" placeholder="Couleurs, broderies, nombre de tenues…">{{ old('message') }}</textarea>
            </div>
            <div class="sm:col-span-2">
                <label class="form-label" for="inspiration">Photo d'inspiration <span class="font-normal normal-case tracking-normal text-muted">(optionnel, 5 Mo max)</span></label>
                <input id="inspiration" type="file" name="inspiration" accept="image/*" class="block w-full text-sm text-body file:mr-4 file:border-0 file:bg-ink file:px-4 file:py-2.5 file:text-xs file:tracking-[.14em] file:text-ivory file:uppercase">
                @error('inspiration')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div class="sm:col-span-2">
                <button class="btn btn-primary w-full">Demander mon rendez-vous</button>
            </div>
        </form>
    </section>
@endsection
