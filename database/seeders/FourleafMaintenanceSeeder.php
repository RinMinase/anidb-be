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
    $testData = [
      [
        'date' => '2023-06-01',
        'description' => 'PMS Labor',
        'odometer' => 1400,
      ], [
        'date' => '2024-01-01',
        'description' => 'PMS Labor',
        'odometer' => 6600,
      ]
    ];

    foreach ($testData as $item) {
      Maintenance::create($item);
    }

    $testDataParts = [
      [
        'id_fourleaf_maintenance' => 1,
        'part' => 'engine_oil',
      ], [
        'id_fourleaf_maintenance' => 2,
        'part' => 'engine_oil',
      ],
    ];

    DB::table('fourleaf_maintenance_parts')->insert($testDataParts);
  }
}
