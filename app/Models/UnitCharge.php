<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class UnitCharge extends Model
{
    protected $fillable = [
    'unit_id',
    'label',
    'amount',
];

}
