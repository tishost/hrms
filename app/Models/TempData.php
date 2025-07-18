<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TempData extends Model
{
    protected $table = 'temp_data';

    protected $fillable = [
        'user_id', 'key', 'related_id', 'data', 'expires_at'
    ];

    protected $casts = [
        'data' => 'array',
        'expires_at' => 'datetime',
    ];
}
