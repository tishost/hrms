<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'mobile',
        'email',
        'details',
        'status',
        'ticket_number',
        'admin_response',
        'responded_at',
        'responded_by',
        'admin_notes',
    ];

    protected $casts = [
        'responded_at' => 'datetime',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }
} 