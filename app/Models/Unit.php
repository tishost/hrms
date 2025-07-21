<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'property_id',
        'name',
        'floor',
        'rent',
        'status',
        'tenant_id',
        'owner_id',
    ];

    protected $casts = [
        'rent' => 'decimal:2',
    ];

    public function charges()
    {
        return $this->hasMany(\App\Models\UnitCharge::class);
    }

    public function tenant()
    {
        return $this->belongsTo(\App\Models\Tenant::class, 'tenant_id');
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_id');
    }

    public function checkoutRecords()
    {
        return $this->hasMany(CheckoutRecord::class);
    }
}
