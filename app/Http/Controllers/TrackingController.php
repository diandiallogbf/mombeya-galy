<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrackingController extends Controller
{
    public function show(Request $request): View
    {
        $reference = strtoupper(trim((string) $request->query('reference')));
        $order = $reference !== '' ? Order::with('items')->where('reference', $reference)->first() : null;

        return view('tracking', [
            'reference' => $reference,
            'order' => $order,
            'searched' => $reference !== '',
        ]);
    }
}
