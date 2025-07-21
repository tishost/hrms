<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChargeSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('charges')->insert([
            ['label' => 'Gas Bill', 'amount' => 500],
            ['label' => 'Water Bill', 'amount' => 300],
            ['label' => 'Electricity Bill', 'amount' => 1000],
            ['label' => 'Service Charge', 'amount' => 200],
            ['label' => 'Security', 'amount' => 150],
            ['label' => 'Left Fees', 'amount' => 150],
        ]);
    }
}
