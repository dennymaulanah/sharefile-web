<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::set('sharefile_password', 'budidaya123');
        Setting::set('sharefile_password_enabled', '1');
    }
}
