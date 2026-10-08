<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['full_name', 'phone', 'transfer_method', 'email', 'message', 'status'])]
class AidRequest extends Model
{
    public const STATUSES = [
        'nouvelle' => 'Nouvelle',
        'en_cours' => 'En cours d\'étude',
        'acceptee' => 'Acceptée',
        'refusee' => 'Refusée',
    ];
}
