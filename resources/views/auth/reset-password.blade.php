@extends('layouts.app')

@section('title', 'Nouveau mot de passe')

@section('content')
    <div class="container-shop py-12">
        <div class="mx-auto max-w-lg rounded-lg bg-white p-6 shadow-lg md:p-8">
            <h1 class="mb-6 text-2xl">Choisissez un nouveau mot de passe</h1>
            <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <div>
                    <label class="form-label" for="email">Adresse email</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $email) }}" required class="form-control">
                    @error('email')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label" for="password">Nouveau mot de passe</label>
                    <input id="password" type="password" name="password" required class="form-control">
                    @error('password')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label" for="password_confirmation">Confirmez le mot de passe</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required class="form-control">
                </div>
                <button class="btn btn-primary w-full">Enregistrer le mot de passe</button>
            </form>
        </div>
    </div>
@endsection
