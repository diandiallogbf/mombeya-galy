<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function index(Request $request): View
    {
        return $this->listing($request, Product::query(), [
            'title' => $request->filled('q') ? 'Résultats pour « '.$request->string('q')->limit(40).' »' : 'Boutique',
            'subtitle' => 'Toutes les créations Mombeya Galy',
        ]);
    }

    public function category(Request $request, Category $category): View
    {
        abort_unless($category->is_active, 404);

        return $this->listing($request, $category->products()->getQuery(), [
            'title' => $category->name,
            'subtitle' => $category->description,
            'category' => $category,
            'banner' => $category->imageUrl(),
        ]);
    }

    public function news(Request $request): View
    {
        return $this->listing($request, Product::where('is_new', true), [
            'title' => 'Nouveautés',
            'subtitle' => 'Les dernières créations de nos ateliers',
        ]);
    }

    public function promotions(Request $request): View
    {
        return $this->listing($request, Product::whereNotNull('discount_price')->whereColumn('discount_price', '<', 'price'), [
            'title' => 'Promotions',
            'subtitle' => 'Profitez de nos meilleures offres du moment',
        ]);
    }

    public function prestige(Request $request): View
    {
        return $this->listing($request, Product::where('is_prestige', true), [
            'title' => 'Mombeya Galy Prestige',
            'subtitle' => 'Des pièces d\'exception pour vos grandes occasions',
            'prestige' => true,
        ]);
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    private function listing(Request $request, Builder $query, array $meta): View
    {
        $sort = array_key_exists($request->query('tri'), Product::SORTS) ? $request->query('tri') : 'recent-ancien';

        $products = $query
            ->active()
            ->with('category')
            ->search($request->query('q'))
            ->sorted($sort)
            ->paginate(config('shop.per_page', 36))
            ->withQueryString();

        return view('shop.listing', array_merge([
            'products' => $products,
            'sort' => $sort,
            'search' => $request->query('q'),
            'category' => null,
            'banner' => null,
            'prestige' => false,
        ], $meta));
    }
}
