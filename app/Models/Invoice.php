<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'owner_id',
        'tenant_id',
        'unit_id',
        'type',
        'amount',
        'status',
        'issue_date',
        'due_date',
        'paid_date',
        'payment_method',
        'notes',
        'breakdown',
        'rent_month',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }
}
