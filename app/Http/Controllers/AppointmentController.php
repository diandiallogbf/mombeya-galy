<?php

namespace App\Http\Controllers;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Showroom;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public const BUDGETS = [
        'Moins de 1.000.000 GNF',
        '1.000.000 – 2.500.000 GNF',
        '2.500.000 – 5.000.000 GNF',
        'Plus de 5.000.000 GNF',
    ];

    public function create(Request $request): View
    {
        return view('sur-mesure.index', [
            'types' => self::types(),
            'slots' => self::slots(),
            'showrooms' => Showroom::active()->get(),
            'budgets' => self::BUDGETS,
            'user' => $request->user(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_phone' => ['required', 'string', 'max:30', 'regex:/^[0-9 +().-]{8,}$/'],
            'customer_email' => ['nullable', 'email', 'max:150'],
            'outfit_type' => ['required', Rule::in(self::types())],
            'occasion' => ['nullable', 'string', 'max:120'],
            'preferred_date' => ['required', 'date', 'after_or_equal:today'],
            'preferred_slot' => ['required', Rule::in(self::slots())],
            'showroom_id' => ['required', Rule::exists('showrooms', 'id')->where('is_active', true)],
            'budget' => ['nullable', Rule::in(self::BUDGETS)],
            'message' => ['nullable', 'string', 'max:2000'],
            'inspiration' => ['nullable', 'image', 'max:5120'],
        ], [], [
            'customer_name' => 'nom complet',
            'customer_phone' => 'téléphone',
            'outfit_type' => 'type de tenue',
            'preferred_date' => 'date souhaitée',
            'preferred_slot' => 'créneau',
            'showroom_id' => 'maison',
            'inspiration' => 'photo d\'inspiration',
        ]);

        $showroom = Showroom::find($data['showroom_id']);
        $image = $request->file('inspiration')?->store('sur-mesure', 'public');

        $appointment = Appointment::create([
            ...collect($data)->except('inspiration')->all(),
            'user_id' => $request->user()?->id,
            'showroom_name' => $showroom->name,
            'inspiration_image' => $image,
            'status' => AppointmentStatus::Requested,
        ]);

        $request->session()->push('placed_appointments', $appointment->reference);

        return redirect()->route('appointments.confirmation', $appointment);
    }

    public function confirmation(Request $request, Appointment $appointment): View
    {
        $allowed = in_array($appointment->reference, $request->session()->get('placed_appointments', []), true)
            || ($request->user() && $appointment->user_id === $request->user()->id);
        abort_unless($allowed, 404);

        return view('sur-mesure.confirmation', ['appointment' => $appointment]);
    }

    /**
     * @return array<int, string>
     */
    public static function types(): array
    {
        return self::list('sur_mesure_types');
    }

    /**
     * @return array<int, string>
     */
    public static function slots(): array
    {
        return self::list('sur_mesure_slots');
    }

    /**
     * @return array<int, string>
     */
    private static function list(string $setting): array
    {
        return array_values(array_filter(array_map('trim', explode(',', (string) setting($setting)))));
    }
}
