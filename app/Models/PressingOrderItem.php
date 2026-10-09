<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['pressing_order_id', 'pressing_service_id', 'service_name', 'quantity', 'unit_price', 'total'])]
class PressingOrderItem extends Model
{
    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'integer',
            'total' => 'integer',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(PressingOrder::class, 'pressing_order_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(PressingService::class, 'pressing_service_id');
    }
}
