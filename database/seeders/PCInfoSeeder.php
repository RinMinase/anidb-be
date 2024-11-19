<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

use App\Models\PCInfo;
use App\Models\PCOwner;

class PCInfoSeeder extends Seeder {

  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {
    $id_alice = PCOwner::where('name', 'Alice')->first()->makeVisible('id')->id;
    $id_hecate = PCOwner::where('name', 'Hecate')->first()->makeVisible('id')->id;

    $data = [
      [
        'label' => 'Alice v1',
        'is_current' => false,
        'id_owner' => $id_alice,
      ],
      [
        'label' => 'Alice v2',
        'is_current' => true,
        'id_owner' => $id_alice,
      ],
      [
        'label' => 'Hecate v1',
        'is_current' => false,
        'id_owner' => $id_hecate,
      ],
    ];

    foreach ($data as $item) {
      PCInfo::create([
        'uuid' => Str::uuid()->toString(),
        ...$item,
      ]);
    }
  }
}
