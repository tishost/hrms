<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RentPayment extends Model
{
    protected $fillable = [
        'owner_id',
        'tenant_id',
        'unit_id',
        'invoice_id',
        'amount',
        'amount_due',
        'amount_paid',
        'payment_date',
        'payment_method',
        'reference_number',
        'notes',
        'status',
    ];
}
