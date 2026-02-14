<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\CarMaintenance;
use App\Models\CarMaintenancePart;
use App\Models\CarMaintenanceType;

class CarMaintenanceSeeder extends Seeder {
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {
    $data = [
      [
        'date' => '2023-06-01',
        'description' => 'PMS Labor',
        'odometer' => 1400,
        'parts' => ['engine_oil'],
      ],
      [
        'date' => '2024-08-29',
        'description' => 'PMS Labor',
        'odometer' => 6600,
        'parts' => ['engine_oil'],
      ],
      [
        'date' => '2025-02-01',
        'description' => 'PMS Labor',
        'odometer' => 13979,
        'parts' => ['engine_oil', 'brake_sanding'],
      ],
      [
        'date' => '2026-02-03',
        'description' => 'Manual Battery Change',
        'odometer' => 19940,
        'parts' => ['battery'],
      ],
      [
        'date' => '2026-02-08',
        'description' => 'PMS Labor',
        'odometer' => 19998,
        'parts' => ['engine_oil', 'brake_sanding', 'engine_wash', 'spark_plugs'],
      ],
    ];

    $maintenance_types = CarMaintenanceType::all()->toArray();
    $indexed_types = array_column($maintenance_types, null, 'type');

    foreach ($data as $item) {
      $maintenance_id = CarMaintenance::insertGetId([
        'date' => $item['date'],
        'description' => $item['description'],
        'odometer' => $item['odometer'],
      ]);

      $for_maintenance_part = [];
      foreach ($item['parts'] as $part) {
        $maintenace_type_id = $indexed_types[$part]['id'] ?? null;
        array_push($for_maintenance_part, [
          'id_car_maintenance' => $maintenance_id,
          'id_car_maintenance_type' => $maintenace_type_id,
        ]);
      }

      CarMaintenancePart::insert($for_maintenance_part);
    }

    CarMaintenance::refreshAutoIncrements();
    CarMaintenancePart::refreshAutoIncrements();
  }
}
