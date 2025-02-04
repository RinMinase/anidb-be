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
      ['type' => 'ac_coolant', 'label' => 'AC Coolant'],
      ['type' => 'battery', 'label' => 'Battery'],
      ['type' => 'brake_fluid', 'label' => 'Brake Fluid'],
      ['type' => 'brake_sanding', 'label' => 'Brake Sanding'],
      ['type' => 'engine_oil', 'label' => 'Engine Oil'],
      ['type' => 'power_steering_fluid', 'label' => 'Power Steering Fluid'],
      ['type' => 'radiator_fluid', 'label' => 'Radiator Fluid'],
      ['type' => 'spark_plugs', 'label' => 'Spark Plugs'],
      ['type' => 'tires_rotation', 'label' => 'Tires Rotation'],
      ['type' => 'tires_change', 'label' => 'Tires Change'],
      ['type' => 'transmission', 'label' => 'Transmission Fluid'],
      ['type' => 'others', 'label' => 'Others'],
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
