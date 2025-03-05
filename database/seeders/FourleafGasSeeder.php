<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Fourleaf\Models\Gas;

class FourleafGasSeeder extends Seeder {
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {
    $data = [
      [
        'date' => '2023-04-29',
        'from_bars' => 8,
        'to_bars' => 8,
        'odometer' => 165,
        'price_per_liter' => null,
        'liters_filled' => null,
      ],
      [
        'date' => '2023-05-03',
        'from_bars' => 4,
        'to_bars' => 8,
        'odometer' => 239,
        'price_per_liter' => 62.85,
        'liters_filled' => 13.93,
      ],
      [
        'date' => '2023-05-10',
        'from_bars' => 1,
        'to_bars' => 8,
        'odometer' => 386,
        'price_per_liter' => 60.65,
        'liters_filled' => 18.928,
      ],
      [
        'date' => '2023-05-19',
        'from_bars' => 2,
        'to_bars' => 8,
        'odometer' => 507,
        'price_per_liter' => 61,
        'liters_filled' => 18.739,
      ],
      [
        'date' => '2023-05-27',
        'from_bars' => 2,
        'to_bars' => 8,
        'odometer' => 662,
        'price_per_liter' => 61.8,
        'liters_filled' => 15.86,
      ],
      [
        'date' => '2023-06-06',
        'from_bars' => 0,
        'to_bars' => 2,
        'odometer' => 913,
        'price_per_liter' => 62.3,
        'liters_filled' => 5,
      ],
      [
        'date' => '2023-06-09',
        'from_bars' => 1,
        'to_bars' => 2,
        'odometer' => 958,
        'price_per_liter' => 62.3,
        'liters_filled' => 5,
      ],
      [
        'date' => '2023-06-10',
        'from_bars' => 1,
        'to_bars' => 8,
        'odometer' => 1030,
        'price_per_liter' => 62.3,
        'liters_filled' => 21.829,
      ],
      [
        'date' => '2023-06-12',
        'from_bars' => 0,
        'to_bars' => 2,
        'odometer' => 1299,
        'price_per_liter' => 62.3,
        'liters_filled' => 5,
      ],
      [
        'date' => '2023-06-16',
        'from_bars' => 1,
        'to_bars' => 8,
        'odometer' => 1340,
        'price_per_liter' => 63.5,
        'liters_filled' => 22.2,
      ],
      [
        'date' => '2023-06-25',
        'from_bars' => 2,
        'to_bars' => 8,
        'odometer' => 1477,
        'price_per_liter' => 63.15,
        'liters_filled' => 16.142,
      ],
      [
        'date' => '2023-07-02',
        'from_bars' => 4,
        'to_bars' => 8,
        'odometer' => 1595,
        'price_per_liter' => 63.35,
        'liters_filled' => 11.709,
      ],
      [
        'date' => '2023-07-15',
        'from_bars' => 2,
        'to_bars' => 8,
        'odometer' => 1839,
        'price_per_liter' => 62.45,
        'liters_filled' => 22.227,
      ],
      [
        'date' => '2023-07-25',
        'from_bars' => 1,
        'to_bars' => 8,
        'odometer' => 2084,
        'price_per_liter' => 64.35,
        'liters_filled' => 22.542,
      ],
      [
        'date' => '2023-08-02',
        'from_bars' => 1,
        'to_bars' => 4,
        'odometer' => 2299,
        'price_per_liter' => 67.8,
        'liters_filled' => 10,
      ],
      [
        'date' => '2023-08-08',
        'from_bars' => 2,
        'to_bars' => 8,
        'odometer' => 2342,
        'price_per_liter' => 67.8,
        'liters_filled' => 19.083,
      ],
      [
        'date' => '2023-08-16',
        'from_bars' => 3,
        'to_bars' => 8,
        'odometer' => 2448,
        'price_per_liter' => 70.2,
        'liters_filled' => 13.975,
      ],
      [
        'date' => '2023-08-23',
        'from_bars' => 0,
        'to_bars' => 3,
        'odometer' => 2685,
        'price_per_liter' => 71.3,
        'liters_filled' => 7.012,
      ],
      [
        'date' => '2023-08-24',
        'from_bars' => 1,
        'to_bars' => 8,
        'odometer' => 2746,
        'price_per_liter' => 71.3,
        'liters_filled' => 24.072,
      ],
      [
        'date' => '2023-09-02',
        'from_bars' => 0,
        'to_bars' => 8,
        'odometer' => 2982,
        'price_per_liter' => 71.6,
        'liters_filled' => 23.16,
      ],
      [
        'date' => '2023-09-10',
        'from_bars' => 5,
        'to_bars' => 8,
        'odometer' => 3070,
        'price_per_liter' => 72.1,
        'liters_filled' => 8.26,
      ],
      [
        'date' => '2023-09-17',
        'from_bars' => 3,
        'to_bars' => 8,
        'odometer' => 3211,
        'price_per_liter' => 72.3,
        'liters_filled' => 16.051,
      ],
      [
        'date' => '2023-09-21',
        'from_bars' => 5,
        'to_bars' => 8,
        'odometer' => 3304,
        'price_per_liter' => 74.3,
        'liters_filled' => 7.912,
      ],
      [
        'date' => '2023-10-06',
        'from_bars' => 4,
        'to_bars' => 8,
        'odometer' => 3436,
        'price_per_liter' => 69.65,
        'liters_filled' => 12.891,
      ],
      [
        'date' => '2023-10-10',
        'from_bars' => 6,
        'to_bars' => 8,
        'odometer' => 3497,
        'price_per_liter' => 64.63,
        'liters_filled' => 7.554,
      ],
      [
        'date' => '2023-10-14',
        'from_bars' => 7,
        'to_bars' => 8,
        'odometer' => 3541,
        'price_per_liter' => 64.63,
        'liters_filled' => 4.712,
      ],
      [
        'date' => '2023-10-14',
        'from_bars' => 4,
        'to_bars' => 8,
        'odometer' => 3782,
        'price_per_liter' => 64.63,
        'liters_filled' => 11.248,
      ],
      [
        'date' => '2023-10-21',
        'from_bars' => 2,
        'to_bars' => 8,
        'odometer' => 4064,
        'price_per_liter' => 65.18,
        'liters_filled' => 18.938,
      ],
      [
        'date' => '2023-10-29',
        'from_bars' => 5,
        'to_bars' => 8,
        'odometer' => 4212,
        'price_per_liter' => null,
        'liters_filled' => null,
      ],
      [
        'date' => '2023-11-02',
        'from_bars' => 4,
        'to_bars' => 8,
        'odometer' => 4371,
        'price_per_liter' => 66.06,
        'liters_filled' => 14.213,
      ],
      [
        'date' => '2023-11-05',
        'from_bars' => 6,
        'to_bars' => 8,
        'odometer' => 4440,
        'price_per_liter' => 66.06,
        'liters_filled' => 5.229,
      ],
      [
        'date' => '2023-11-11',
        'from_bars' => 4,
        'to_bars' => 8,
        'odometer' => 4561,
        'price_per_liter' => 66.06,
        'liters_filled' => 5.229,
      ],
      [
        'date' => '2023-11-24',
        'from_bars' => 4,
        'to_bars' => 8,
        'odometer' => 4684,
        'price_per_liter' => 64.16,
        'liters_filled' => 12.145,
      ],
      [
        'date' => '2023-11-26',
        'from_bars' => 4,
        'to_bars' => 8,
        'odometer' => 4805,
        'price_per_liter' => 64.16,
        'liters_filled' => 9.968,
      ],
      [
        'date' => '2023-11-29',
        'from_bars' => 7,
        'to_bars' => 8,
        'odometer' => 4838,
        'price_per_liter' => 62.55,
        'liters_filled' => 5.672,
      ],
      [
        'date' => '2023-12-01',
        'from_bars' => 3,
        'to_bars' => 6,
        'odometer' => 5144,
        'price_per_liter' => null,
        'liters_filled' => null,
      ],
      [
        'date' => '2023-12-02',
        'from_bars' => 2,
        'to_bars' => 8,
        'odometer' => 5319,
        'price_per_liter' => null,
        'liters_filled' => null,
      ],
      [
        'date' => '2023-12-09',
        'from_bars' => 2,
        'to_bars' => 4,
        'odometer' => 5516,
        'price_per_liter' => 61.1,
        'liters_filled' => 10,
      ],
      [
        'date' => '2023-12-19',
        'from_bars' => 2,
        'to_bars' => 8,
        'odometer' => 5646,
        'price_per_liter' => 60.92,
        'liters_filled' => 16.834,
      ],
      [
        'date' => '2023-12-22',
        'from_bars' => 6,
        'to_bars' => 8,
        'odometer' => 5687,
        'price_per_liter' => 60.92,
        'liters_filled' => 7.834,
      ],
      [
        'date' => '2023-12-25',
        'from_bars' => 4,
        'to_bars' => 7,
        'odometer' => 5934,
        'price_per_liter' => 69.65,
        'liters_filled' => 10,
      ],
      [
        'date' => '2023-12-30',
        'from_bars' => 2,
        'to_bars' => 8,
        'odometer' => 6166,
        'price_per_liter' => 62.52,
        'liters_filled' => 17.717,
      ],
      [
        'date' => '2024-01-06',
        'from_bars' => 3,
        'to_bars' => 8,
        'odometer' => 6305,
        'price_per_liter' => 62.2,
        'liters_filled' => 15.192,
      ],
      [
        'date' => '2024-01-12',
        'from_bars' => 3,
        'to_bars' => 8,
        'odometer' => 6468,
        'price_per_liter' => 62.3,
        'liters_filled' => 16.52,
      ],
      [
        'date' => '2024-01-24',
        'from_bars' => 3,
        'to_bars' => 8,
        'odometer' => 6631,
        'price_per_liter' => 63.9,
        'liters_filled' => 17.197,
      ],
      [
        'date' => '2024-02-03',
        'from_bars' => 4,
        'to_bars' => 8,
        'odometer' => 6754,
        'price_per_liter' => 63.11,
        'liters_filled' => 15.846,
      ],
      [
        'date' => '2024-02-17',
        'from_bars' => 5,
        'to_bars' => 8,
        'odometer' => 6878,
        'price_per_liter' => 63.26,
        'liters_filled' => 11.9,
      ],
      [
        'date' => '2024-02-24',
        'from_bars' => 2,
        'to_bars' => 5,
        'odometer' => 7077,
        'price_per_liter' => 67.9,
        'liters_filled' => 8,
      ],
      [
        'date' => '2024-02-25',
        'from_bars' => 3,
        'to_bars' => 8,
        'odometer' => 7140,
        'price_per_liter' => 64.86,
        'liters_filled' => 17.994,
      ],
      [
        'date' => '2024-03-20',
        'from_bars' => 2,
        'to_bars' => 8,
        'odometer' => 7522,
        'price_per_liter' => 64.26,
        'liters_filled' => 20.78,
      ],
      [
        'date' => '2024-03-22',
        'from_bars' => 7,
        'to_bars' => 8,
        'odometer' => 7597,
        'price_per_liter' => 63.75,
        'liters_filled' => 6.573,
      ],
      [
        'date' => '2024-03-24',
        'from_bars' => 3,
        'to_bars' => 8,
        'odometer' => 7901,
        'price_per_liter' => 63.75,
        'liters_filled' => 15.86,
      ],
      [
        'date' => '2024-04-06',
        'from_bars' => 3,
        'to_bars' => 6,
        'odometer' => 8032,
        'price_per_liter' => 66.38,
        'liters_filled' => 7.532,
      ],
      [
        'date' => '2024-04-06',
        'from_bars' => 2,
        'to_bars' => 5,
        'odometer' => 8167,
        'price_per_liter' => 68.31,
        'liters_filled' => 7.32,
      ],
      [
        'date' => '2024-04-07',
        'from_bars' => 3,
        'to_bars' => 5,
        'odometer' => 8266,
        'price_per_liter' => 66.38,
        'liters_filled' => 7.532,
      ],
      [
        'date' => '2024-04-11',
        'from_bars' => 2,
        'to_bars' => 9,
        'odometer' => 8343,
        'price_per_liter' => 66.35,
        'liters_filled' => 20.545,
      ],
      [
        'date' => '2024-04-20',
        'from_bars' => 3,
        'to_bars' => 8,
        'odometer' => 8613,
        'price_per_liter' => 67.7,
        'liters_filled' => 16.912,
      ],
      [
        'date' => '2024-04-28',
        'from_bars' => 2,
        'to_bars' => 3,
        'odometer' => 8888,
        'price_per_liter' => 73.85,
        'liters_filled' => 4.73,
      ],
      [
        'date' => '2024-04-28',
        'from_bars' => 3,
        'to_bars' => 9,
        'odometer' => 8960,
        'price_per_liter' => 66.33,
        'liters_filled' => 19.373,
      ],
      [
        'date' => '2024-05-08',
        'from_bars' => 4,
        'to_bars' => 9,
        'odometer' => 9160,
        'price_per_liter' => 65.33,
        'liters_filled' => 15.646,
      ],
      [
        'date' => '2024-05-17',
        'from_bars' => 4,
        'to_bars' => 9,
        'odometer' => 9399,
        'price_per_liter' => 63.33,
        'liters_filled' => 16.175,
      ],
      [
        'date' => '2024-05-25',
        'from_bars' => 3,
        'to_bars' => 9,
        'odometer' => 9625,
        'price_per_liter' => 62.90,
        'liters_filled' => 18.437,
      ],
      [
        'date' => '2024-06-04',
        'from_bars' => 4,
        'to_bars' => 9,
        'odometer' => 9824,
        'price_per_liter' => 62.40,
        'liters_filled' => 15.166,
      ],
      [
        'date' => '2024-06-09',
        'from_bars' => 7,
        'to_bars' => 9,
        'odometer' => 9919,
        'price_per_liter' => 62.40,
        'liters_filled' => 6.653,
      ],
      [
        'date' => '2024-06-14',
        'from_bars' => 6,
        'to_bars' => 9,
        'odometer' => 10053,
        'price_per_liter' => 61.80,
        'liters_filled' => 10.105,
      ],
      [
        'date' => '2024-06-16',
        'from_bars' => 5,
        'to_bars' => 9,
        'odometer' => 10209,
        'price_per_liter' => 61.80,
        'liters_filled' => 12.575,
      ],
      [
        'date' => '2024-06-27',
        'from_bars' => 3,
        'to_bars' => 9,
        'odometer' => 10433,
        'price_per_liter' => 64.05,
        'liters_filled' => 18.105,
      ],
      [
        'date' => '2024-07-03',
        'from_bars' => 4,
        'to_bars' => 9,
        'odometer' => 10624,
        'price_per_liter' => 65,
        'liters_filled' => 14.667,
      ],
      [
        'date' => '2024-07-12',
        'from_bars' => 2,
        'to_bars' => 9,
        'odometer' => 10913,
        'price_per_liter' => 66.3,
        'liters_filled' => 22.065,
      ],
      [
        'date' => '2024-07-21',
        'from_bars' => 5,
        'to_bars' => 9,
        'odometer' => 11093,
        'price_per_liter' => 65.7,
        'liters_filled' => 11.989,
      ],
      [
        'date' => '2024-08-08',
        'from_bars' => 4,
        'to_bars' => 9,
        'odometer' => 11279,
        'price_per_liter' => 64.95,
        'liters_filled' => 14.613,
      ],
      [
        'date' => '2024-08-18',
        'from_bars' => 3,
        'to_bars' => 5,
        'odometer' => 11499,
        'price_per_liter' => 62.5,
        'liters_filled' => 8,
      ],
      [
        'date' => '2024-08-22',
        'from_bars' => 5,
        'to_bars' => 9,
        'odometer' => 11537,
        'price_per_liter' => 63.5,
        'liters_filled' => 12.905,
      ],
      [
        'date' => '2024-09-07',
        'from_bars' => 3,
        'to_bars' => 9,
        'odometer' => 11719,
        'price_per_liter' => 62.29,
        'liters_filled' => 17.018,
      ],
      [
        'date' => '2024-09-15',
        'from_bars' => 6,
        'to_bars' => 9,
        'odometer' => 11809,
        'price_per_liter' => 59.3,
        'liters_filled' => 9.029,
      ],
      [
        'date' => '2024-10-04',
        'from_bars' => 3,
        'to_bars' => 9,
        'odometer' => 12024,
        'price_per_liter' => 59.34,
        'liters_filled' => 17.443,
      ],
      [
        'date' => '2024-10-19',
        'from_bars' => 2,
        'to_bars' => 9,
        'odometer' => 12282,
        'price_per_liter' => 61.7,
        'liters_filled' => 20.613,
      ],
      [
        'date' => '2024-11-02',
        'from_bars' => 4,
        'to_bars' => 9,
        'odometer' => 12447,
        'price_per_liter' => 60.8,
        'liters_filled' => 14.98,
      ],
      [
        'date' => '2024-11-30',
        'from_bars' => 4,
        'to_bars' => 9,
        'odometer' => 12803,
        'price_per_liter' => 60.85,
        'liters_filled' => 15.712,
      ],
      [
        'date' => '2024-12-07',
        'from_bars' => 3,
        'to_bars' => 9,
        'odometer' => 13034,
        'price_per_liter' => 61.75,
        'liters_filled' => 18.288,
      ],
      [
        'date' => '2024-12-08',
        'from_bars' => 7,
        'to_bars' => 9,
        'odometer' => 13097,
        'price_per_liter' => 61.34,
        'liters_filled' => 6.606,
      ],
      [
        'date' => '2024-12-22',
        'from_bars' => 2,
        'to_bars' => 9,
        'odometer' => 13369,
        'price_per_liter' => 62.3,
        'liters_filled' => 21.302,
      ],
      [
        'date' => '2025-01-11',
        'from_bars' => 3,
        'to_bars' => 9,
        'odometer' => 13598,
        'price_per_liter' => 62.05,
        'liters_filled' => 19.018,
      ],
      [
        'date' => '2025-01-25',
        'from_bars' => 3,
        'to_bars' => 9,
        'odometer' => 13837,
        'price_per_liter' => 63.95,
        'liters_filled' => 18.373,
      ],
      [
        'date' => '2025-02-09',
        'from_bars' => 3,
        'to_bars' => 9,
        'odometer' => 14054,
        'price_per_liter' => 61,
        'liters_filled' => 19.815,
      ],
      [
        'date' => '2025-02-15',
        'from_bars' => 2,
        'to_bars' => 9,
        'odometer' => 14332,
        'price_per_liter' => 62,
        'liters_filled' => 22.14,
      ],
    ];

    foreach ($data as $item) {
      Gas::create($item);
    }
  }
}
