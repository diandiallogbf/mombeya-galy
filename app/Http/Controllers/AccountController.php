<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PressingOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        return view('account.index', [
            'user' => $user,
            'orders' => $user->orders()->withCount('items')->latest()->paginate(10),
            'pressingOrders' => $user->pressingOrders()->withCount('items')->latest()->take(10)->get(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:30'],
        ], [], ['name' => 'nom complet', 'phone' => 'téléphone']);

        $request->user()->update($data);

        return back()->with('success', 'Vos informations ont été mises à jour.');
    }

    public function order(Request $request, Order $order): View
    {
        abort_unless($order->user_id === $request->user()->id, 404);

        return view('account.order', ['order' => $order->load('items')]);
    }

    public function pressingOrder(Request $request, PressingOrder $pressingOrder): View
    {
        abort_unless($pressingOrder->user_id === $request->user()->id, 404);

        return view('account.pressing', ['order' => $pressingOrder->load('items')]);
    }
}
