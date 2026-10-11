<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Support\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WishlistController extends Controller
{
    public function index(): View
    {
        $ids = Wishlist::ids();
        $products = Product::active()->with('category')->whereIn('id', $ids)->get()
            ->sortBy(fn (Product $product) => array_search($product->id, $ids, true))
            ->values();

        return view('wishlist', ['products' => $products]);
    }

    public function toggle(Request $request, Product $product): JsonResponse|RedirectResponse
    {
        $added = Wishlist::toggle($product);
        $message = $added ? 'Ajouté à vos favoris' : 'Retiré de vos favoris';

        if ($request->expectsJson()) {
            return response()->json(['added' => $added, 'count' => Wishlist::count(), 'message' => $message]);
        }

        return back()->with('success', $message);
    }
}
