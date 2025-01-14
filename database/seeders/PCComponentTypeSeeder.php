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
    $data_system = [
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
    ];

    $data_peripherals = [
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
      'audio_related' => 'Audio Related',
      'controller' => 'Controller',
      'other' => 'Other',
    ];

    foreach ($data_system as $type => $name) {
      PCComponentType::create([
        'type' => $type,
        'name' => $name,
        'is_peripheral' => false,
      ]);
    }

    foreach ($data_peripherals as $type => $name) {
      PCComponentType::create([
        'type' => $type,
        'name' => $name,
        'is_peripheral' => true,
      ]);
    }
  }
}
