<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="cart-count" content="{{ $cartCount }}">
    <meta name="cart-total" content="{{ $cartTotal }}">
    <meta name="wishlist-count" content="{{ $wishlistCount }}">
    <title>@hasSection('title')@yield('title') | @endif{{ setting('shop_name') }} – Maison de mode africaine à Conakry</title>
    <meta name="description" content="@yield('description', setting('shop_name').' : grands boubous, bazin, tenues traditionnelles, sur mesure et pressing. Maisons à Conakry, livraison partout dans le monde.')">
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <meta property="og:title" content="@yield('title', setting('shop_name'))">
    <meta property="og:image" content="@yield('og_image', asset('images/logo-rond.jpg'))">
    <script>document.documentElement.classList.add('js')</script>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-screen" x-data>
    @php
        $nav = [
            ['Nouveautés', route('shop.news'), request()->routeIs('shop.news')],
            ['Boutique', route('shop.index'), request()->routeIs('shop.index', 'shop.category', 'product.show'), 'mega'],
            ['Prestige', route('shop.prestige'), request()->routeIs('shop.prestige')],
            ['Sur mesure', route('appointments.create'), request()->routeIs('appointments.*')],
            ['Pressing', route('pressing.index'), request()->routeIs('pressing.*')],
            ['Nos maisons', route('showrooms'), request()->routeIs('showrooms')],
        ];
        $accountUrl = auth()->check() ? route('account.index') : route('login');
        $featuredCategory = $menuCategories->firstWhere('slug', 'homme') ?? $menuCategories->first();
    @endphp

    {{-- ============ ANNOUNCEMENT BAR ============ --}}
    @if ($announcements)
        <div class="overflow-hidden bg-ink py-2.5 text-[.72rem] tracking-[.16em] text-ivory/85 uppercase">
            <div class="animate-marquee flex w-max gap-16 whitespace-nowrap">
                @foreach (array_merge($announcements, $announcements) as $message)
                    <span class="flex items-center gap-16">{{ $message }} <span class="text-brand">✦</span></span>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ============ HEADER ============ --}}
    <header class="sticky top-0 z-40 border-b border-line bg-ivory/95 backdrop-blur"
            x-data="{ menu: false, mega: false, search: false, scrolled: false }"
            @scroll.window.throttle.50ms="scrolled = window.scrollY > 40"
            @keydown.escape.window="menu = false; search = false; mega = false">
        <div class="container-shop">
            <div class="grid grid-cols-[1fr_auto_1fr] items-center transition-all duration-300" :class="scrolled ? 'h-16' : 'h-20 lg:h-24'">
                {{-- Left --}}
                <div class="flex items-center gap-1">
                    <button type="button" class="-ml-2 flex size-11 items-center justify-center text-xl text-ink lg:hidden" @click="menu = true" aria-label="Menu">
                        <i class="fa-solid fa-bars-staggered"></i>
                    </button>
                    <button type="button" class="hidden size-11 items-center justify-center text-ink transition hover:text-brand lg:flex" @click="search = true" aria-label="Rechercher">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                    <a href="{{ route('tracking') }}" class="hidden text-[.7rem] tracking-[.16em] text-body uppercase transition hover:text-ink xl:inline">Suivi de commande</a>
                </div>

                {{-- Logo --}}
                <a href="{{ route('home') }}" class="justify-self-center" aria-label="{{ setting('shop_name') }} – accueil">
                    <img src="{{ asset('images/logo.png') }}" alt="{{ setting('shop_name') }}" class="w-auto transition-all duration-300" :class="scrolled ? 'h-10' : 'h-12 lg:h-16'">
                </a>

                {{-- Right --}}
                <div class="flex items-center justify-end gap-0.5 sm:gap-1">
                    <div class="relative hidden md:block" x-data="{ open: false }" @click.outside="open = false">
                        <button type="button" @click="open = !open" class="flex h-11 items-center gap-1 px-2 text-[.72rem] tracking-[.14em] text-body uppercase hover:text-ink">
                            {{ $currentCurrency }} <i class="fa-solid fa-chevron-down text-[8px]"></i>
                        </button>
                        <div x-cloak x-show="open" x-transition class="absolute right-0 top-11 z-50 w-32 border border-line bg-white py-2 text-sm">
                            @foreach (config('shop.currencies') as $code => $currency)
                                <a href="{{ route('currency', $code) }}" @class(['block px-4 py-1.5 hover:bg-ivory', 'font-medium text-brand' => $code === $currentCurrency])>{{ $currency['label'] }}</a>
                            @endforeach
                        </div>
                    </div>
                    <a href="{{ $accountUrl }}" class="hidden size-11 items-center justify-center text-ink transition hover:text-brand sm:flex" title="Mon compte"><i class="fa-regular fa-user"></i></a>
                    <a href="{{ route('wishlist.index') }}" class="relative flex size-11 items-center justify-center text-ink transition hover:text-brand" title="Mes favoris">
                        <i class="fa-regular fa-heart"></i>
                        <span x-cloak x-show="$store.wishlist.count > 0" x-text="$store.wishlist.count" class="absolute right-1 top-1.5 flex size-4 items-center justify-center rounded-full bg-brand text-[.6rem] text-white">{{ $wishlistCount }}</span>
                    </a>
                    <div class="relative" x-data="{ open: false, html: '', async load() { this.html = await (await fetch('{{ route('cart.index', ['mini' => 1]) }}')).text(); } }"
                         @mouseenter="if (window.innerWidth >= 1024) { open = true; load(); }" @mouseleave="open = false" @cart-updated.window="html = ''">
                        <a href="{{ route('cart.index') }}" class="relative flex size-11 items-center justify-center text-ink transition hover:text-brand" title="Mon panier">
                            <i class="fa-solid fa-bag-shopping"></i>
                            <span x-text="$store.cart.count" class="absolute right-1 top-1.5 flex size-4 items-center justify-center rounded-full bg-ink text-[.6rem] text-ivory">{{ $cartCount }}</span>
                        </a>
                        <div x-cloak x-show="open" x-transition.opacity class="absolute right-0 top-full z-50 w-80 pt-2">
                            <div class="border border-line bg-white p-5 shadow-card">
                                <div x-show="!html" class="py-6 text-center text-sm text-muted"><i class="fa-solid fa-spinner fa-spin"></i></div>
                                <div x-html="html"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Desktop navigation --}}
            <nav class="hidden justify-center gap-9 transition-all lg:flex" :class="scrolled ? 'pb-3' : 'pb-4'" @mouseleave="mega = false">
                @foreach ($nav as $item)
                    <a href="{{ $item[1] }}" @if (($item[3] ?? null) === 'mega') @mouseenter="mega = true" @else @mouseenter="mega = false" @endif
                       @class(['relative py-1 text-[.74rem] font-medium tracking-[.2em] uppercase transition hover:text-brand', 'text-brand' => $item[2], 'text-ink' => ! $item[2]])>
                        {{ $item[0] }}
                        @if ($item[2])<span class="absolute inset-x-0 -bottom-1 mx-auto h-px w-6 bg-brand"></span>@endif
                    </a>
                @endforeach
            </nav>
        </div>

        {{-- Mega menu --}}
        <div x-cloak x-show="mega" x-transition.opacity @mouseenter="mega = true" @mouseleave="mega = false" class="absolute inset-x-0 top-full hidden border-b border-line bg-white lg:block">
            <div class="container-shop grid grid-cols-12 gap-10 py-10">
                <div class="col-span-5">
                    <p class="kicker mb-4">Nos univers</p>
                    <div class="grid grid-cols-2 gap-x-8 gap-y-2.5">
                        @foreach ($menuCategories as $menuCategory)
                            <a href="{{ route('shop.category', $menuCategory) }}" class="font-display text-lg text-ink transition hover:text-brand">{{ $menuCategory->name }}</a>
                        @endforeach
                    </div>
                </div>
                <div class="col-span-3 border-l border-line pl-10">
                    <p class="kicker mb-4">Sélections</p>
                    <div class="flex flex-col gap-2.5 text-sm">
                        <a href="{{ route('shop.news') }}" class="text-ink hover:text-brand">Nouvelle collection</a>
                        <a href="{{ route('shop.prestige') }}" class="text-ink hover:text-brand">Collection Prestige</a>
                        <a href="{{ route('shop.promotions') }}" class="text-ink hover:text-brand">Offres du moment</a>
                        <a href="{{ route('shop.index') }}" class="text-ink hover:text-brand">Toute la boutique</a>
                        <a href="{{ route('size-guide') }}" class="mt-3 text-muted hover:text-brand"><i class="fa-solid fa-ruler mr-1"></i> Guide des tailles</a>
                    </div>
                </div>
                @if ($featuredCategory)
                    <a href="{{ route('shop.category', $featuredCategory) }}" class="group col-span-4 relative block overflow-hidden">
                        <img src="{{ $featuredCategory->imageUrl() }}" alt="" class="h-56 w-full object-cover transition duration-700 group-hover:scale-105">
                        <span class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></span>
                        <span class="absolute bottom-5 left-5 text-ivory">
                            <span class="block font-display text-2xl">{{ $featuredCategory->name }}</span>
                            <span class="text-[.7rem] tracking-[.2em] uppercase">Découvrir →</span>
                        </span>
                    </a>
                @endif
            </div>
        </div>

        {{-- Search overlay --}}
        <div x-cloak x-show="search" x-transition.opacity class="fixed inset-0 z-[60] bg-ivory/98" @click.self="search = false">
            <div class="container-shop pt-24">
                <button type="button" @click="search = false" class="absolute right-6 top-6 text-2xl text-ink" aria-label="Fermer"><i class="fa-solid fa-xmark"></i></button>
                <p class="kicker mb-4 text-center">Que recherchez-vous ?</p>
                <form action="{{ route('shop.index') }}" class="mx-auto flex max-w-2xl items-center border-b-2 border-ink">
                    <input type="search" name="q" placeholder="Boubou, bazin, sandales…" x-ref="q" x-effect="search && $nextTick(() => $refs.q.focus())" class="flex-1 bg-transparent py-4 font-display text-3xl text-ink outline-none placeholder:text-muted/60">
                    <button class="text-xl text-ink" aria-label="Rechercher"><i class="fa-solid fa-arrow-right"></i></button>
                </form>
            </div>
        </div>

        {{-- Mobile menu --}}
        <div x-cloak x-show="menu" class="fixed inset-0 z-[60] lg:hidden">
            <div x-show="menu" x-transition.opacity class="absolute inset-0 bg-black/40" @click="menu = false"></div>
            <aside x-show="menu" x-transition:enter="transition duration-300" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
                   x-transition:leave="transition duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
                   class="absolute inset-y-0 left-0 flex w-[22rem] max-w-[88vw] flex-col overflow-y-auto bg-ivory">
                <div class="flex items-center justify-between border-b border-line px-5 py-4">
                    <img src="{{ asset('images/logo.png') }}" alt="{{ setting('shop_name') }}" class="h-10 w-auto">
                    <button type="button" class="text-xl text-ink" @click="menu = false" aria-label="Fermer"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <form action="{{ route('shop.index') }}" class="border-b border-line px-5 py-4">
                    <div class="flex items-center border-b border-ink">
                        <input type="search" name="q" placeholder="Rechercher…" class="flex-1 bg-transparent py-2 outline-none">
                        <i class="fa-solid fa-magnifying-glass text-ink"></i>
                    </div>
                </form>
                <nav class="flex flex-col px-5 py-4">
                    @foreach ($nav as $item)
                        <a href="{{ $item[1] }}" class="border-b border-line py-3 font-display text-2xl text-ink">{{ $item[0] }}</a>
                    @endforeach
                </nav>
                <div class="px-5 pb-6">
                    <p class="kicker mb-3">Nos univers</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($menuCategories as $menuCategory)
                            <a href="{{ route('shop.category', $menuCategory) }}" class="chip text-xs">{{ $menuCategory->name }}</a>
                        @endforeach
                    </div>
                </div>
                <div class="mt-auto grid grid-cols-2 border-t border-line text-center text-xs tracking-[.14em] uppercase">
                    <a href="{{ $accountUrl }}" class="border-r border-line py-4 text-ink"><i class="fa-regular fa-user mr-1"></i> Compte</a>
                    <a href="{{ route('tracking') }}" class="py-4 text-ink"><i class="fa-solid fa-location-dot mr-1"></i> Suivi</a>
                </div>
                <div class="flex justify-center gap-3 border-t border-line py-4">
                    @foreach (config('shop.currencies') as $code => $currency)
                        <a href="{{ route('currency', $code) }}" @class(['text-xs tracking-[.14em]', 'font-medium text-brand' => $code === $currentCurrency, 'text-muted' => $code !== $currentCurrency])>{{ $code }}</a>
                    @endforeach
                </div>
            </aside>
        </div>
    </header>

    {{-- Flash messages --}}
    @if (session('success') || session('error'))
        <div class="container-shop mt-5" x-data="{ show: true }" x-show="show">
            <div @class([
                'flex items-start gap-3 border px-4 py-3 text-sm',
                'border-success/30 bg-success/10 text-[#1d6b49]' => session('success'),
                'border-danger/30 bg-danger/10 text-danger' => session('error'),
            ])>
                <i @class(['fa-solid mt-0.5', 'fa-circle-check' => session('success'), 'fa-circle-exclamation' => session('error')])></i>
                <span class="flex-1">{{ session('success') ?? session('error') }}</span>
                <button type="button" @click="show = false" class="opacity-60 hover:opacity-100" aria-label="Fermer"><i class="fa-solid fa-xmark"></i></button>
            </div>
        </div>
    @endif

    <main>
        @yield('content')
    </main>

    {{-- ============ FOOTER ============ --}}
    <footer class="mt-24 border-t border-line bg-white">
        <div class="container-shop grid gap-10 py-16 sm:grid-cols-2 lg:grid-cols-12">
            <div class="lg:col-span-4">
                <img src="{{ asset('images/logo.png') }}" alt="{{ setting('shop_name') }}" class="mb-5 h-16 w-auto">
                <p class="mb-6 max-w-xs font-display text-xl leading-snug text-ink">{{ setting('slogan') }}</p>
                <div class="flex gap-2">
                    @foreach (['facebook_url' => 'fa-facebook-f', 'instagram_url' => 'fa-instagram', 'tiktok_url' => 'fa-tiktok', 'youtube_url' => 'fa-youtube', 'twitter_url' => 'fa-x-twitter'] as $key => $icon)
                        @if (setting($key))
                            <a href="{{ setting($key) }}" target="_blank" rel="noopener" class="flex size-10 items-center justify-center rounded-full border border-line text-ink transition hover:border-ink hover:bg-ink hover:text-ivory" aria-label="{{ ucfirst(str_replace('_url', '', $key)) }}">
                                <i class="fa-brands {{ $icon }}"></i>
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
            <div class="lg:col-span-2">
                <p class="kicker mb-4">Boutique</p>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="{{ route('shop.news') }}" class="hover:text-brand">Nouvelle collection</a></li>
                    @foreach ($menuCategories->take(5) as $menuCategory)
                        <li><a href="{{ route('shop.category', $menuCategory) }}" class="hover:text-brand">{{ $menuCategory->name }}</a></li>
                    @endforeach
                    <li><a href="{{ route('shop.promotions') }}" class="hover:text-brand">Offres du moment</a></li>
                </ul>
            </div>
            <div class="lg:col-span-2">
                <p class="kicker mb-4">Services</p>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="{{ route('appointments.create') }}" class="hover:text-brand">Sur mesure</a></li>
                    <li><a href="{{ route('pressing.index') }}" class="hover:text-brand">Pressing</a></li>
                    <li><a href="{{ route('size-guide') }}" class="hover:text-brand">Guide des tailles</a></li>
                    <li><a href="{{ route('tracking') }}" class="hover:text-brand">Suivi de commande</a></li>
                    <li><a href="{{ route('showrooms') }}" class="hover:text-brand">Nos maisons</a></li>
                    <li><a href="{{ route('videos') }}" class="hover:text-brand">Vidéos</a></li>
                </ul>
            </div>
            <div class="lg:col-span-2">
                <p class="kicker mb-4">Mon espace</p>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="{{ $accountUrl }}" class="hover:text-brand">{{ auth()->check() ? 'Mon compte' : 'Se connecter' }}</a></li>
                    <li><a href="{{ route('wishlist.index') }}" class="hover:text-brand">Mes favoris</a></li>
                    <li><a href="{{ route('cart.index') }}" class="hover:text-brand">Mon panier</a></li>
                </ul>
            </div>
            <div class="lg:col-span-2">
                <p class="kicker mb-4">Contact</p>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="tel:{{ preg_replace('/\s+/', '', (string) setting('phone')) }}" class="hover:text-brand">{{ setting('phone') }}</a></li>
                    <li><a href="{{ whatsapp_url('Bonjour Mombeya Galy !') }}" target="_blank" rel="noopener" class="hover:text-brand">WhatsApp</a></li>
                    @if (setting('email'))<li><a href="mailto:{{ setting('email') }}" class="break-all hover:text-brand">{{ setting('email') }}</a></li>@endif
                    <li class="text-muted">{{ setting('address') }}</li>
                </ul>
            </div>
        </div>
        <div class="border-t border-line">
            <div class="container-shop flex flex-col items-center justify-between gap-4 py-6 text-xs text-muted md:flex-row">
                <p>© {{ date('Y') }} {{ setting('shop_name') }}. Tous droits réservés.</p>
                <div class="flex flex-wrap items-center justify-center gap-2 tracking-[.12em] uppercase">
                    @foreach (['Orange Money', 'MTN MoMo', 'Visa', 'Mastercard', 'Western Union'] as $method)
                        <span class="border border-line px-2.5 py-1 text-[.62rem] text-body">{{ $method }}</span>
                    @endforeach
                    <span class="px-1 text-[.62rem] text-body"><i class="fa-solid fa-lock"></i> Paiement sécurisé</span>
                </div>
            </div>
        </div>
    </footer>

    {{-- WhatsApp --}}
    <a href="{{ whatsapp_url('Bonjour Mombeya Galy !') }}" target="_blank" rel="noopener" aria-label="WhatsApp"
       class="fixed bottom-5 right-5 z-40 flex size-12 items-center justify-center rounded-full bg-[#25D366] text-2xl text-white shadow-lg transition hover:scale-110">
        <i class="fa-brands fa-whatsapp"></i>
    </a>

    {{-- Toasts --}}
    <div class="pointer-events-none fixed inset-x-0 bottom-6 z-[80] flex flex-col items-center gap-2 px-4">
        <template x-for="toast in $store.toast.items" :key="toast.id">
            <div x-transition class="pointer-events-auto flex items-center gap-4 bg-ink px-5 py-3.5 text-sm text-ivory shadow-xl">
                <i class="fa-solid fa-check text-[#7ad3a8]"></i>
                <span x-text="toast.message"></span>
                <template x-if="toast.link">
                    <a :href="toast.link.url" class="border-b border-ivory/60 text-xs tracking-[.14em] uppercase" x-text="toast.link.label"></a>
                </template>
            </div>
        </template>
    </div>

    @stack('scripts')
</body>
</html>
