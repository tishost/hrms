<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Owner;
use App\Models\Tenant;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, HasRoles, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'tenant_id',
        'owner_id',
        'fcm_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function owner()
    {
        return $this->hasOne(Owner::class, 'user_id')->withTrashed();
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function subscription()
    {
        return $this->hasOneThrough(OwnerSubscription::class, Owner::class, 'user_id', 'owner_id', 'id', 'id');
    }

    public function activeSubscription()
    {
        return $this->hasOneThrough(OwnerSubscription::class, Owner::class, 'user_id', 'owner_id', 'id', 'id')
            ->whereIn('owner_subscriptions.status', ['active', 'pending_upgrade'])
            ->where('owner_subscriptions.end_date', '>=', now());
    }

    public function properties()
    {
        return $this->hasMany(Property::class, 'owner_id');
    }

    public function units()
    {
        return $this->hasManyThrough(Unit::class, Property::class, 'owner_id', 'property_id');
    }

    public function tenants()
    {
        return $this->hasMany(Tenant::class, 'owner_id');
    }

    public function billing()
    {
        return $this->hasMany(Billing::class, 'owner_id');
    }
}
