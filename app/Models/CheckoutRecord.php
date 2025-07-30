<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckoutRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'unit_id',
        'owner_id',
        'check_out_date',
        'security_deposit',
        'deposit_returned',
        'outstanding_dues',
        'utility_bills',
        'cleaning_charges',
        'other_charges',
        'final_settlement_amount',
        'settlement_status',
        'check_out_reason',
        'handover_date',
        'handover_condition',
        'notes',
        'property_image',
        'payment_reference',
        'payment_method',
    ];

    protected $casts = [
        'check_out_date' => 'date',
        'handover_date' => 'date',
        'security_deposit' => 'decimal:2',
        'deposit_returned' => 'decimal:2',
        'outstanding_dues' => 'decimal:2',
        'utility_bills' => 'decimal:2',
        'cleaning_charges' => 'decimal:2',
        'other_charges' => 'decimal:2',
        'final_settlement_amount' => 'decimal:2',
    ];

    // Relationships
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
