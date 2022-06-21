<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class CatalogSeeder extends Seeder {
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {
    $testData = [
      [
        'uuid' => Str::uuid()->toString(),
        'description' => 'Summer 2020',
        'order' => 1,
        'year' => 2020,
        'season' => 'Summer',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
      ], [
        'uuid' => Str::uuid()->toString(),
        'description' => 'Fall 2020',
        'order' => 2,
        'year' => 2020,
        'season' => 'Fall',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
      ], [
        'uuid' => Str::uuid()->toString(),
        'description' => 'Spring 2021',
        'order' => null,
        'year' => 2020,
        'season' => 'Fall',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
      ],
    ];

    DB::table('catalogs')->insert($testData);
  }
}
