<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function __invoke(Request $request, string $code): RedirectResponse
    {
        $code = strtoupper($code);
        if (array_key_exists($code, config('shop.currencies'))) {
            $request->session()->put('currency', $code);
        }

        return redirect()->back(fallback: route('home'));
    }
}
