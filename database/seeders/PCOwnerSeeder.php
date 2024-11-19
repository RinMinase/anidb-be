<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

use App\Models\PCOwner;

class PCOwnerSeeder extends Seeder {

  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {
    $data = [
      'Alice',
      'Hecate',
    ];

    foreach ($data as $item) {
      PCOwner::create([
        'uuid' => Str::uuid()->toString(),
        'name' => $item,
      ]);
    }
  }
}
