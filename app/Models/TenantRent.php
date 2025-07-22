<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantRent extends Model
{
    protected $fillable = [
        'tenant_id',
        'unit_id',
        'owner_id',
        'start_month',
        'due_day',
        'advance_amount',
        'frequency',
        'discount',
        'remarks',
        'fee_labels',
        'fee_amounts'
    ];
}
