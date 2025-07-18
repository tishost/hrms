<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $fillable = [
    'first_name',
    'last_name',
    'gender',
    'mobile',
    'alt_mobile',
    'email',
    'nid_number',
    'address',
    'country',
    'total_family_member',
    'occupation',
    'company_name',
    'is_driver',
    'driver_name',
    'building_id',
    'unit_id',
    'owner_id',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class, 'building_id');
    }

    public function rents()
    {
        return $this->hasMany(\App\Models\TenantRent::class);
    }


}
