<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
                // Get super_admin role
        $superAdminRole = Role::where('name', 'super_admin')->first();

        if (!$superAdminRole) {
            $this->command->error('Super admin role not found!');
            return;
        }

        // Create admin user
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@hrms.com'],
            [
                'name' => 'Admin User',
                'phone' => '01712345678',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ]
        );

        // Assign super_admin role
        $adminUser->assignRole($superAdminRole);

        // Create free subscription for admin
        $freePlan = \App\Models\SubscriptionPlan::where('name', 'Free')->first();

        if ($freePlan) {
            \App\Models\OwnerSubscription::create([
                'owner_id' => $adminUser->id,
                'plan_id' => $freePlan->id,
                'status' => 'active',
                'start_date' => now(),
                'end_date' => now()->addYear(),
                'auto_renew' => true,
                'sms_credits' => 0
            ]);
        }

        $this->command->info('Admin user created successfully!');
        $this->command->info('Email: admin@hrms.com');
        $this->command->info('Password: admin123');
    }
}
