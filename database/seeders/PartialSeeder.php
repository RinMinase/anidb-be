<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

use App\Models\Partial;

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
      ], [
        'uuid' => Str::uuid()->toString(),
        'title' => 'partial 4',
        'id_catalogs' => 1,
        'id_priority' => 2,
      ], [
        'uuid' => Str::uuid()->toString(),
        'title' => 'partial 2',
        'id_catalogs' => 2,
        'id_priority' => 2,
      ], [
        'uuid' => Str::uuid()->toString(),
        'title' => 'partial 3',
        'id_catalogs' => 3,
        'id_priority' => 3,
      ],
    ];

    foreach ($testData as $item) {
      Partial::create($item);
    }
  }
}
