<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

use App\Models\Catalog;

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
        'year' => 2020,
        'season' => 'Summer',
      ], [
        'uuid' => Str::uuid()->toString(),
        'year' => 2020,
        'season' => 'Fall',
      ], [
        'uuid' => Str::uuid()->toString(),
        'year' => 2021,
        'season' => 'Winter',
      ],
    ];

    foreach ($testData as $item) {
      Catalog::create($item);
    }
  }
}
