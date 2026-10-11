<?php

namespace App\Models;

use App\Enums\AppointmentStatus;
use App\Support\Media;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

#[Fillable([
    'reference', 'user_id', 'customer_name', 'customer_phone', 'customer_email', 'outfit_type', 'occasion',
    'preferred_date', 'preferred_slot', 'showroom_id', 'showroom_name', 'budget', 'message',
    'inspiration_image', 'status', 'admin_notes',
])]
class Appointment extends Model
{
    protected function casts(): array
    {
        return [
            'preferred_date' => 'date',
            'status' => AppointmentStatus::class,
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Appointment $appointment) {
            if (blank($appointment->reference)) {
                do {
                    $reference = 'SM'.now()->format('ymd').strtoupper(Str::random(4));
                } while (static::where('reference', $reference)->exists());
                $appointment->reference = $reference;
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'reference';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function showroom(): BelongsTo
    {
        return $this->belongsTo(Showroom::class);
    }

    public function inspirationUrl(): ?string
    {
        return $this->inspiration_image ? Media::url($this->inspiration_image) : null;
    }
}
