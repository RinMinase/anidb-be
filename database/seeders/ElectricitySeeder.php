<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Electricity;
use App\Models\ElectricityAppliance;

class ElectricitySeeder extends Seeder {
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {
    $testData = [
      [
        'datetime' => '2022-06-01 13:00:00',
        'reading' => 100,
      ],
      [
        'datetime' => '2022-06-02 13:00:00',
        'reading' => 120,
      ],
      [
        'datetime' => '2022-06-03 15:00:00',
        'reading' => 145,
      ],
      [
        'datetime' => '2022-06-04 07:00:00',
        'reading' => 160,
      ],
      [
        'datetime' => '2022-06-05 15:00:00',
        'reading' => 200,
      ],
      [
        'datetime' => '2022-06-06 15:00:00',
        'reading' => 220,
      ],
      [
        'datetime' => '2022-06-07 11:00:00',
        'reading' => 230,
      ],
      [
        'datetime' => '2022-06-12 12:00:00',
        'reading' => 320,
      ],
      [
        'datetime' => '2022-06-26 12:00:00',
        'reading' => 435,
      ],
    ];

    foreach ($testData as $item) {
      Electricity::create($item);
    }

    $testData = [
      [
        'date' => '2022-01-01',
        'name' => 'Appliance 1',
      ],
      [
        'date' => '2022-02-01',
        'name' => 'Appliance 2',
      ],
      [
        'date' => '2022-02-01',
        'name' => 'Appliance 3',
      ],
      [
        'date' => '2022-03-01',
        'name' => 'Appliance 4',
      ],
      [
        'date' => '2022-03-01',
        'name' => 'Appliance 5',
      ],
      [
        'date' => '2022-03-01',
        'name' => 'Appliance 6',
      ],
      [
        'date' => '2023-01-01',
        'name' => 'Appliance 7',
      ],
      [
        'date' => '2023-02-01',
        'name' => 'Appliance 8',
      ],
    ];

    foreach ($testData as $item) {
      ElectricityAppliance::create($item);
    }
  }
}
