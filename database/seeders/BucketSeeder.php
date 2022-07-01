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
      ], [
        'from' => 'e',
        'to' => 'h',
        'size' => 2_000_339_066_880,
      ], [
        'from' => 'i',
        'to' => 'l',
        'size' => 2_000_339_066_880,
      ], [
        'from' => 'm',
        'to' => 'p',
        'size' => 2_000_339_066_880,
      ], [
        'from' => 'q',
        'to' => 'u',
        'size' => 2_000_339_066_880,
      ], [
        'from' => 'v',
        'to' => 'z',
        'size' => 2_000_339_066_880,
      ],
    ];

    foreach ($testData as $item) {
      Bucket::create($item);
    }
  }
}
