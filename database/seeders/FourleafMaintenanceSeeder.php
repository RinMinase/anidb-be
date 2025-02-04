<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

use App\Fourleaf\Models\Maintenance;

class FourleafMaintenanceSeeder extends Seeder {
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

    $testDataParts = [
      [
        'id_fourleaf_maintenance' => $id_fourleaf_maintenance_1,
        'part' => 'engine_oil',
      ],
      [
        'id_fourleaf_maintenance' => $id_fourleaf_maintenance_2,
        'part' => 'engine_oil',
      ],
      [
        'id_fourleaf_maintenance' => $id_fourleaf_maintenance_3,
        'part' => 'engine_oil',
      ],
      [
        'id_fourleaf_maintenance' => $id_fourleaf_maintenance_3,
        'part' => 'brake_sanding',
      ],
    ];

    DB::table('fourleaf_maintenance_parts')->insert($testDataParts);
  }
}
