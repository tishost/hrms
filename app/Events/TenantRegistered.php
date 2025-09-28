<?php

namespace App\Events;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TenantRegistered
{
    use Dispatchable, SerializesModels;

    public $tenant;
    public $user;
    public $property;
    public $unit;

    /**
     * Create a new event instance.
     */
    public function __construct(Tenant $tenant, User $user, $property = null, $unit = null)
    {
        $this->tenant = $tenant;
        $this->user = $user;
        $this->property = $property;
        $this->unit = $unit;
    }
}

