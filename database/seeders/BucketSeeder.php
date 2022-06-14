<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

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
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
      ], [
        'from' => 'e',
        'to' => 'h',
        'size' => 2_000_339_066_880,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
      ], [
        'from' => 'i',
        'to' => 'l',
        'size' => 2_000_339_066_880,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
      ], [
        'from' => 'm',
        'to' => 'p',
        'size' => 2_000_339_066_880,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
      ], [
        'from' => 'q',
        'to' => 'u',
        'size' => 2_000_339_066_880,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
      ], [
        'from' => 'v',
        'to' => 'z',
        'size' => 2_000_339_066_880,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
      ],
    ];

    DB::table('buckets')->insert($testData);
  }
}
