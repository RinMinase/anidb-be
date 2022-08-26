<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

use App\Models\BucketSim;
use App\Models\BucketSimInfo;

class BucketSimSeeder extends Seeder {
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {
    $testInfo = [
      [
        'uuid' => Str::uuid()->toString(),
        'description' => '6 Buckets',
      ], [
        'uuid' => Str::uuid()->toString(),
        'description' => '10 Buckets',
      ],
    ];

    $testSims = [
      [
        'id_sim_info' => 1,
        'from' => 'a',
        'to' => 'd',
        'size' => 2_000_339_066_880,
      ], [
        'id_sim_info' => 1,
        'from' => 'e',
        'to' => 'h',
        'size' => 2_000_339_066_880,
      ], [
        'id_sim_info' => 1,
        'from' => 'i',
        'to' => 'l',
        'size' => 2_000_339_066_880,
      ], [
        'id_sim_info' => 1,
        'from' => 'm',
        'to' => 'p',
        'size' => 2_000_339_066_880,
      ], [
        'id_sim_info' => 1,
        'from' => 'q',
        'to' => 'u',
        'size' => 2_000_339_066_880,
      ], [
        'id_sim_info' => 1,
        'from' => 'v',
        'to' => 'z',
        'size' => 2_000_339_066_880,
      ], [
        'id_sim_info' => 2,
        'from' => 'a',
        'to' => 'b',
        'size' => 2_000_339_066_880,
      ], [
        'id_sim_info' => 2,
        'from' => 'c',
        'to' => 'e',
        'size' => 2_000_339_066_880,
      ], [
        'id_sim_info' => 2,
        'from' => 'f',
        'to' => 'g',
        'size' => 2_000_339_066_880,
      ], [
        'id_sim_info' => 2,
        'from' => 'h',
        'to' => 'j',
        'size' => 2_000_339_066_880,
      ], [
        'id_sim_info' => 2,
        'from' => 'k',
        'to' => 'k',
        'size' => 2_000_339_066_880,
      ], [
        'id_sim_info' => 2,
        'from' => 'l',
        'to' => 'l',
        'size' => 2_000_339_066_880,
      ], [
        'id_sim_info' => 2,
        'from' => 'm',
        'to' => 'n',
        'size' => 2_000_339_066_880,
      ], [
        'id_sim_info' => 2,
        'from' => 'o',
        'to' => 'r',
        'size' => 2_000_339_066_880,
      ], [
        'id_sim_info' => 2,
        'from' => 's',
        'to' => 's',
        'size' => 2_000_339_066_880,
      ], [
        'id_sim_info' => 2,
        'from' => 't',
        'to' => 'z',
        'size' => 2_000_339_066_880,
      ],
    ];

    foreach ($testInfo as $item) {
      BucketSimInfo::create($item);
    }

    foreach ($testSims as $item) {
      BucketSim::create($item);
    }
  }
}
