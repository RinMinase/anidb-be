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
        'color' => '#F78FA7',
      ],
      [
        'label' => 'Together',
        'color' => '#B39DDB',
      ]
    ];

    foreach ($data as $item) {
      EntryWatcher::create($item);
    }
  }
}
