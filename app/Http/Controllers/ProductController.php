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
            ->take(12)
            ->get();

        return view('product.show', [
            'product' => $product->load('category'),
            'related' => $related,
            'showrooms' => Showroom::active()->get(),
        ]);
    }

    public function quickView(Product $product): View
    {
        abort_unless($product->is_active, 404);

        return view('product.quick-view', ['product' => $product->load('category')]);
    }
}
