<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\AppSetting;

class AppSettingSeeder extends Seeder {
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {
    $data = [
      [
        'key' => 'kwh_price',
        'value' => 15,
      ],
    ];

    foreach ($data as $item) {
      AppSetting::create($item);
    }
  }
}
