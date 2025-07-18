<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */


public function run()
{
    $user = User::find(1); // Or use email match
    if ($user) {
        $user->assignRole('super_admin');
    }
}
}
