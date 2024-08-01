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

    $id_fourleaf_maintenance_1 = Maintenance::where('date', $testData[0]['date'])
      ->where('description', $testData[0]['description'])
      ->where('odometer', $testData[0]['odometer'])
      ->first()
      ->id;

    $id_fourleaf_maintenance_2 = Maintenance::where('date', $testData[1]['date'])
      ->where('description', $testData[1]['description'])
      ->where('odometer', $testData[1]['odometer'])
      ->first()
      ->id;

    $testDataParts = [
      [
        'id_fourleaf_maintenance' => $id_fourleaf_maintenance_1,
        'part' => 'engine_oil',
      ], [
        'id_fourleaf_maintenance' => $id_fourleaf_maintenance_2,
        'part' => 'engine_oil',
      ],
    ];

    DB::table('fourleaf_maintenance_parts')->insert($testDataParts);
  }
}
