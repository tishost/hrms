<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Owner;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin User
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@hrms.com',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now(),
        ]);

        // Create Super Admin Owner Profile
        Owner::create([
            'user_id' => $superAdmin->id,
            'name' => 'Super Admin',
            'email' => 'admin@hrms.com',
            'phone' => '01700000000',
            'address' => 'Dhaka, Bangladesh',
            'country' => 'Bangladesh',
            'is_super_admin' => true,
            'status' => 'active',
        ]);

        $this->command->info('Super Admin created successfully!');
        $this->command->info('Email: admin@hrms.com');
        $this->command->info('Password: admin123');
    }
}
