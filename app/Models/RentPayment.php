<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'tenant_id',
        'unit_id',
        'invoice_id',
        'amount_due',
        'amount_paid',
        'payment_method',
        'reference_number',
        'payment_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'payment_date' => 'date',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
