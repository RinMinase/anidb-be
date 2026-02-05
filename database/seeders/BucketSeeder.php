<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Bucket;

class BucketSeeder extends Seeder {
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {
    $testData = [
      [
        'from' => 'a',
        'to' => 'd',
        'size' => 2_000_339_066_880,
        'purchase_date' => '2020-01-01',
        'last_six_sn' => 'AAA111',
      ],
      [
        'from' => 'e',
        'to' => 'h',
        'size' => 2_000_339_066_880,
        'purchase_date' => '2021-01-01',
        'last_six_sn' => 'BBB222',
      ],
      [
        'from' => 'i',
        'to' => 'l',
        'size' => 2_000_339_066_880,
        'purchase_date' => '2022-01-01',
        'last_six_sn' => 'CCC333',
      ],
      [
        'from' => 'm',
        'to' => 'p',
        'size' => 2_000_339_066_880,
        'purchase_date' => '2023-01-01',
        'last_six_sn' => 'DDD444',
      ],
      [
        'from' => 'q',
        'to' => 'u',
        'size' => 2_000_339_066_880,
        'purchase_date' => '2024-01-01',
        'last_six_sn' => 'EEE555',
      ],
      [
        'from' => 'v',
        'to' => 'z',
        'size' => 2_000_339_066_880,
        'purchase_date' => '2025-01-01',
        'last_six_sn' => 'FFF666',
      ],
    ];

    foreach ($testData as $item) {
      Bucket::create($item);
    }
  }
}
