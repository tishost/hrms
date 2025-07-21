<?php

namespace App\Models;
use App\Models\Owner;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'owner_id',
        'name',
        'property_type',
        'address',
        'city',
        'state',
        'zip_code',
        'country',
        'total_units',
        'description',
        'status',
    ];

    protected $casts = [
        'total_units' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function units()
    {
        return $this->hasMany(Unit::class);
    }

    public function getFullAddressAttribute()
    {
        return "{$this->address}, {$this->city}, {$this->state} {$this->zip_code}, {$this->country}";
    }

    public function getOccupiedUnitsCountAttribute()
    {
        return $this->units()->where('status', 'occupied')->count();
    }

    public function getVacantUnitsCountAttribute()
    {
        return $this->units()->where('status', 'vacant')->count();
    }

    public function getOccupancyRateAttribute()
    {
        if ($this->total_units == 0) {
            return 0;
        }
        return round(($this->occupied_units_count / $this->total_units) * 100, 2);
    }
}
