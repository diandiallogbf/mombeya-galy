@extends('layouts.app')

@section('title', 'Connexion')

@section('content')
    <div class="container-shop py-10 lg:py-16">
        <div class="grid gap-10 lg:grid-cols-2">
            {{-- Login --}}
            <div class="rounded-lg bg-white p-6 shadow-lg md:p-8">
                <h1 class="mb-1 text-2xl">Se connecter</h1>
                <p class="mb-5 text-sm text-muted">Accédez à vos commandes et à votre compte client.</p>
                <hr class="mb-6 border-line">
                <form method="POST" action="{{ route('login.attempt') }}" class="space-y-4" x-data="{ show: false }">
                    @csrf
                    <div class="relative">
                        <i class="fa-regular fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-muted"></i>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="Adresse email" class="form-control pl-11">
                    </div>
                    @error('email')<p class="form-error">{{ $message }}</p>@enderror
                    <div class="relative">
                        <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-muted"></i>
                        <input :type="show ? 'text' : 'password'" type="password" name="password" required placeholder="Mot de passe" class="form-control pl-11 pr-11">
                        <button type="button" @click="show = !show" class="absolute right-4 top-1/2 -translate-y-1/2 text-muted hover:text-ink" aria-label="Afficher le mot de passe"><i class="fa-regular" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i></button>
                    </div>
                    <div class="flex flex-wrap items-center justify-between gap-2 text-sm">
                        <label class="flex items-center gap-2"><input type="checkbox" name="remember" checked class="accent-[#e0144f]"> Se souvenir de moi</label>
                        <a href="{{ route('password.request') }}" class="text-brand hover:underline">Mot de passe oublié ?</a>
                    </div>
                    <hr class="border-line">
                    <div class="text-right">
                        <button class="btn btn-primary btn-shadow"><i class="fa-solid fa-right-to-bracket"></i> Connexion</button>
                    </div>
                </form>
            </div>

            {{-- Register --}}
            <div class="pt-2 lg:pt-6">
                <h2 class="mb-2 text-2xl">Pas de compte ? Créez un compte</h2>
                <p class="mb-6 text-sm text-muted">L'inscription prend moins d'une minute et vous donne un contrôle total sur vos commandes.</p>
                <form method="POST" action="{{ route('register') }}" x-data="{ terms: {{ old('terms') ? 'true' : 'false' }} }">
                    @csrf
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="form-label" for="reg-name">Prénom et nom</label>
                            <input id="reg-name" name="name" value="{{ old('name') }}" required placeholder="Nom complet" class="form-control">
                            @error('name', 'register')<p class="form-error">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="form-label" for="reg-email">Adresse email</label>
                            <input id="reg-email" type="email" name="email" value="{{ old('email') }}" required placeholder="vous@exemple.com" class="form-control">
                            @error('email', 'register')<p class="form-error">{{ $message }}</p>@enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label class="form-label" for="reg-phone">Téléphone <span class="font-normal text-muted">(optionnel)</span></label>
                            <input id="reg-phone" type="tel" name="phone" value="{{ old('phone') }}" placeholder="+224 6XX XX XX XX" class="form-control">
                        </div>
                        <div>
                            <label class="form-label" for="reg-password">Mot de passe</label>
                            <input id="reg-password" type="password" name="password" required placeholder="8 caractères minimum" class="form-control">
                            @error('password', 'register')<p class="form-error">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="form-label" for="reg-password2">Confirmez le mot de passe</label>
                            <input id="reg-password2" type="password" name="password_confirmation" required placeholder="Confirmez le mot de passe" class="form-control">
                        </div>
                    </div>
                    <label class="mt-5 flex items-start gap-2 text-sm">
                        <input type="checkbox" name="terms" value="1" x-model="terms" class="mt-1 accent-[#e0144f]">
                        <span>J'accepte les termes et conditions de la politique de confidentialité.</span>
                    </label>
                    @error('terms', 'register')<p class="form-error">{{ $message }}</p>@enderror
                    <div class="mt-6 text-right">
                        <button class="btn btn-primary btn-shadow" :disabled="!terms"><i class="fa-solid fa-user-plus"></i> S'inscrire</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
