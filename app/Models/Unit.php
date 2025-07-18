<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = [
    'property_id',
    'name',
    'floor',
    'rent',

    // add any other fields you want to mass-assign
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

}
