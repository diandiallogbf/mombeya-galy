<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Support\Cart;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(Request $request): View
    {
        $lines = Cart::lines();

        return view($request->boolean('mini') ? 'cart.mini' : 'cart.index', [
            'lines' => $lines,
            'subtotal' => (int) $lines->sum('total'),
        ]);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'size' => ['nullable', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'max:50'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:'.config('shop.max_quantity', 20)],
        ]);

        $product = Product::findOrFail($data['product_id']);

        if (! $product->is_active || ! $product->inStock()) {
            throw ValidationException::withMessages(['product_id' => 'Ce produit n\'est plus disponible (fin de stock).']);
        }
        if ($product->sizeList() && ! in_array($data['size'] ?? null, $product->sizeList(), true)) {
            throw ValidationException::withMessages(['size' => 'Veuillez choisir une taille.']);
        }
        if ($product->colorList() && ! in_array($data['color'] ?? null, $product->colorList(), true)) {
            throw ValidationException::withMessages(['color' => 'Veuillez choisir une couleur.']);
        }

        Cart::add(
            $product,
            (int) ($data['quantity'] ?? 1),
            $product->sizeList() ? $data['size'] : null,
            $product->colorList() ? $data['color'] : null,
        );

        return $this->respond($request, 'Produit ajouté au panier avec succès');
    }

    public function update(Request $request, string $key): JsonResponse|RedirectResponse
    {
        $data = $request->validate(['quantity' => ['required', 'integer', 'min:0', 'max:'.config('shop.max_quantity', 20)]]);
        Cart::update($key, (int) $data['quantity']);

        return $this->respond($request, 'Panier mis à jour');
    }

    public function destroy(Request $request, string $key): JsonResponse|RedirectResponse
    {
        Cart::remove($key);

        return $this->respond($request, 'Article retiré du panier');
    }

    private function respond(Request $request, string $message): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'count' => Cart::count(),
                'total' => money(Cart::subtotal()),
            ]);
        }

        return redirect()->route('cart.index')->with('success', $message);
    }
}
