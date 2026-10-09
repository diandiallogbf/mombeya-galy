<?php

namespace App\Http\Controllers;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\PressingStatus;
use App\Models\PressingOrder;
use App\Models\PressingService;
use App\Models\PressingZone;
use App\Models\Showroom;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PressingController extends Controller
{
    /** Payment methods offered for pressing (paid upfront or at pick-up / delivery). */
    public const PAYMENT_METHODS = [
        'livraison' => 'En espèces à la récupération ou à la livraison',
        'orange_money' => 'Orange Money',
        'mtn_money' => 'MTN Mobile Money',
    ];

    public function index(): View
    {
        return view('pressing.index', [
            'servicesByCategory' => $this->servicesByCategory(),
            'zones' => PressingZone::active()->get(),
            'dropPoints' => Showroom::active()->where('accepts_pressing', true)->get(),
            'expressPercent' => self::expressPercent(),
        ]);
    }

    public function create(Request $request): View
    {
        return view('pressing.create', [
            'services' => PressingService::active()->get(),
            'servicesByCategory' => $this->servicesByCategory(),
            'zones' => PressingZone::active()->get(),
            'dropPoints' => Showroom::active()->where('accepts_pressing', true)->get(),
            'slots' => self::slots(),
            'expressPercent' => self::expressPercent(),
            'preselected' => (int) $request->query('service'),
            'user' => $request->user(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_phone' => ['required', 'string', 'max:30', 'regex:/^[0-9 +().-]{8,}$/'],
            'customer_email' => ['nullable', 'email', 'max:150'],
            'mode' => ['required', Rule::in(array_keys(PressingOrder::MODES))],
            'pressing_zone_id' => ['required_if:mode,collecte', 'nullable', Rule::exists('pressing_zones', 'id')->where('is_active', true)],
            'address' => ['required_if:mode,collecte', 'nullable', 'string', 'max:255'],
            'pickup_date' => ['required_if:mode,collecte', 'nullable', 'date', 'after_or_equal:today'],
            'pickup_slot' => ['required_if:mode,collecte', 'nullable', Rule::in(self::slots())],
            'showroom_id' => ['required_if:mode,depot', 'nullable', Rule::exists('showrooms', 'id')->where('is_active', true)->where('accepts_pressing', true)],
            'express' => ['nullable', 'boolean'],
            'items' => ['required', 'array'],
            'items.*' => ['nullable', 'integer', 'min:0', 'max:50'],
            'payment_method' => ['required', Rule::in(array_keys(self::PAYMENT_METHODS))],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'pressing_zone_id.required_if' => 'Choisissez votre zone de collecte.',
            'address.required_if' => 'Indiquez l\'adresse de collecte.',
            'pickup_date.required_if' => 'Choisissez la date de collecte.',
            'pickup_slot.required_if' => 'Choisissez un créneau de collecte.',
            'showroom_id.required_if' => 'Choisissez le showroom où vous déposerez vos vêtements.',
            'items.required' => 'Ajoutez au moins un article.',
        ], [
            'customer_name' => 'nom complet',
            'customer_phone' => 'téléphone',
            'pickup_date' => 'date de collecte',
            'payment_method' => 'mode de paiement',
        ]);

        $quantities = collect($data['items'])->map(fn ($q) => (int) $q)->filter(fn ($q) => $q > 0);
        $services = PressingService::active()->whereIn('id', $quantities->keys())->get();
        if ($services->isEmpty()) {
            throw ValidationException::withMessages(['items' => 'Ajoutez au moins un article.']);
        }

        $collecte = $data['mode'] === 'collecte';
        $zone = $collecte ? PressingZone::find($data['pressing_zone_id']) : null;
        $showroom = $collecte ? null : Showroom::find($data['showroom_id']);
        $express = (bool) ($data['express'] ?? false);

        $order = DB::transaction(function () use ($data, $services, $quantities, $collecte, $zone, $showroom, $express, $request) {
            $subtotal = $services->sum(fn (PressingService $s) => $s->price * $quantities[$s->id]);
            $expressFee = $express ? (int) round($subtotal * self::expressPercent() / 100) : 0;
            $collectionFee = $zone?->fee ?? 0;

            $order = PressingOrder::create([
                'user_id' => $request->user()?->id,
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'],
                'customer_email' => $data['customer_email'] ?? null,
                'mode' => $data['mode'],
                'pressing_zone_id' => $zone?->id,
                'zone_name' => $zone?->name,
                'address' => $collecte ? $data['address'] : null,
                'showroom_id' => $showroom?->id,
                'showroom_name' => $showroom?->name,
                'pickup_date' => $collecte ? $data['pickup_date'] : null,
                'pickup_slot' => $collecte ? $data['pickup_slot'] : null,
                'express' => $express,
                'subtotal' => $subtotal,
                'express_fee' => $expressFee,
                'collection_fee' => $collectionFee,
                'total' => $subtotal + $expressFee + $collectionFee,
                'payment_method' => $data['payment_method'],
                'payment_status' => PaymentStatus::Pending,
                'status' => PressingStatus::Requested,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($services as $service) {
                $quantity = $quantities[$service->id];
                $order->items()->create([
                    'pressing_service_id' => $service->id,
                    'service_name' => $service->name,
                    'quantity' => $quantity,
                    'unit_price' => $service->price,
                    'total' => $service->price * $quantity,
                ]);
            }

            return $order;
        });

        $request->session()->push('placed_pressing', $order->reference);

        return redirect()->route('pressing.confirmation', $order);
    }

    public function confirmation(Request $request, PressingOrder $pressingOrder): View
    {
        $allowed = in_array($pressingOrder->reference, $request->session()->get('placed_pressing', []), true)
            || ($request->user() && $pressingOrder->user_id === $request->user()->id);
        abort_unless($allowed, 404);

        return view('pressing.confirmation', ['order' => $pressingOrder->load('items')]);
    }

    public static function expressPercent(): int
    {
        return max(0, (int) setting('pressing_express_percent', 50));
    }

    /**
     * Pick-up time slots, configured in the admin as a comma-separated list.
     *
     * @return array<int, string>
     */
    public static function slots(): array
    {
        return array_values(array_filter(array_map('trim', explode(',', (string) setting('pressing_slots')))));
    }

    public static function paymentLabel(PaymentMethod $method): string
    {
        return self::PAYMENT_METHODS[$method->value] ?? $method->getLabel();
    }

    /**
     * @return Collection<string, Collection<int, PressingService>>
     */
    private function servicesByCategory()
    {
        $services = PressingService::active()->get();

        return collect(PressingService::CATEGORIES)
            ->map(fn ($label, $key) => $services->where('category', $key)->values())
            ->filter(fn ($group) => $group->isNotEmpty());
    }
}
