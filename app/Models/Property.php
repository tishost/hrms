<?php

namespace App\Models;
use App\Models\Owner;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
        protected $fillable = [
        'owner_id',
        'name',
        'type',
        'address',
        'country',
        'unit_limit',
        'is_active',
        'features',
];

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function units()
    {
        return $this->hasMany(Unit::class);
    }


}
