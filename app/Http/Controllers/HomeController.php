<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\GalleryPhoto;
use App\Models\Product;
use App\Models\Review;
use App\Models\Showroom;
use App\Models\Slide;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $base = fn () => Product::active()->with('category');

        $essentials = $base()->where('is_trend_week', true)->latest()->take(12)->get();
        if ($essentials->count() < 4) {
            $essentials = $base()->orderByDesc('sales_count')->take(12)->get();
        }

        $newCollection = $base()->where('is_trend_month', true)->latest()->take(8)->get();
        if ($newCollection->count() < 4) {
            $newCollection = $base()->latest()->take(8)->get();
        }

        $accessories = $base()
            ->whereHas('category', fn ($q) => $q->where('slug', 'accessoires'))
            ->latest()->take(10)->get();

        return view('home', [
            'slides' => Slide::active()->get(),
            'universes' => Category::active()->where('show_on_home', true)->get(),
            'essentials' => $essentials,
            'newCollection' => $newCollection,
            'accessories' => $accessories,
            'testimonials' => Review::approved()->where('rating', '>=', 4)->with('product')->latest()->take(9)->get(),
            'gallery' => GalleryPhoto::active()->take(8)->get(),
            'showroomsCount' => Showroom::active()->count(),
        ]);
    }
}
