<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantRent extends Model
{
    protected $fillable = [
    'tenant_id',
    'unit_id',
    'start_month',
    'due_day',
    'advance_amount',
    'frequency',
    'discount',
    'remarks',
    'fee_labels', // if you're storing fee labels as JSON or serialized
    'fee_amounts' // same for amounts
    ];
}
