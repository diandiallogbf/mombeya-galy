@extends('layouts.app')

@section('title', 'Mon compte')

@section('content')
    <x-page-header title="Mon compte" :breadcrumbs="['Mon compte' => null]" />

    <div class="container-shop">
        <div class="relative -mt-20 grid gap-6 lg:grid-cols-12">
            <aside class="lg:col-span-4">
                <div class="rounded-lg bg-white p-6 shadow-lg">
                    <div class="mb-5 flex items-center gap-4">
                        <span class="flex size-14 items-center justify-center rounded-full bg-brand-light text-xl font-medium text-brand">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span>
                        <div>
                            <p class="font-medium text-ink">{{ $user->name }}</p>
                            <p class="text-sm text-muted">{{ $user->email }}</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('account.update') }}" class="space-y-3">
                        @csrf @method('PUT')
                        <div>
                            <label class="form-label" for="name">Nom complet</label>
                            <input id="name" name="name" value="{{ old('name', $user->name) }}" required class="form-control">
                            @error('name')<p class="form-error">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="form-label" for="phone">Téléphone</label>
                            <input id="phone" name="phone" value="{{ old('phone', $user->phone) }}" class="form-control">
                        </div>
                        <button class="btn btn-primary btn-sm w-full">Enregistrer</button>
                    </form>
                    <form method="POST" action="{{ route('logout') }}" class="mt-4 border-t border-line pt-4">
                        @csrf
                        <button class="btn btn-sm btn-outline-dark w-full"><i class="fa-solid fa-right-from-bracket"></i> Se déconnecter</button>
                    </form>
                    @if ($user->is_admin)
                        <a href="{{ url('/admin') }}" class="btn btn-sm btn-secondary mt-3 w-full"><i class="fa-solid fa-gauge"></i> Administration</a>
                    @endif
                </div>
            </aside>

            <section class="lg:col-span-8">
                <div class="rounded-lg bg-white p-6 shadow-lg">
                    <h2 class="mb-5 text-lg">Mes commandes</h2>
                    @if ($orders->isEmpty())
                        <div class="py-10 text-center">
                            <i class="fa-solid fa-box-open mb-3 text-4xl text-line"></i>
                            <p class="mb-5 text-muted">Vous n'avez pas encore passé de commande.</p>
                            <a href="{{ route('shop.index') }}" class="btn btn-primary btn-sm">Découvrir la boutique</a>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead class="border-b border-line text-xs uppercase text-muted">
                                    <tr><th class="py-2">Référence</th><th>Date</th><th>Articles</th><th>Statut</th><th class="text-right">Total</th></tr>
                                </thead>
                                <tbody class="divide-y divide-line">
                                    @foreach ($orders as $order)
                                        <tr>
                                            <td class="py-3"><a href="{{ route('account.order', $order) }}" class="font-mono font-medium text-brand hover:underline">{{ $order->reference }}</a></td>
                                            <td>{{ $order->created_at->format('d/m/Y') }}</td>
                                            <td>{{ $order->items_count }}</td>
                                            <td>@include('partials.status-badge', ['status' => $order->status])</td>
                                            <td class="text-right">{{ money($order->total, 'GNF') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-5">{{ $orders->links() }}</div>
                    @endif
                </div>

                <div class="mt-6 rounded-lg bg-white p-6 shadow-lg">
                    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
                        <h2 class="text-lg"><i class="fa-solid fa-soap mr-1 text-brand"></i> Mes réservations pressing</h2>
                        <a href="{{ route('pressing.create') }}" class="btn btn-sm btn-outline">Nouvelle réservation</a>
                    </div>
                    @if ($pressingOrders->isEmpty())
                        <p class="py-4 text-center text-sm text-muted">Aucune réservation pour le moment.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead class="border-b border-line text-xs uppercase text-muted">
                                    <tr><th class="py-2">Référence</th><th>Date</th><th>Articles</th><th>Statut</th><th class="text-right">Total</th></tr>
                                </thead>
                                <tbody class="divide-y divide-line">
                                    @foreach ($pressingOrders as $pressingOrder)
                                        <tr>
                                            <td class="py-3"><a href="{{ route('account.pressing', $pressingOrder) }}" class="font-mono font-medium text-brand hover:underline">{{ $pressingOrder->reference }}</a></td>
                                            <td>{{ $pressingOrder->created_at->format('d/m/Y') }}</td>
                                            <td>{{ $pressingOrder->items_count }}</td>
                                            <td>@include('partials.status-badge', ['status' => $pressingOrder->status])</td>
                                            <td class="text-right">{{ money($pressingOrder->total, 'GNF') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </section>
        </div>
    </div>
@endsection
