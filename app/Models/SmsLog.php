<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone',
        'message',
        'status',
        'provider',
        'response',
        'error_message',
        'sent_at',
        'owner_id',
        'user_id'
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'response' => 'array'
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByProvider($query, $provider)
    {
        return $query->where('provider', $provider);
    }
} 