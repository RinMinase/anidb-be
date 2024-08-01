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
    $uuid_1 = Str::uuid()->toString();
    $uuid_2 = Str::uuid()->toString();

    $testInfo = [
      [
        'uuid' => $uuid_1,
        'description' => '6 Buckets',
      ], [
        'uuid' => $uuid_2,
        'description' => '10 Buckets',
      ],
    ];

    foreach ($testInfo as $item) {
      BucketSimInfo::create($item);
    }

    $id_sim_info_1 = BucketSimInfo::where('uuid', $uuid_1)->first()->id;
    $id_sim_info_2 = BucketSimInfo::where('uuid', $uuid_2)->first()->id;

    $testSims = [
      [
        'id_sim_info' => $id_sim_info_1,
        'from' => 'a',
        'to' => 'd',
        'size' => 2_000_339_066_880,
      ], [
        'id_sim_info' => $id_sim_info_1,
        'from' => 'e',
        'to' => 'h',
        'size' => 2_000_339_066_880,
      ], [
        'id_sim_info' => $id_sim_info_1,
        'from' => 'i',
        'to' => 'l',
        'size' => 2_000_339_066_880,
      ], [
        'id_sim_info' => $id_sim_info_1,
        'from' => 'm',
        'to' => 'p',
        'size' => 2_000_339_066_880,
      ], [
        'id_sim_info' => $id_sim_info_1,
        'from' => 'q',
        'to' => 'u',
        'size' => 2_000_339_066_880,
      ], [
        'id_sim_info' => $id_sim_info_1,
        'from' => 'v',
        'to' => 'z',
        'size' => 2_000_339_066_880,
      ], [
        'id_sim_info' => $id_sim_info_2,
        'from' => 'a',
        'to' => 'b',
        'size' => 2_000_339_066_880,
      ], [
        'id_sim_info' => $id_sim_info_2,
        'from' => 'c',
        'to' => 'e',
        'size' => 2_000_339_066_880,
      ], [
        'id_sim_info' => $id_sim_info_2,
        'from' => 'f',
        'to' => 'g',
        'size' => 2_000_339_066_880,
      ], [
        'id_sim_info' => $id_sim_info_2,
        'from' => 'h',
        'to' => 'j',
        'size' => 2_000_339_066_880,
      ], [
        'id_sim_info' => $id_sim_info_2,
        'from' => 'k',
        'to' => 'k',
        'size' => 2_000_339_066_880,
      ], [
        'id_sim_info' => $id_sim_info_2,
        'from' => 'l',
        'to' => 'l',
        'size' => 2_000_339_066_880,
      ], [
        'id_sim_info' => $id_sim_info_2,
        'from' => 'm',
        'to' => 'n',
        'size' => 2_000_339_066_880,
      ], [
        'id_sim_info' => $id_sim_info_2,
        'from' => 'o',
        'to' => 'r',
        'size' => 2_000_339_066_880,
      ], [
        'id_sim_info' => $id_sim_info_2,
        'from' => 's',
        'to' => 's',
        'size' => 2_000_339_066_880,
      ], [
        'id_sim_info' => $id_sim_info_2,
        'from' => 't',
        'to' => 'z',
        'size' => 2_000_339_066_880,
      ],
    ];

    foreach ($testSims as $item) {
      BucketSim::create($item);
    }
  }
}
