<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PressingOrder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrackingController extends Controller
{
    /**
     * Tracks both shop orders (MG…) and pressing orders (PR…).
     */
    public function show(Request $request): View
    {
        $reference = strtoupper(trim((string) $request->query('reference')));
        $order = null;
        $pressing = null;

        if ($reference !== '') {
            $order = Order::with('items')->where('reference', $reference)->first();
            $pressing = $order ? null : PressingOrder::with('items')->where('reference', $reference)->first();
        }

        return view('tracking', [
            'reference' => $reference,
            'order' => $order,
            'pressing' => $pressing,
            'searched' => $reference !== '',
        ]);
    }
}
