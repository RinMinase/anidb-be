<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\PCSetupOld;

class PCSetupOldSeeder extends Seeder {

  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {
    $testData = [
      [
        'label' => 'Setup Name',
        'is_current' => false,
        'is_future' => false,
        'is_server' => false,
        'cpu' => 'CPU Name',
      ],
    ];

    foreach ($testData as $item) {
      PCSetupOld::create($item);
    }
  }
}
