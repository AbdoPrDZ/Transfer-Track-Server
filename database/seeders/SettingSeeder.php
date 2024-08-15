<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder {

  use WithoutModelEvents;

  /**
   * Run the database seeds.
   */
  public function run(): void {
    Setting::firstOrCreate([
      'name' => 'email_resend_duration',
      'value' => 180,
    ]);
    Setting::firstOrCreate([
      'name' => 'sms_resend_duration',
      'value' => 180,
    ]);
  }

}
