<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantLedger extends Model
{
    protected $fillable = [
        'tenant_id',
        'unit_id',
        'owner_id',
        'transaction_type',
        'reference_type',
        'reference_id',
        'invoice_number',
        'debit_amount',
        'credit_amount',
        'balance',
        'description',
        'notes',
        'transaction_date',
        'due_date',
        'payment_method',
        'payment_reference',
        'payment_status',
        'created_by',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'due_date' => 'date',
        'debit_amount' => 'decimal:2',
        'credit_amount' => 'decimal:2',
        'balance' => 'decimal:2',
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
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
