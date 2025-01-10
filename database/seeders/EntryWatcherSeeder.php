<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\EntryWatcher;

class EntryWatcherSeeder extends Seeder {
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {
    $data = [
      [
        'label' => 'Pamm',
        'color' => '#F2C6DE',
      ],
      [
        'label' => 'Together',
        'color' => '#C6DEF1',
      ]
    ];

    foreach ($data as $item) {
      EntryWatcher::create($item);
    }
  }
}
