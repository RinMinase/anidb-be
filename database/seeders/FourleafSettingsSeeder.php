<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Fourleaf\Models\Settings;

class FourleafSettingsSeeder extends Seeder {
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
      Settings::create($item);
    }
  }
}
