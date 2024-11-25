<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\PCComponentType;

class PCComponentTypeSeeder extends Seeder {

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
      'psu',
      'cooler',
      'ssd',
      'hdd',
      'chassis',
      'fan',
      'pcie_card',
      'accessory',
      'monitor',
      'keyboard',
      'keyboard_accessory',
      'mouse',
      'speakers',
      'headset',
      'microphone',
      'interface',
      'amplifier',
      'equalizer',
      'other',
    ];

    foreach ($data as $item) {
      PCComponentType::create(['type' => $item]);
    }
  }
}
