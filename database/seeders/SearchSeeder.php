<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

use App\Models\Search;

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
      ], [
        'id_user' => null,
        'uuid' => Str::uuid()->toString(),
      ],
    ];

    foreach ($testData as $item) {
      Search::create($item);
    }
  }
}
