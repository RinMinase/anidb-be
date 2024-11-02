<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

use App\Fourleaf\Models\BillsElectricity;

class FourleafBillsElectricitySeeder extends Seeder {
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {
    $data = [
      [
        'uid' => 202405,
        'kwh' => 222,
        'cost' => 2528.91,
      ],
      [
        'uid' => 202406,
        'kwh' => 284,
        'cost' => 3435.15,
      ],
      [
        'uid' => 202407,
        'kwh' => 224,
        'cost' => 2167.61,
      ],
      [
        'uid' => 202408,
        'kwh' => 228,
        'cost' => 3017,
      ],
      [
        'uid' => 202409,
        'kwh' => 300,
        'cost' => 3756.26,
      ],
    ];

    foreach ($data as $item) {
      $item['uuid'] = Str::uuid()->toString();

      BillsElectricity::create($item);
    }
  }
}
