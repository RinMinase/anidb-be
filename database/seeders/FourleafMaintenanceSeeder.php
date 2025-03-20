<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

use App\Fourleaf\Models\Maintenance;
use App\Fourleaf\Models\MaintenancePart;
use App\Fourleaf\Models\MaintenanceType;

class FourleafMaintenanceSeeder extends Seeder {
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
      ]);
    }

    $data = [
      [
        'date' => '2023-06-01',
        'description' => 'PMS Labor',
        'odometer' => 1400,
      ],
      [
        'date' => '2024-01-01',
        'description' => 'PMS Labor',
        'odometer' => 6600,
      ],
      [
        'date' => '2025-02-01',
        'description' => 'PMS Labor',
        'odometer' => 13979,
      ]
    ];

    foreach ($data as $item) {
      Maintenance::create($item);
    }

    $id_fourleaf_maintenance_1 = Maintenance::where('date', $data[0]['date'])
      ->where('odometer', $data[0]['odometer'])
      ->first()
      ->id;

    $id_fourleaf_maintenance_2 = Maintenance::where('date', $data[1]['date'])
      ->where('odometer', $data[1]['odometer'])
      ->first()
      ->id;

    $id_fourleaf_maintenance_3 = Maintenance::where('date', $data[2]['date'])
      ->where('odometer', $data[2]['odometer'])
      ->first()
      ->id;

    $id_fourleaf_maintenance_part_1 = MaintenanceType::where('type', 'engine_oil')
      ->first()
      ->id;

    $id_fourleaf_maintenance_part_2 = MaintenanceType::where('type', 'brake_sanding')
      ->first()
      ->id;

    $parts = [
      [
        'id_fourleaf_maintenance' => $id_fourleaf_maintenance_1,
        'id_fourleaf_maintenance_type' => $id_fourleaf_maintenance_part_1,
      ],
      [
        'id_fourleaf_maintenance' => $id_fourleaf_maintenance_2,
        'id_fourleaf_maintenance_type' => $id_fourleaf_maintenance_part_1,
      ],
      [
        'id_fourleaf_maintenance' => $id_fourleaf_maintenance_3,
        'id_fourleaf_maintenance_type' => $id_fourleaf_maintenance_part_1,
      ],
      [
        'id_fourleaf_maintenance' => $id_fourleaf_maintenance_3,
        'id_fourleaf_maintenance_type' => $id_fourleaf_maintenance_part_2,
      ],
    ];

    DB::table('fourleaf_maintenance_parts')->insert($parts);
    MaintenancePart::refreshAutoIncrements();
  }
}
