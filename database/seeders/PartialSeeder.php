<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PartialSeeder extends Seeder {
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {
    $testData = [
      [
        'uuid' => Str::uuid()->toString(),
        'title' => 'partial 1',
        'id_catalogs' => 1,
        'id_priority' => 1,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
      ], [
        'uuid' => Str::uuid()->toString(),
        'title' => 'partial 2',
        'id_catalogs' => 2,
        'id_priority' => 2,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
      ], [
        'uuid' => Str::uuid()->toString(),
        'title' => 'partial 3',
        'id_catalogs' => 3,
        'id_priority' => 3,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
      ],
    ];

    DB::table('partials')->insert($testData);
  }
}
