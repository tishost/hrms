<?php

namespace App\Models;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
class Owner extends Authenticatable
{
    //
protected $fillable = [
    'name',
    'email',
    'phone',
    'address',
    'country',
    'owner_uid',
    'total_properties',
    'total_tenants',
    'user_id', // Added user_id to fillable attributes
];
 


protected static function booted()
{
    static::creating(function ($owner) {
        $owner->owner_uid = 'OWN-' . strtoupper(Str::random(8));
    });
}

public function user()
{
    return $this->belongsTo(User::class);
}

public function properties() {
    return $this->hasMany(Property::class, 'owner_id');
}
// In Owner.php
public function ownedProperties() {
    return $this->hasMany(Property::class)->with('units');
}

}
