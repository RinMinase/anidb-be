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
      'cpu' => 'CPU',
      'ram' => 'RAM',
      'gpu' => 'GPU',
      'motherboard' => 'Motherboard',
      'psu' => 'PSU',
      'cooler' => 'Cooler',
      'ssd' => 'SSD',
      'hdd' => 'HDD',
      'chassis' => 'Chassis',
      'fan' => 'Fan',
      'pcie_card' => 'PCIe Card',
      'accessory' => 'Accessory',
      'monitor' => 'Monitor',
      'keyboard' => 'Keyboard',
      'keyboard_accessory' => 'Keyboard Accesory',
      'mouse' => 'Mouse',
      'speakers' => 'Speakers',
      'headset' => 'Headset',
      'microphone' => 'Microphone',
      'interface' => 'Interface',
      'amplifier' => 'Amplifier',
      'equalizer' => 'Equalizer',
      'other' => 'Other',
    ];

    foreach ($data as $type => $name) {
      PCComponentType::create([
        'type' => $type,
        'name' => $name,
      ]);
    }
  }
}
