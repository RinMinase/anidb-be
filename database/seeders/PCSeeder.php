<?php

namespace Database\Seeders;

use App\Models\PCComponentType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

use App\Models\PCInfo;
use App\Models\PCOwner;
use App\Models\PCComponent;
use App\Models\PCSetup;

class PCSeeder extends Seeder {

  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {
    $id_owner_alice = PCOwner::where('name', 'Alice')->first()->makeVisible('id')->id;
    $id_owner_hecate = PCOwner::where('name', 'Hecate')->first()->makeVisible('id')->id;

    $dataInfo = [
      [
        'label' => 'Alice v1',
        'is_current' => false,
        'id_owner' => $id_owner_alice,
      ],
      [
        'label' => 'Alice v2',
        'is_current' => true,
        'id_owner' => $id_owner_alice,
      ],
      [
        'label' => 'Hecate v1',
        'is_current' => false,
        'id_owner' => $id_owner_hecate,
      ],
    ];

    $id_info_alice1 = PCInfo::insertGetId([
      'uuid' => Str::uuid()->toString(),
      ...$dataInfo[0],
    ]);

    $id_info_alice2 = PCInfo::insertGetId([
      'uuid' => Str::uuid()->toString(),
      ...$dataInfo[0],
    ]);

    $id_info_hecate = PCInfo::insertGetId([
      'uuid' => Str::uuid()->toString(),
      ...$dataInfo[0],
    ]);

    $id_component_types = PCComponentType::select('id', 'type')->get()->toArray();

    $data_components_alice1 = [
      [
        'id' => 1,
        'id_type' => $this->get_id_from_type($id_component_types, 'cpu'),
        'name' => 'Intel Core i7-2600',
        'description' => null,
        'price' => 18_000,
        'is_onhand' => false,
      ],
      [
        'id' => 2,
        'id_type' => $this->get_id_from_type($id_component_types, 'gpu'),
        'name' => 'Asus ROG Strix GTX 1070',
        'description' => null,
        'price' => 26_638,
        'is_onhand' => false,
      ],
      [
        'id' => 3,
        'id_type' => $this->get_id_from_type($id_component_types, 'motherboard'),
        'name' => 'Gigabyte GA-B75M-HD3 mATX',
        'description' => null,
        'price' => 1_500,
        'is_onhand' => false,
      ],
      [
        'id' => 4,
        'id_type' => $this->get_id_from_type($id_component_types, 'ram'),
        'name' => 'Kingston ValueRam 2x 8GB DDR3',
        'description' => '1600 MHz C11',
        'price' => 4_000,
        'is_onhand' => false,
      ],
      [
        'id' => 5,
        'id_type' => $this->get_id_from_type($id_component_types, 'hdd'),
        'name' => 'Seagate 1TB',
        'description' => null,
        'price' => 2_000,
        'is_onhand' => false,
      ],
      [
        'id' => 6,
        'id_type' => $this->get_id_from_type($id_component_types, 'chassis'),
        'name' => 'Tecware Nexus ATX Mid Tower Case',
        'description' => null,
        'price' => 2_250,
        'is_onhand' => false,
      ],
      [
        'id' => 7,
        'id_type' => $this->get_id_from_type($id_component_types, 'fan'),
        'name' => 'Deepcool XFAN 120mm',
        'description' => null,
        'price' => 150,
        'is_onhand' => false,
      ],
      [
        'id' => 8,
        'id_type' => $this->get_id_from_type($id_component_types, 'psu'),
        'name' => 'Corsair VS650',
        'description' => null,
        'price' => 2_600,
        'is_onhand' => false,
      ],
      [
        'id' => 9,
        'id_type' => $this->get_id_from_type($id_component_types, 'pcie card'),
        'name' => 'TP-Link TL-WN881ND WiFi Adapter',
        'description' => null,
        'price' => 1_200,
        'is_onhand' => false,
      ],
      [
        'id' => 10,
        'id_type' => $this->get_id_from_type($id_component_types, 'monitor'),
        'name' => 'Samsung S20D300H 19.5in',
        'description' => '1600 x 900 60Hz',
        'price' => 3_000,
        'is_onhand' => false,
      ],
      [
        'id' => 11,
        'id_type' => $this->get_id_from_type($id_component_types, 'keyboard'),
        'name' => 'Razer BlackWidow X TE Chroma',
        'description' => null,
        'price' => 5_920,
        'is_onhand' => false,
      ],
      [
        'id' => 12,
        'id_type' => $this->get_id_from_type($id_component_types, 'mouse'),
        'name' => 'Razer DeathAdder Elite',
        'description' => null,
        'price' => 2_960,
        'is_onhand' => false,
      ],
      [
        'id' => 13,
        'id_type' => $this->get_id_from_type($id_component_types, 'speakers'),
        'name' => 'Creative A220 2.1ch',
        'description' => null,
        'price' => 1_400,
        'is_onhand' => false,
      ],
    ];

    foreach ($data_components_alice1 as $item) {
      PCComponent::create($item);
    }

    for ($i = 1; $i <= count($data_components_alice1); $i++) {
      $data_to_insert = [
        'id_owner' => $id_owner_alice,
        'id_info' => $id_info_alice1,
        'id_component' => $i,
        'count' => 1,
      ];

      if ($i === 4) {
        $data_to_insert['count'] = 2; // RAM
      } else if ($i === 5) {
        $data_to_insert['count'] = 2; // HDD
      } else if ($i === 7) {
        $data_to_insert['count'] = 3; // Fans
      }

      PCSetup::create($data_to_insert);
    }
  }

  private function get_id_from_type(array $types, string $type): int {
    foreach ($types as $item) {
      if ($item['type'] === $type) {
        return intval($item['id']);
      }
    }

    return -1;
  }
}
