<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SearchSeeder extends Seeder {
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {
    $testData = [
      [
        'id_user' => null,
        'uuid' => Str::uuid()->toString(),
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
      ], [
        'id_user' => null,
        'uuid' => Str::uuid()->toString(),
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
      ],
    ];

    DB::table('search')->insert($testData);
  }
}
