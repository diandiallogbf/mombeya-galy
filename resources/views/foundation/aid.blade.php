@extends('layouts.app')

@section('title', 'Demande d\'aide')

@section('content')
    <x-page-header title="Demande d’aide" :breadcrumbs="['Fondation' => route('foundation.index'), 'Demande d’aide' => null]" />

    <div class="container-shop">
        <div class="page-card mx-auto max-w-2xl">
            <div class="mb-6 rounded-lg border border-azure/40 bg-azure/10 p-4 text-center text-sm text-ocean">
                Inscrivez-vous pour faire une demande d'aide auprès de la Fondation Mombeya Galy.
            </div>
            <form method="POST" action="{{ route('foundation.aid.store') }}" class="space-y-4" x-data="{ method: @js(old('transfer_method', 'Orange Money')) }">
                @csrf
                <div>
                    <label class="form-label" for="full_name">Prénom et nom *</label>
                    <input id="full_name" name="full_name" value="{{ old('full_name') }}" required class="form-control">
                    @error('full_name')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label" for="phone">Numéro de téléphone *</label>
                    <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" required class="form-control" placeholder="+224 6XX XX XX XX">
                    @error('phone')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <span class="form-label">Moyen de transfert *</span>
                    <div class="flex flex-wrap gap-3">
                        @foreach (['Orange Money', 'MTN Mobile Money'] as $option)
                            <label class="flex cursor-pointer items-center gap-2 rounded-full border px-4 py-2 text-sm transition" :class="method === @js($option) ? 'border-brand bg-brand-light text-brand' : 'border-line'">
                                <input type="radio" name="transfer_method" value="{{ $option }}" x-model="method" class="accent-[#e0144f]"> {{ $option }}
                            </label>
                        @endforeach
                    </div>
                    @error('transfer_method')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label" for="email">Email <span class="font-normal text-muted">(optionnel)</span></label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" class="form-control">
                    @error('email')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label" for="message">Message <span class="font-normal text-muted">(optionnel)</span></label>
                    <textarea id="message" name="message" rows="5" class="form-control" placeholder="Expliquez brièvement votre situation…">{{ old('message') }}</textarea>
                    @error('message')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <button class="btn btn-primary w-full">Envoyer</button>
            </form>
        </div>
    </div>
@endsection
