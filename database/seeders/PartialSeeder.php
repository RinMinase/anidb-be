<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

use App\Models\Catalog;
use App\Models\Partial;
use App\Models\Priority;

class PartialSeeder extends Seeder {
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {
    $id_catalog_1 = Catalog::where('year', 2020)->where('season', 'Summer')->first()->id;
    $id_catalog_2 = Catalog::where('year', 2020)->where('season', 'Fall')->first()->id;
    $id_catalog_3 = Catalog::where('year', 2021)->where('season', 'Winter')->first()->id;

    $id_priority_1 = Priority::where('priority', 'High')->first()->id;
    $id_priority_2 = Priority::where('priority', 'Normal')->first()->id;
    $id_priority_3 = Priority::where('priority', 'Low')->first()->id;

    $testData = [
      [
        'uuid' => Str::uuid()->toString(),
        'title' => 'partial 1',
        'id_catalog' => $id_catalog_1,
        'id_priority' => $id_priority_1,
      ], [
        'uuid' => Str::uuid()->toString(),
        'title' => 'partial 4',
        'id_catalog' => $id_catalog_1,
        'id_priority' => $id_priority_2,
      ], [
        'uuid' => Str::uuid()->toString(),
        'title' => 'partial 2',
        'id_catalog' => $id_catalog_2,
        'id_priority' => $id_priority_2,
      ], [
        'uuid' => Str::uuid()->toString(),
        'title' => 'partial 3',
        'id_catalog' => $id_catalog_3,
        'id_priority' => $id_priority_3,
      ],
    ];

    foreach ($testData as $item) {
      Partial::create($item);
    }
  }
}
