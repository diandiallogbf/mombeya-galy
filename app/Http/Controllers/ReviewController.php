<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Reviews are published only after an admin approves them.
     */
    public function store(Request $request, Product $product): RedirectResponse
    {
        abort_unless($product->is_active, 404);

        $data = $request->validateWithBag('review', [
            'name' => ['required', 'string', 'max:80'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['required', 'string', 'min:10', 'max:1000'],
        ], [
            'rating.required' => 'Choisissez une note de 1 à 5 étoiles.',
        ], [
            'name' => 'prénom',
            'comment' => 'avis',
        ]);

        $product->reviews()->create($data + [
            'user_id' => $request->user()?->id,
            'is_approved' => false,
        ]);

        return redirect()->to(route('product.show', $product).'#avis')
            ->with('success', 'Merci pour votre avis ! Il sera publié après validation par notre équipe.');
    }
}
