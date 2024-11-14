<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

use App\Models\PCSetupInventory;
use App\Models\PCSetupInventoryType;

class PCSetupInventorySeeder extends Seeder {

  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {
    $data = [
      [
        'id_pc_setups_inventory_type' => $this->get_id_from_type('cpu'),
        'name' => 'Some CPU Name',
        'price' => 30_000,
        'purchase_date' => '2020-01-30',
        'purchase_location' => 'Some store',
        'is_onhand' => true,
      ],
      [
        'id_pc_setups_inventory_type' => $this->get_id_from_type('ram'),
        'name' => 'Some RAM Name',
        'price' => 10_000,
        'purchase_date' => '2020-02-05',
        'purchase_location' => 'Another store',
        'is_onhand' => true,
      ],
      [
        'id_pc_setups_inventory_type' => $this->get_id_from_type('gpu'),
        'name' => 'Some GPU Name',
        'price' => 50_000,
        'purchase_date' => '2021-01-01',
        'purchase_location' => 'Some GPU store',
        'is_onhand' => true,
      ],
    ];

    foreach ($data as $item) {
      PCSetupInventory::create([
        'uuid' => Str::uuid()->toString(),
        ...$item,
      ]);
    }
  }

  private function get_id_from_type(string $type): int {
    $types = PCSetupInventoryType::all()->toArray();
    $id_other_type = PCSetupInventoryType::select('id')
      ->where('inventory_type', 'other')
      ->first()
      ->id;

    foreach ($types as $value) {
      if ($value['inventory_type'] === $type) {
        return intval($value['id']);
      }
    }

    return $id_other_type;
  }
}
