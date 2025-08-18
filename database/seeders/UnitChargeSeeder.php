<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitChargeSeeder extends Seeder
{
    public function run(): void
    {
        // Get all units that have tenants
        $units = DB::table('units')
            ->whereNotNull('tenant_id')
            ->where('deleted_at', null)
            ->get();

        foreach ($units as $unit) {
            // Add common charges for each unit
            DB::table('unit_charges')->insert([
                [
                    'unit_id' => $unit->id,
                    'label' => 'Electricity',
                    'amount' => 200.00,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'unit_id' => $unit->id,
                    'label' => 'Water',
                    'amount' => 100.00,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'unit_id' => $unit->id,
                    'label' => 'Gas',
                    'amount' => 150.00,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'unit_id' => $unit->id,
                    'label' => 'Service Charge',
                    'amount' => 100.00,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }
}
