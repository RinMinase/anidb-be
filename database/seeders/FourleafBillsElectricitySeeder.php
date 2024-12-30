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
        'estimated_kwh' => false,
      ],
      [
        'uid' => 202406,
        'kwh' => 284,
        'cost' => 3435.15,
        'estimated_kwh' => false,
      ],
      [
        'uid' => 202407,
        'kwh' => 224,
        'cost' => 2167.61,
        'estimated_kwh' => false,
      ],
      [
        'uid' => 202408,
        'kwh' => 228,
        'cost' => 3017,
        'estimated_kwh' => false,
      ],
      [
        'uid' => 202409,
        'kwh' => 300,
        'cost' => 3756.26,
        'estimated_kwh' => false,
      ],
      [
        'uid' => 202410,
        'kwh' => 240,
        'cost' => 3122.74,
        'estimated_kwh' => true,
      ],
      [
        'uid' => 202411,
        'kwh' => 353,
        'cost' => 3998.04,
        'estimated_kwh' => true,
      ],
      [
        'uid' => 202412,
        'kwh' => 281,
        'cost' => 3412.37,
        'estimated_kwh' => false,
      ],
    ];

    foreach ($data as $item) {
      $item['uuid'] = Str::uuid()->toString();

      BillsElectricity::create($item);
    }
  }
}
