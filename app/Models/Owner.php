<?php

namespace App\Models;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
class Owner extends Model
{
    use SoftDeletes;
    //
protected $fillable = [
    'name',
    'email',
    'phone',
    'address',
    'country',
    'gender',
    'profile_pic',
    'owner_uid',
    'total_properties',
    'total_tenants',
    'user_id',
    'is_super_admin',
    'status',
    'phone_verified',
];

protected $casts = [
    'is_super_admin' => 'boolean',
];


protected static function booted()
{
    static::creating(function ($owner) {
        $owner->owner_uid = 'OWN-' . strtoupper(Str::random(8));
    });
}

public function user()
{
    return $this->belongsTo(User::class, 'user_id');
}

public function properties() {
    return $this->hasMany(Property::class, 'owner_id');
}
// In Owner.php
public function ownedProperties() {
    return $this->hasMany(Property::class)->with('units');
}

public function subscriptions()
{
    return $this->hasMany(OwnerSubscription::class, 'owner_id');
}

public function billing()
{
    return $this->hasMany(Billing::class, 'owner_id');
}

}
