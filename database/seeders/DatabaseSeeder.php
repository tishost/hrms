<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\SystemSettingSeeder;
use Database\Seeders\ChargeSeeder;
use Database\Seeders\SuperAdminSeeder;
use Database\Seeders\SubscriptionPlanSeeder;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\PaymentMethodSeeder;
use Database\Seeders\BkashPaymentMethodSeeder;
use Database\Seeders\AdsSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call([
            RoleSeeder::class,
            SuperAdminSeeder::class,
            SystemSettingSeeder::class,
            ChargeSeeder::class,
            SubscriptionPlanSeeder::class,
            AdminUserSeeder::class,
            PaymentMethodSeeder::class,
            BkashPaymentMethodSeeder::class,
            AdsSeeder::class,
        ]);
    }
}
