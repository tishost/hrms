<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\OwnerSubscription;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class TestOwnerSeeder extends Seeder
{
    public function run()
    {
        // Get owner role
        $ownerRole = Role::where('name', 'owner')->first();

        if (!$ownerRole) {
            $this->command->error('Owner role not found!');
            return;
        }

        // Create test owner
        $owner = User::firstOrCreate(
            ['email' => 'owner@test.com'],
            [
                'name' => 'Test Owner',
                'phone' => '01712345679',
                'password' => Hash::make('owner123'),
                'email_verified_at' => now(),
            ]
        );

        // Assign owner role
        $owner->assignRole($ownerRole);

        // Get free plan
        $freePlan = SubscriptionPlan::where('name', 'Free')->first();

        if ($freePlan) {
            // Create subscription for owner
            OwnerSubscription::firstOrCreate(
                ['owner_id' => $owner->id],
                [
                    'plan_id' => $freePlan->id,
                    'status' => 'active',
                    'start_date' => now(),
                    'end_date' => now()->addYear(),
                    'auto_renew' => true,
                    'sms_credits' => 0
                ]
            );
        }

        $this->command->info('Test owner created successfully!');
        $this->command->info('Email: owner@test.com');
        $this->command->info('Password: owner123');
    }
}
