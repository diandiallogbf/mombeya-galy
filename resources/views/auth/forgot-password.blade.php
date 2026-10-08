@extends('layouts.app')

@section('title', 'Mot de passe oublié')

@section('content')
    <div class="container-shop py-12">
        <div class="mx-auto max-w-lg rounded-lg bg-white p-6 shadow-lg md:p-8">
            <h1 class="mb-2 text-2xl">Réinitialisation du mot de passe</h1>
            <p class="mb-6 text-sm text-muted">Saisissez votre adresse email : nous vous enverrons un lien pour choisir un nouveau mot de passe.</p>
            <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                @csrf
                <input type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="Adresse email" class="form-control">
                @error('email')<p class="form-error">{{ $message }}</p>@enderror
                <button class="btn btn-primary w-full">Envoyer le lien de réinitialisation</button>
            </form>
            <p class="mt-6 text-center text-sm">Vous vous souvenez du mot de passe ? <a href="{{ route('login') }}" class="text-brand hover:underline">Retour à la connexion</a></p>
        </div>
    </div>
@endsection
