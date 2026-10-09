<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\PressingZone;
use App\Models\Product;
use App\Models\Slide;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $base = fn () => Product::active()->with('category');

        $trendWeek = $base()->where('is_trend_week', true)->latest()->take(20)->get();
        if ($trendWeek->count() < 4) {
            $trendWeek = $base()->orderByDesc('sales_count')->take(20)->get();
        }

        $trendMonth = $base()->where('is_trend_month', true)->latest()->take(20)->get();
        if ($trendMonth->count() < 4) {
            $trendMonth = $base()->latest()->take(20)->get();
        }

        $accessories = $base()
            ->whereHas('category', fn ($q) => $q->where('slug', 'accessoires'))
            ->latest()->take(12)->get();

        return view('home', [
            'slides' => Slide::active()->get(),
            'homeCategories' => Category::active()->where('show_on_home', true)->get(),
            'trendWeek' => $trendWeek,
            'trendMonth' => $trendMonth,
            'accessories' => $accessories,
            'pressingZones' => PressingZone::active()->pluck('name'),
        ]);
    }
}
