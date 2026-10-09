<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\PressingStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable([
    'reference', 'user_id', 'customer_name', 'customer_phone', 'customer_email', 'mode',
    'pressing_zone_id', 'zone_name', 'address', 'showroom_id', 'showroom_name', 'pickup_date', 'pickup_slot',
    'express', 'subtotal', 'express_fee', 'collection_fee', 'total',
    'payment_method', 'payment_status', 'status', 'notes', 'admin_notes',
])]
class PressingOrder extends Model
{
    public const MODES = [
        'collecte' => 'Collecte à domicile',
        'depot' => 'Dépôt en showroom',
    ];

    protected function casts(): array
    {
        return [
            'status' => PressingStatus::class,
            'payment_status' => PaymentStatus::class,
            'payment_method' => PaymentMethod::class,
            'pickup_date' => 'date',
            'express' => 'boolean',
            'subtotal' => 'integer',
            'express_fee' => 'integer',
            'collection_fee' => 'integer',
            'total' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (PressingOrder $order) {
            if (blank($order->reference)) {
                do {
                    $reference = 'PR'.now()->format('ymd').strtoupper(Str::random(4));
                } while (static::where('reference', $reference)->exists());
                $order->reference = $reference;
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'reference';
    }

    public function items(): HasMany
    {
        return $this->hasMany(PressingOrderItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(PressingZone::class, 'pressing_zone_id');
    }

    public function showroom(): BelongsTo
    {
        return $this->belongsTo(Showroom::class);
    }

    public function modeLabel(): string
    {
        return self::MODES[$this->mode] ?? $this->mode;
    }

    /**
     * Where the clothes are picked up: zone + address, or the showroom.
     */
    public function placeLabel(): string
    {
        return $this->mode === 'depot'
            ? (string) $this->showroom_name
            : trim($this->zone_name.' – '.$this->address, ' –');
    }
}
