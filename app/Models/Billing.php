<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Billing extends Model
{
    use HasFactory;

    protected $table = 'billing';

    protected $fillable = [
        'owner_id',
        'subscription_id',
        'amount',
        'status',
        'due_date',
        'paid_date',
        'invoice_number',
        'payment_method',
        'transaction_id',
        'payment_method_id',
        'transaction_fee',
        'net_amount'
    ];

    protected $casts = [
        'due_date' => 'date',
        'paid_date' => 'date',
        'transaction_fee' => 'decimal:2',
        'net_amount' => 'decimal:2'
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_id');
    }

    public function subscription()
    {
        return $this->belongsTo(OwnerSubscription::class, 'subscription_id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function isPaid()
    {
        return $this->status === 'paid';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isOverdue()
    {
        return $this->isPending() && $this->due_date->isPast();
    }

    public function getFormattedAmountAttribute()
    {
        return '৳' . number_format($this->amount, 2);
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'warning',
            'paid' => 'success',
            'failed' => 'danger',
            'refunded' => 'info'
        ];

        return $badges[$this->status] ?? 'secondary';
    }
}
