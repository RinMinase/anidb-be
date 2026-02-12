<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Fourleaf\Models\MaintenancePart;
use App\Fourleaf\Models\MaintenanceType;

class FourleafMaintenanceTypeSeeder extends Seeder {
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {
    $types = [
      [
        'type' => 'ac_coolant',
        'label' => 'AC Coolant',
        'km' => null,
        'year' => 3
      ],
      [
        'type' => 'battery',
        'label' => 'Battery',
        'km' => null,
        'year' => 3
      ],
      [
        'type' => 'brake_fluid',
        'label' => 'Brake Fluid',
        'km' => 50_000,
        'year' => 2
      ],
      [
        'type' => 'brake_sanding',
        'label' => 'Brake Sanding',
        'km' => 10_000,
        'year' => 1
      ],
      [
        'type' => 'engine_oil',
        'label' => 'Engine Oil',
        'km' => 8_000,
        'year' => 1
      ],
      [
        'type' => 'power_steering_fluid',
        'label' => 'Power Steering Fluid',
        'km' => 100_000,
        'year' => 5
      ],
      [
        'type' => 'radiator_fluid',
        'label' => 'Radiator Fluid',
        'km' => 50_000,
        'year' => 3
      ],
      [
        'type' => 'spark_plugs',
        'label' => 'Spark Plugs',
        'km' => 50_000,
        'year' => null
      ],
      [
        'type' => 'tires_rotation',
        'label' => 'Tires Rotation',
        'km' => 20_000,
        'year' => null
      ],
      [
        'type' => 'tires_change',
        'label' => 'Tires Change',
        'km' => null,
        'year' => 5
      ],
      [
        'type' => 'transmission',
        'label' => 'Transmission Fluid',
        'km' => 50_000,
        'year' => 2
      ],
      [
        'type' => 'engine_wash',
        'label' => 'Engine Wash',
        'km' => null,
        'year' => 2
      ],
      [
        'type' => 'wiper_change',
        'label' => 'Wiper Change',
        'km' => null,
        'year' => 1,
      ],
      [
        'type' => 'lights_change',
        'label' => 'Lights Change',
        'km' => null,
        'year' => null,
      ],
      [
        'type' => 'others',
        'label' => 'Others',
        'km' => null,
        'year' => null
      ],
    ];

    foreach ($types as $key => $item) {
      MaintenanceType::create([
        'id' => $key + 1,
        'type' => $item['type'],
        'label' => $item['label'],
        'km' => $item['km'],
        'year' => $item['year'],
      ]);
    }

    MaintenancePart::refreshAutoIncrements();
  }
}
