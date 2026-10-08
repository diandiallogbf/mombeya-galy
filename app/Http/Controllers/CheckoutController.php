<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\DeliveryZone;
use App\Models\Order;
use App\Models\Product;
use App\Support\Cart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        $lines = Cart::lines();
        if ($lines->isEmpty()) {
            return redirect()->route('cart.index');
        }

        return view('checkout.create', [
            'lines' => $lines,
            'subtotal' => (int) $lines->sum('total'),
            'zones' => DeliveryZone::active()->get(),
            'methods' => PaymentMethod::cases(),
            'user' => $request->user(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_phone' => ['required', 'string', 'max:30', 'regex:/^[0-9 +().-]{8,}$/'],
            'customer_email' => ['nullable', 'email', 'max:150'],
            'delivery_zone_id' => ['required', Rule::exists('delivery_zones', 'id')->where('is_active', true)],
            'address' => ['required', 'string', 'max:255'],
            'payment_method' => ['required', Rule::enum(PaymentMethod::class)],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [], [
            'customer_name' => 'nom complet',
            'customer_phone' => 'téléphone',
            'customer_email' => 'email',
            'delivery_zone_id' => 'zone de livraison',
            'address' => 'adresse',
            'payment_method' => 'mode de paiement',
        ]);

        $lines = Cart::lines();
        if ($lines->isEmpty()) {
            return redirect()->route('cart.index');
        }

        $zone = DeliveryZone::findOrFail($data['delivery_zone_id']);

        $order = DB::transaction(function () use ($data, $lines, $zone, $request) {
            $subtotal = 0;
            foreach ($lines as $line) {
                /** @var Product $product */
                $product = Product::lockForUpdate()->find($line->product->id);
                if (! $product || $product->stock < $line->quantity) {
                    throw ValidationException::withMessages([
                        'cart' => "Stock insuffisant pour « {$line->product->name} ». Merci de modifier votre panier.",
                    ]);
                }
                $subtotal += $line->total;
            }

            $order = Order::create([
                ...$data,
                'user_id' => $request->user()?->id,
                'delivery_zone_name' => $zone->name,
                'subtotal' => $subtotal,
                'delivery_fee' => $zone->fee,
                'total' => $subtotal + $zone->fee,
                'status' => OrderStatus::Pending,
                'payment_status' => PaymentStatus::Pending,
            ]);

            foreach ($lines as $line) {
                $order->items()->create([
                    'product_id' => $line->product->id,
                    'product_name' => $line->product->name,
                    'product_image' => $line->product->images[0] ?? null,
                    'size' => $line->size,
                    'color' => $line->color,
                    'quantity' => $line->quantity,
                    'unit_price' => $line->unit_price,
                    'total' => $line->total,
                ]);
                Product::whereKey($line->product->id)->decrement('stock', $line->quantity);
                Product::whereKey($line->product->id)->increment('sales_count', $line->quantity);
            }

            return $order;
        });

        Cart::clear();
        $request->session()->push('placed_orders', $order->reference);

        return redirect()->route('checkout.confirmation', $order);
    }

    public function confirmation(Request $request, Order $order): View
    {
        $allowed = in_array($order->reference, $request->session()->get('placed_orders', []), true)
            || ($request->user() && $order->user_id === $request->user()->id);
        abort_unless($allowed, 404);

        return view('checkout.confirmation', ['order' => $order->load('items')]);
    }
}
