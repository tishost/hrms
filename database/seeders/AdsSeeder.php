<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Ad;
use Carbon\Carbon;

class AdsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ads = [
            [
                'title' => 'Welcome to HRMS',
                'description' => 'Discover the power of our comprehensive property management system',
                'image_path' => 'https://via.placeholder.com/400x200/007bff/ffffff?text=Welcome+to+HRMS',
                'url' => 'https://barimanager.com',
                'is_active' => true,
                'show_on_owner_dashboard' => true,
                'show_on_tenant_dashboard' => true,
                'start_date' => Carbon::now()->subDays(30),
                'end_date' => Carbon::now()->addDays(365),
                'display_order' => 1,
                'clicks_count' => 45,
                'impressions_count' => 1200,
            ],
            [
                'title' => 'Premium Features Available',
                'description' => 'Upgrade your plan to unlock advanced features and analytics',
                'image_path' => 'https://via.placeholder.com/400x200/28a745/ffffff?text=Premium+Features',
                'url' => 'https://barimanager.com/plans',
                'is_active' => true,
                'show_on_owner_dashboard' => true,
                'show_on_tenant_dashboard' => false,
                'start_date' => Carbon::now()->subDays(15),
                'end_date' => Carbon::now()->addDays(180),
                'display_order' => 2,
                'clicks_count' => 23,
                'impressions_count' => 800,
            ],
            [
                'title' => '24/7 Support',
                'description' => 'Get help anytime with our round-the-clock customer support',
                'image_path' => 'https://via.placeholder.com/400x200/17a2b8/ffffff?text=24%2F7+Support',
                'url' => 'https://barimanager.com/support',
                'is_active' => true,
                'show_on_owner_dashboard' => true,
                'show_on_tenant_dashboard' => true,
                'start_date' => Carbon::now()->subDays(7),
                'end_date' => Carbon::now()->addDays(90),
                'display_order' => 3,
                'clicks_count' => 67,
                'impressions_count' => 1500,
            ],
            [
                'title' => 'Mobile App Update',
                'description' => 'New features and improvements in our latest mobile app version',
                'image_path' => 'https://via.placeholder.com/400x200/ffc107/000000?text=Mobile+App+Update',
                'url' => null,
                'is_active' => true,
                'show_on_owner_dashboard' => false,
                'show_on_tenant_dashboard' => true,
                'start_date' => Carbon::now()->subDays(3),
                'end_date' => Carbon::now()->addDays(60),
                'display_order' => 4,
                'clicks_count' => 12,
                'impressions_count' => 600,
            ],
            [
                'title' => 'Holiday Special Offer',
                'description' => 'Limited time discount on premium plans - 20% off!',
                'image_path' => 'https://via.placeholder.com/400x200/dc3545/ffffff?text=Holiday+Special+Offer',
                'url' => 'https://barimanager.com/holiday-offer',
                'is_active' => true,
                'show_on_owner_dashboard' => true,
                'show_on_tenant_dashboard' => false,
                'start_date' => Carbon::now()->subDays(1),
                'end_date' => Carbon::now()->addDays(30),
                'display_order' => 5,
                'clicks_count' => 89,
                'impressions_count' => 2000,
            ],
        ];

        foreach ($ads as $adData) {
            Ad::create($adData);
        }

        $this->command->info('Sample ads seeded successfully!');
    }
}
