<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            if (empty($invoice->invoice_number)) {
                $invoice->invoice_number = 'INV-' . date('Y') . '-' . str_pad(static::whereYear('created_at', date('Y'))->count() + 1, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    protected $fillable = [
        'invoice_number',
        'owner_id',
        'tenant_id',
        'unit_id',
        'type',
        'invoice_type',
        'amount',
        'paid_amount',
        'status',
        'issue_date',
        'due_date',
        'paid_date',
        'payment_method',
        'notes',
        'description',
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

    public function property()
    {
        return $this->belongsTo(Property::class, 'unit_id', 'id');
    }
}
