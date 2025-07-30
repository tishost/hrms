<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SystemSetting;

class SystemSettingSeeder extends Seeder
{
    public function run()
    {
        SystemSetting::updateOrCreate(['key' => 'default_building_limit'], ['value' => '1']);
    }
}
