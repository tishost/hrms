<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paymentMethods = [
            [
                'name' => 'bKash',
                'code' => 'bkash',
                'description' => 'Mobile banking service by bKash Limited',
                'transaction_fee' => 1.5,
                'is_active' => true,
                'settings' => [
                    'gateway_url' => 'https://www.bkash.com/payment',
                    'merchant_id' => 'BKASH001',
                    'api_key' => 'bkash_api_key_here'
                ]
            ],
            [
                'name' => 'Nagad',
                'code' => 'nagad',
                'description' => 'Digital financial service by Bangladesh Post Office',
                'transaction_fee' => 1.0,
                'is_active' => true,
                'settings' => [
                    'gateway_url' => 'https://nagad.com.bd/payment',
                    'merchant_id' => 'NAGAD001',
                    'api_key' => 'nagad_api_key_here'
                ]
            ],
            [
                'name' => 'Rocket',
                'code' => 'rocket',
                'description' => 'Mobile banking service by Dutch-Bangla Bank',
                'transaction_fee' => 1.8,
                'is_active' => true,
                'settings' => [
                    'gateway_url' => 'https://rocket.com.bd/payment',
                    'merchant_id' => 'ROCKET001',
                    'api_key' => 'rocket_api_key_here'
                ]
            ],
            [
                'name' => 'Bank Transfer',
                'code' => 'bank_transfer',
                'description' => 'Direct bank transfer to our account',
                'transaction_fee' => 0.0,
                'is_active' => true,
                'settings' => [
                    'bank_name' => 'Demo Bank',
                    'account_number' => '1234567890',
                    'branch' => 'Dhaka Main Branch'
                ]
            ],
            [
                'name' => 'Cash Payment',
                'code' => 'cash',
                'description' => 'Pay at our office location',
                'transaction_fee' => 0.0,
                'is_active' => true,
                'settings' => [
                    'office_address' => 'Dhaka, Bangladesh',
                    'office_hours' => '9:00 AM - 5:00 PM'
                ]
            ]
        ];

        foreach ($paymentMethods as $method) {
            PaymentMethod::updateOrCreate(
                ['code' => $method['code']],
                $method
            );
        }
    }
}
