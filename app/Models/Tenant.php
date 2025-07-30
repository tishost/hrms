<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use SoftDeletes;

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
        'status',
        'check_in_date',
        'check_out_date',
        'security_deposit',
        'cleaning_charges',
        'other_charges',
        'check_out_reason',
        'handover_date',
        'handover_condition',
        'remarks',
        'family_types',
        'child_qty',
        'city',
        'state',
        'zip',
        'college_university',
        'business_name',
        'frequency',
        'nid_picture',
        'nid_front_picture',
        'nid_back_picture',
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'handover_date' => 'date',
        'security_deposit' => 'decimal:2',
        'cleaning_charges' => 'decimal:2',
        'other_charges' => 'decimal:2',
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

    public function checkoutRecords()
    {
        return $this->hasMany(CheckoutRecord::class);
    }

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getPhoneAttribute()
    {
        return $this->mobile;
    }

    public function getEmailAttribute()
    {
        return $this->email ?? 'N/A';
    }
}
