<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Showroom;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function show(Product $product): View
    {
        abort_unless($product->is_active, 404);

        Product::withoutTimestamps(fn () => $product->increment('views'));

        $related = Product::active()
            ->with('category')
            ->whereKeyNot($product->id)
            ->when($product->category_id, fn ($q) => $q->orderByRaw('category_id = ? desc', [$product->category_id]))
            ->inRandomOrder()
            ->take(8)
            ->get();

        $reviews = $product->reviews()->approved()->latest()->get();

        return view('product.show', [
            'product' => $product->load('category'),
            'related' => $related,
            'showrooms' => Showroom::active()->get(),
            'reviews' => $reviews,
            'rating' => $reviews->isNotEmpty() ? round($reviews->avg('rating'), 1) : null,
        ]);
    }
}
