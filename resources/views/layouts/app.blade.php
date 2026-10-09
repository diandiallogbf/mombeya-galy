<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="cart-count" content="{{ $cartCount }}">
    <meta name="cart-total" content="{{ $cartTotal }}">
    <title>@hasSection('title')@yield('title') | @endif{{ setting('shop_name') }} – Mode africaine à Conakry</title>
    <meta name="description" content="@yield('description', setting('shop_name').' : grands boubous, tenues traditionnelles, bazin Getzner, chemises et accessoires. Showrooms à Conakry, livraison partout dans le monde.')">
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <meta property="og:title" content="@yield('title', setting('shop_name'))">
    <meta property="og:image" content="@yield('og_image', asset('images/logo-rond.jpg'))">
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-screen pb-16 lg:pb-0" x-data>

    {{-- ============ TOP BAR ============ --}}
    <div class="bg-night text-sm text-white/65">
        <div class="container-shop flex h-10 items-center justify-between gap-4">
            <div class="hidden items-center gap-2 md:flex">
                <i class="fa-solid fa-headset text-brand"></i>
                <span>Appelez-nous au</span>
                <a href="tel:{{ preg_replace('/\s+/', '', setting('phone')) }}" class="text-white/85 hover:text-white">{{ setting('phone') }}</a>
            </div>

            {{-- Mobile "important links" dropdown --}}
            <div class="relative md:hidden" x-data="{ open: false }" @click.outside="open = false">
                <button type="button" @click="open = !open" class="flex items-center gap-2 text-white/85">
                    <i class="fa-solid fa-link text-brand"></i> Liens importants <i class="fa-solid fa-chevron-down text-[10px]"></i>
                </button>
                <div x-cloak x-show="open" x-transition class="absolute left-0 top-8 z-50 w-60 rounded-md bg-white py-2 text-body shadow-lg">
                    <a href="tel:{{ preg_replace('/\s+/', '', setting('phone')) }}" class="flex items-center gap-2 px-4 py-2 hover:text-brand"><i class="fa-solid fa-phone w-4 text-muted"></i>{{ setting('phone') }}</a>
                    <a href="{{ route('tracking') }}" class="flex items-center gap-2 px-4 py-2 hover:text-brand"><i class="fa-solid fa-location-dot w-4 text-muted"></i>Suivi de la commande</a>
                    <a href="{{ route('showrooms') }}" class="flex items-center gap-2 px-4 py-2 hover:text-brand"><i class="fa-solid fa-store w-4 text-muted"></i>Nos showrooms</a>
                </div>
            </div>

            <div class="hidden text-center lg:block">{{ setting('topbar_message') }}</div>

            <div class="flex items-center gap-5">
                <a href="{{ route('tracking') }}" class="hidden items-center gap-1.5 hover:text-white md:flex">
                    <i class="fa-solid fa-location-dot text-brand"></i> Suivi de commande
                </a>
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button type="button" @click="open = !open" class="flex items-center gap-1.5 text-white/85 hover:text-white">
                        <i class="fa-solid fa-coins text-brand"></i> {{ $currentCurrency }} <i class="fa-solid fa-chevron-down text-[10px]"></i>
                    </button>
                    <div x-cloak x-show="open" x-transition class="absolute right-0 top-8 z-50 w-36 rounded-md bg-white py-2 text-body shadow-lg">
                        @foreach (config('shop.currencies') as $code => $currency)
                            <a href="{{ route('currency', $code) }}" @class(['block px-4 py-1.5 hover:text-brand', 'font-medium text-brand' => $code === $currentCurrency])>{{ $currency['label'] }}</a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="h-1.5 bg-gradient-to-r from-brand via-brand to-azure"></div>

    {{-- ============ NAVBAR ============ --}}
    <header class="relative z-40 bg-white shadow-sm" x-data="{ menu: false }" @open-menu.window="menu = true">
        <div class="container-shop flex h-[68px] items-center gap-4">
            <button type="button" class="text-2xl text-ink lg:hidden" @click="menu = true" aria-label="Menu">
                <i class="fa-solid fa-bars"></i>
            </button>

            <a href="{{ route('home') }}" class="shrink-0">
                <img src="{{ asset('images/logo.png') }}" alt="{{ setting('shop_name') }}" class="h-[52px] w-auto">
            </a>

            <nav class="ml-4 hidden flex-1 items-center lg:flex">
                @php
                    $links = [
                        ['Accueil', route('home'), request()->routeIs('home'), false],
                        ['Pressing', route('pressing.index'), request()->routeIs('pressing.*'), true],
                        ['Prestige', route('shop.prestige'), request()->routeIs('shop.prestige'), false],
                    ];
                @endphp
                @foreach ($links as [$label, $url, $active, $bold])
                    <a href="{{ $url }}" @class(['px-[1.05rem] py-5 text-[.95rem] transition hover:text-brand', 'text-brand' => $active, 'text-body' => ! $active, 'font-medium' => $bold])>{{ $label }}</a>
                @endforeach

                {{-- Boutique with categories dropdown --}}
                <div class="group relative">
                    <a href="{{ route('shop.index') }}" @class(['flex items-center gap-1 px-[1.05rem] py-5 text-[.95rem] transition hover:text-brand', 'text-brand' => request()->routeIs('shop.index', 'shop.category'), 'text-body' => ! request()->routeIs('shop.index', 'shop.category')])>
                        Boutique <i class="fa-solid fa-chevron-down text-[10px]"></i>
                    </a>
                    <div class="invisible absolute left-0 top-full w-72 rounded-b-lg border-t-2 border-brand bg-white py-2 opacity-0 shadow-lg transition group-hover:visible group-hover:opacity-100">
                        @foreach ($menuCategories as $menuCategory)
                            <a href="{{ route('shop.category', $menuCategory) }}" class="flex items-center gap-3 px-5 py-2 text-sm text-body hover:bg-soft hover:text-brand">
                                <i class="{{ $menuCategory->icon ?: 'fa-solid fa-tag' }} w-4 text-center text-muted"></i>{{ $menuCategory->name }}
                            </a>
                        @endforeach
                        <div class="my-2 border-t border-line"></div>
                        <a href="{{ route('shop.news') }}" class="flex items-center gap-3 px-5 py-2 text-sm text-body hover:bg-soft hover:text-brand"><i class="fa-solid fa-circle-plus w-4 text-center text-muted"></i>Nouveautés</a>
                        <a href="{{ route('shop.promotions') }}" class="flex items-center gap-3 px-5 py-2 text-sm text-body hover:bg-soft hover:text-brand"><i class="fa-solid fa-percent w-4 text-center text-muted"></i>Promotions</a>
                        <a href="{{ route('shop.index') }}" class="flex items-center gap-3 px-5 py-2 text-sm font-medium text-brand hover:bg-soft"><i class="fa-solid fa-store w-4 text-center"></i>Toute la boutique</a>
                    </div>
                </div>

                <a href="{{ route('videos') }}" @class(['px-[1.05rem] py-5 text-[.95rem] font-medium transition hover:text-brand', 'text-brand' => request()->routeIs('videos'), 'text-body' => ! request()->routeIs('videos')])>Vidéos</a>
                <a href="{{ route('showrooms') }}" @class(['px-[1.05rem] py-5 text-[.95rem] transition hover:text-brand', 'text-brand' => request()->routeIs('showrooms'), 'text-body' => ! request()->routeIs('showrooms')])>Nos showrooms</a>
            </nav>

            <div class="ml-auto flex items-center gap-2 sm:gap-4">
                <a href="{{ route('shop.index') }}" class="hidden size-11 items-center justify-center rounded-full text-lg text-ink transition hover:bg-soft hover:text-brand sm:flex" title="Rechercher">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </a>
                <a href="{{ auth()->check() ? route('account.index') : route('login') }}" class="hidden size-11 items-center justify-center rounded-full border border-line text-lg text-ink transition hover:border-brand hover:text-brand sm:flex" title="Voir mon compte client">
                    <i class="fa-regular fa-user"></i>
                </a>

                {{-- Cart with mini-cart dropdown --}}
                <div class="relative" x-data="{ open: false, html: '', async load() { this.html = await (await fetch('{{ route('cart.index', ['mini' => 1]) }}')).text(); } }"
                     @mouseenter="if (window.innerWidth >= 1024) { open = true; load(); }" @mouseleave="open = false"
                     @cart-updated.window="html = ''">
                    <a href="{{ route('cart.index') }}" class="flex items-center gap-3">
                        <span class="relative flex size-11 items-center justify-center rounded-full bg-soft text-lg text-ink">
                            <i class="fa-solid fa-cart-shopping"></i>
                            <span class="absolute -right-1 -top-1 flex size-5 items-center justify-center rounded-full bg-brand text-[.7rem] font-medium text-white" x-text="$store.cart.count">{{ $cartCount }}</span>
                        </span>
                        <span class="hidden leading-tight sm:block">
                            <small class="block text-xs text-muted">Mon panier</small>
                            <span class="text-sm font-medium text-ink" x-text="$store.cart.total">{{ $cartTotal }}</span>
                        </span>
                    </a>
                    <div x-cloak x-show="open" x-transition.opacity class="absolute right-0 top-full z-50 w-80 pt-3">
                        <div class="rounded-lg bg-white p-4 shadow-xl ring-1 ring-black/5">
                            <div x-show="!html" class="py-6 text-center text-sm text-muted"><i class="fa-solid fa-spinner fa-spin"></i></div>
                            <div x-html="html"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============ MOBILE OFF-CANVAS MENU ============ --}}
        <div x-cloak x-show="menu" class="fixed inset-0 z-[60] lg:hidden">
            <div x-show="menu" x-transition.opacity class="absolute inset-0 bg-black/50" @click="menu = false"></div>
            <aside x-show="menu" x-transition:enter="transition duration-300" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
                   x-transition:leave="transition duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
                   class="absolute inset-y-0 left-0 flex w-72 max-w-[85vw] flex-col overflow-y-auto bg-white">
                <div class="flex items-center justify-between border-b border-line bg-soft px-4 py-3">
                    <img src="{{ asset('images/logo.png') }}" alt="{{ setting('shop_name') }}" class="h-11 w-auto">
                    <button type="button" class="text-xl text-muted" @click="menu = false" aria-label="Fermer"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <form action="{{ route('shop.index') }}" class="px-4 pt-4">
                    <div class="relative">
                        <input type="search" name="q" placeholder="Rechercher un article…" class="form-control pr-10 text-sm">
                        <i class="fa-solid fa-magnifying-glass absolute right-3 top-1/2 -translate-y-1/2 text-muted"></i>
                    </div>
                </form>
                <nav class="flex flex-col gap-1 px-4 py-4 text-body">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 py-2 hover:text-brand"><i class="fa-solid fa-house w-5 text-center text-brand"></i>Accueil</a>
                    @foreach ($menuCategories as $menuCategory)
                        <a href="{{ route('shop.category', $menuCategory) }}" class="flex items-center gap-3 py-2 hover:text-brand"><i class="{{ $menuCategory->icon ?: 'fa-solid fa-tag' }} w-5 text-center text-brand"></i>{{ $menuCategory->name }}</a>
                    @endforeach
                    <a href="{{ route('pressing.index') }}" class="flex items-center gap-3 py-2 font-medium hover:text-brand"><i class="fa-solid fa-soap w-5 text-center text-brand"></i>Mombeya Galy Pressing</a>
                    <a href="{{ route('shop.prestige') }}" class="flex items-center gap-3 py-2 hover:text-brand"><i class="fa-solid fa-award w-5 text-center text-brand"></i>Mombeya Galy Prestige</a>
                    <a href="{{ route('shop.index') }}" class="flex items-center gap-3 py-2 hover:text-brand"><i class="fa-solid fa-store w-5 text-center text-brand"></i>Boutique</a>
                    <a href="{{ route('shop.news') }}" class="flex items-center gap-3 py-2 hover:text-brand"><i class="fa-solid fa-circle-plus w-5 text-center text-brand"></i>Nouveautés</a>
                    <a href="{{ route('shop.promotions') }}" class="flex items-center gap-3 py-2 hover:text-brand"><i class="fa-solid fa-percent w-5 text-center text-brand"></i>Promotions</a>
                    <a href="{{ auth()->check() ? route('account.index') : route('login') }}" class="flex items-center gap-3 py-2 hover:text-brand"><i class="fa-solid fa-box w-5 text-center text-brand"></i>Mes commandes</a>
                    <a href="{{ route('showrooms') }}" class="flex items-center gap-3 py-2 hover:text-brand"><i class="fa-solid fa-building-columns w-5 text-center text-brand"></i>Nos showrooms</a>
                    <a href="{{ route('videos') }}" class="flex items-center gap-3 py-2 font-medium hover:text-brand"><i class="fa-solid fa-photo-film w-5 text-center text-brand"></i>Vidéos</a>
                </nav>
            </aside>
        </div>
    </header>

    {{-- Flash messages --}}
    @if (session('success') || session('error'))
        <div class="container-shop mt-4">
            <div @class([
                'flex items-start gap-3 rounded-lg border px-4 py-3 text-sm',
                'border-success/40 bg-success/10 text-[#1f7a52]' => session('success'),
                'border-danger/40 bg-danger/10 text-danger' => session('error'),
            ]) x-data="{ show: true }" x-show="show">
                <i @class(['fa-solid mt-0.5', 'fa-circle-check' => session('success'), 'fa-circle-exclamation' => session('error')])></i>
                <span class="flex-1">{{ session('success') ?? session('error') }}</span>
                <button type="button" @click="show = false" class="opacity-60 hover:opacity-100"><i class="fa-solid fa-xmark"></i></button>
            </div>
        </div>
    @endif

    <main>
        @yield('content')
    </main>

    {{-- Mobile "order on WhatsApp" block (replaces the app download block of the original) --}}
    <section class="container-shop my-10 md:hidden">
        <div class="flex items-center gap-4 rounded-xl bg-soft p-5">
            <div class="flex-1">
                <p class="text-sm text-muted">Commandez en un message</p>
                <p class="text-lg font-medium text-ink">Mombeya Galy sur WhatsApp</p>
                <a href="{{ whatsapp_url('Bonjour Mombeya Galy, je souhaite passer une commande.') }}" target="_blank" rel="noopener" class="btn btn-sm mt-3 bg-[#25D366] text-white hover:bg-[#1da851]">
                    <i class="fa-brands fa-whatsapp"></i> Écrire maintenant
                </a>
            </div>
            <img src="{{ asset('images/favicon.png') }}" alt="" class="size-20 rounded-full bg-white p-2">
        </div>
    </section>

    {{-- ============ FOOTER ============ --}}
    <footer class="mt-12">
        <div class="bg-white py-10 text-center">
            <img src="{{ asset('images/logo.png') }}" alt="{{ setting('shop_name') }}" class="mx-auto mb-4 w-48">
            <p class="mb-6 text-sm text-muted">{{ setting('slogan') }}</p>
            <p class="mb-4 font-medium text-ink">Suivez-nous sur les réseaux sociaux</p>
            <div class="flex justify-center gap-2">
                @foreach (['facebook_url' => 'fa-facebook-f', 'instagram_url' => 'fa-instagram', 'tiktok_url' => 'fa-tiktok', 'youtube_url' => 'fa-youtube', 'twitter_url' => 'fa-x-twitter'] as $key => $icon)
                    @if (setting($key))
                        <a href="{{ setting($key) }}" target="_blank" rel="noopener" class="flex size-9 items-center justify-center rounded-[.3125rem] bg-soft text-ink transition hover:bg-brand hover:text-white">
                            <i class="fa-brands {{ $icon }}"></i>
                        </a>
                    @endif
                @endforeach
            </div>
        </div>

        <div class="bg-night pt-12 pb-6">
            <div class="container-shop">
                <div class="grid gap-8 border-b border-white/10 pb-10 sm:grid-cols-2 md:grid-cols-4">
                    <div class="flex gap-4">
                        <i class="fa-solid fa-truck-fast text-3xl text-brand"></i>
                        <div><h6 class="mb-1 text-white">Livraison</h6><p class="text-sm text-white/50">{{ setting('delivery_text') }}</p></div>
                    </div>
                    <div class="flex gap-4">
                        <i class="fa-solid fa-money-bill-transfer text-3xl text-brand"></i>
                        <div><h6 class="mb-1 text-white">Moyens de paiement</h6><p class="text-sm text-white/50">{{ setting('payment_text') }}</p></div>
                    </div>
                    <div class="flex gap-4">
                        <i class="fa-solid fa-headset text-3xl text-brand"></i>
                        <div><h6 class="mb-1 text-white">Assistance clientèle</h6><p class="text-sm text-white/50">{{ setting('topbar_message') }}</p></div>
                    </div>
                    <div class="flex gap-4">
                        <i class="fa-solid fa-tags text-3xl text-brand"></i>
                        <div>
                            <h6 class="mb-2 text-white">Petits prix : moins de {{ money((int) setting('promo_threshold')) }}</h6>
                            <a href="{{ route('shop.promotions') }}" class="btn btn-primary btn-sm">Acheter maintenant</a>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap justify-center gap-x-6 gap-y-2 py-6 text-sm text-white/60">
                    <a href="{{ route('shop.index') }}" class="hover:text-white">Boutique</a>
                    <a href="{{ route('shop.news') }}" class="hover:text-white">Nouveautés</a>
                    <a href="{{ route('shop.promotions') }}" class="hover:text-white">Promotions</a>
                    <a href="{{ route('shop.prestige') }}" class="hover:text-white">Prestige</a>
                    <a href="{{ route('tracking') }}" class="hover:text-white">Suivi de commande</a>
                    <a href="{{ route('showrooms') }}" class="hover:text-white">Nos showrooms</a>
                    <a href="{{ route('pressing.index') }}" class="hover:text-white">Pressing</a>
                </div>

                <div class="flex flex-col items-center justify-between gap-4 md:flex-row">
                    <p class="text-xs text-white/50">© {{ date('Y') }} {{ setting('shop_name') }} – Tous les droits sont réservés. {{ setting('address') }}</p>
                    <div class="flex flex-wrap items-center justify-center gap-2 text-[.7rem] font-bold">
                        <span class="rounded bg-[#FF7900] px-2 py-1 text-white">Orange Money</span>
                        <span class="rounded bg-[#FFCC00] px-2 py-1 text-black">MTN MoMo</span>
                        <span class="rounded bg-white px-2 py-1 text-[#1A1F71]"><i class="fa-brands fa-cc-visa text-base align-middle"></i></span>
                        <span class="rounded bg-white px-2 py-1 text-[#EB001B]"><i class="fa-brands fa-cc-mastercard text-base align-middle"></i></span>
                        <span class="rounded bg-[#FFDD00] px-2 py-1 text-black">Western Union</span>
                        <span class="rounded bg-white/10 px-2 py-1 text-white"><i class="fa-solid fa-lock"></i> Paiement sécurisé</span>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    {{-- ============ MOBILE BOTTOM TOOLBAR ============ --}}
    <div class="fixed inset-x-0 bottom-0 z-40 grid h-16 grid-cols-3 border-t border-line bg-white text-xs text-ink lg:hidden">
        <button type="button" @click="$dispatch('open-menu')" class="flex flex-col items-center justify-center gap-1"><i class="fa-solid fa-bars text-lg"></i>Menu</button>
        <a href="{{ auth()->check() ? route('account.index') : route('login') }}" class="flex flex-col items-center justify-center gap-1 border-x border-line"><i class="fa-regular fa-user text-lg"></i>Mon compte</a>
        <a href="{{ route('cart.index') }}" class="flex flex-col items-center justify-center gap-1">
            <span class="relative"><i class="fa-solid fa-cart-shopping text-lg"></i><span class="absolute -right-3 -top-2 rounded-full bg-brand px-1.5 text-[.65rem] text-white" x-text="$store.cart.count">{{ $cartCount }}</span></span>
            <span x-text="$store.cart.total">{{ $cartTotal }}</span>
        </a>
    </div>

    {{-- WhatsApp floating button --}}
    <a href="{{ whatsapp_url('Bonjour Mombeya Galy !') }}" target="_blank" rel="noopener" aria-label="WhatsApp"
       class="fixed bottom-24 right-5 z-40 flex size-14 items-center justify-center rounded-full bg-[#25D366] text-3xl text-white shadow-lg transition hover:scale-110 lg:bottom-20 lg:right-8">
        <i class="fa-brands fa-whatsapp"></i>
    </a>

    {{-- Back to top --}}
    <div x-data="{ show: false }" @scroll.window.throttle.100ms="show = window.scrollY > 600">
        <button type="button" x-cloak x-show="show" x-transition @click="window.scrollTo({ top: 0, behavior: 'smooth' })" title="Haut"
                class="fixed bottom-[10.5rem] right-[1.6rem] z-40 flex size-11 items-center justify-center rounded-full bg-ink/25 text-white backdrop-blur transition hover:bg-brand lg:bottom-6 lg:right-9">
            <i class="fa-solid fa-arrow-up"></i>
        </button>
    </div>

    {{-- Toasts --}}
    <div class="pointer-events-none fixed inset-x-0 top-4 z-[80] flex flex-col items-center gap-2 px-4">
        <template x-for="toast in $store.toast.items" :key="toast.id">
            <div x-transition class="pointer-events-auto flex items-center gap-3 rounded-lg bg-ink px-5 py-3 text-sm text-white shadow-xl">
                <i class="fa-solid fa-circle-check text-success"></i>
                <span x-text="toast.message"></span>
                <a href="{{ route('cart.index') }}" class="ml-2 font-medium text-azure hover:underline">Voir le panier</a>
            </div>
        </template>
    </div>

    {{-- Quick view modal --}}
    <div x-data="quickView" @quick-view.window="show($event.detail)" @keydown.escape.window="close()">
        <div x-cloak x-show="open" class="fixed inset-0 z-[70] flex items-start justify-center overflow-y-auto bg-black/60 p-4 md:items-center" @click.self="close()">
            <div x-show="open" x-transition class="relative w-full max-w-5xl rounded-xl bg-white shadow-2xl">
                <button type="button" @click="close()" class="absolute right-3 top-3 z-10 flex size-9 items-center justify-center rounded-full bg-soft text-ink hover:bg-brand hover:text-white" aria-label="Fermer"><i class="fa-solid fa-xmark"></i></button>
                <div x-show="loading" class="p-16 text-center text-2xl text-brand"><i class="fa-solid fa-spinner fa-spin"></i></div>
                <div x-html="html"></div>
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
