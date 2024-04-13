<?php

namespace Database\Seeders;

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
        'part' => 'PMS Labor',
        'odometer' => 1400,
      ], [
        'date' => '2023-01-01',
        'part' => 'PMS Labor',
        'odometer' => 6600,
      ]
    ];

    foreach ($testData as $item) {
      Maintenance::create($item);
    }
  }
}
