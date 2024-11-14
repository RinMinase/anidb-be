<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\PCSetupInventoryType;

class PCSetupInventoryTypeSeeder extends Seeder {

  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {
    $data = [
      'cpu',
      'ram',
      'gpu',
      'motherboard',
      'power supply',
      'cooler',
      'ssd',
      'hdd',
      'chassis',
      'fans',
      'pcie cards',
      'accessories',
      'monitor',
      'keyboard',
      'mouse',
      'speakers',
      'headset',
      'microphone',
      'audio related',
      'other',
    ];

    foreach ($data as $item) {
      PCSetupInventoryType::create(['inventory_type' => $item]);
    }
  }
}
