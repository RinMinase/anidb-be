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
      'pcie card',
      'accessory',
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
      PCComponentType::create(['type' => $item]);
    }
  }
}
