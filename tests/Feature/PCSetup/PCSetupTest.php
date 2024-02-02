<?php

namespace Tests\Feature\PCSetup;

use Tests\BaseTestCase;

use App\Models\PCSetup;

class PCSetupTest extends BaseTestCase {

  private $pcSetup_id = 99999;
  private $pcSetup_label = "Test Run -- Test Data";

  private function setup_config() {
    // Clearing possible duplicate data
    $this->setup_clear();

    PCSetup::insert([
      'id' => $this->pcSetup_id,
      'label' => $this->pcSetup_label,

      'is_current' => false,
      'is_future' => true,
      'is_server' => false,

      'cpu' => 'test cpu',
      'cpu_price' => 100,
      'cpu_sub' => 'cpu subtext',
      'cpu_sub2' => 'cpu subtext 2',

      'ram' => 'test ram',
      'ram_price' => 200,
      'ram_sub' => 'ram subtext',

      'gpu' => 'test gpu',
      'gpu_price' => 300,
      'gpu_sub' => 'gpu subtext',

      'motherboard' => 'test motherboard',
      'motherboard_price' => 400,

      'psu' => 'test psu',
      'psu_price' => 500,

      'cooler' => 'test cooler',
      'cooler_price' => 600,
      'cooler_acc' => 'cooler accessory',
      'cooler_acc_price' => 700,

      'ssd_1' => 'ssd 1',
      'ssd_1_price' => 100,
      'ssd_2' => 'ssd 2',
      'ssd_2_price' => 200,
      'ssd_3' => 'ssd 3',
      'ssd_3_price' => 300,
      'ssd_4' => 'ssd 4',
      'ssd_4_price' => 400,

      'hdd_1' => 'hdd 1',
      'hdd_1_price' => 500,
      'hdd_2' => 'hdd 2',
      'hdd_2_price' => 600,
      'hdd_3' => 'hdd 3',
      'hdd_3_price' => 700,
      'hdd_4' => 'hdd 4',
      'hdd_4_price' => 800,

      'case' => 'test case',
      'case_price' => 100,
      'case_fans_1' => 'case fans 1',
      'case_fans_1_price' => 200,
      'case_fans_2' => 'case fans 2',
      'case_fans_2_price' => 300,
      'case_fans_3' => 'case fans 3',
      'case_fans_3_price' => 400,
      'case_fans_4' => 'case fans 4',
      'case_fans_4_price' => 500,

      'monitor' => 'test monitor',
      'monitor_price' => 100,
      'monitor_sub' => 'monitor subtext',
      'monitor_acc_1' => 'monitor accessory 1',
      'monitor_acc_1_price' => 200,
      'monitor_acc_2' => 'monitor accessory 2',
      'monitor_acc_2_price' => 300,

      'keyboard' => 'test keyboard',
      'keyboard_price' => 100,
      'keyboard_sub' => 'keyboard subtext 1',
      'keyboard_sub2' => 'keyboard subtext 2',
      'keyboard_acc_1' => 'keyboard accessory 1',
      'keyboard_acc_1_price' => 200,
      'keyboard_acc_2' => 'keyboard accessory 2',
      'keyboard_acc_2_price' => 300,

      'mouse' => 'test mouse',
      'mouse_price' => 100,

      'speakers' => 'test speakers',
      'speakers_price' => 200,

      'wifi' => 'test wifi',
      'wifi_price' => 300,

      'headset_1' => 'test headset 1',
      'headset_1_price' => 400,
      'headset_2' => 'test headset 2',
      'headset_2_price' => 500,

      'mic' => 'test mic',
      'mic_price' => 600,
      'mic_acc' => 'mic accessory',
      'mic_acc_price' => 700,

      'audio_interface' => 'test interface',
      'audio_interface_price' => 100,
      'equalizer' => 'test eq',
      'equalizer_price' => 200,
      'amplifier' => 'test amp',
      'amplifier_price' => 300,

      'created_at' => '2000-12-31 23:10:00',
      'updated_at' => '2000-12-31 23:10:00',
    ]);
  }

  private function setup_clear() {
    PCSetup::where('id', $this->pcSetup_id)
      ->where('label', $this->pcSetup_label)
      ->forceDelete();
  }

  public function test_get_all_entries() {
    $this->setup_config();

    $response = $this->withoutMiddleware()
      ->get('/api/pc-setups');

    $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [[
          'id',
          'label',
          'is_current',
          'is_future',
          'is_server',
          'cpu',
          'cpu_price',
          'cpu_sub',
          'cpu_sub2',
          'ram',
          'ram_price',
          'ram_sub',
          'gpu',
          'gpu_price',
          'gpu_sub',
          'motherboard',
          'motherboard_price',
          'psu',
          'psu_price',
          'cooler',
          'cooler_price',
          'cooler_acc',
          'cooler_acc_price',
          'ssd_1',
          'ssd_1_price',
          'ssd_2',
          'ssd_2_price',
          'ssd_3',
          'ssd_3_price',
          'ssd_4',
          'ssd_4_price',
          'hdd_1',
          'hdd_1_price',
          'hdd_2',
          'hdd_2_price',
          'hdd_3',
          'hdd_3_price',
          'hdd_4',
          'hdd_4_price',
          'case',
          'case_price',
          'case_fans_1',
          'case_fans_1_price',
          'case_fans_2',
          'case_fans_2_price',
          'case_fans_3',
          'case_fans_3_price',
          'case_fans_4',
          'case_fans_4_price',
          'monitor',
          'monitor_price',
          'monitor_sub',
          'monitor_acc_1',
          'monitor_acc_1_price',
          'monitor_acc_2',
          'monitor_acc_2_price',
          'keyboard',
          'keyboard_price',
          'keyboard_sub',
          'keyboard_sub2',
          'keyboard_acc_1',
          'keyboard_acc_1_price',
          'keyboard_acc_2',
          'keyboard_acc_2_price',
          'mouse',
          'mouse_price',
          'speakers',
          'speakers_price',
          'wifi',
          'wifi_price',
          'headset_1',
          'headset_1_price',
          'headset_2',
          'headset_2_price',
          'mic',
          'mic_price',
          'mic_acc',
          'mic_acc_price',
          'audio_interface',
          'audio_interface_price',
          'equalizer',
          'equalizer_price',
          'amplifier',
          'amplifier_price',
          'created_at',
          'updated_at',
        ]],
      ]);

    $expected = [
      'id' => $this->pcSetup_id,
      'label' => $this->pcSetup_label,

      'is_current' => false,
      'is_future' => true,
      'is_server' => false,

      'cpu' => 'test cpu',
      'cpu_price' => 100,
      'cpu_sub' => 'cpu subtext',
      'cpu_sub2' => 'cpu subtext 2',

      'ram' => 'test ram',
      'ram_price' => 200,
      'ram_sub' => 'ram subtext',

      'gpu' => 'test gpu',
      'gpu_price' => 300,
      'gpu_sub' => 'gpu subtext',

      'motherboard' => 'test motherboard',
      'motherboard_price' => 400,

      'psu' => 'test psu',
      'psu_price' => 500,

      'cooler' => 'test cooler',
      'cooler_price' => 600,
      'cooler_acc' => 'cooler accessory',
      'cooler_acc_price' => 700,

      'ssd_1' => 'ssd 1',
      'ssd_1_price' => 100,
      'ssd_2' => 'ssd 2',
      'ssd_2_price' => 200,
      'ssd_3' => 'ssd 3',
      'ssd_3_price' => 300,
      'ssd_4' => 'ssd 4',
      'ssd_4_price' => 400,

      'hdd_1' => 'hdd 1',
      'hdd_1_price' => 500,
      'hdd_2' => 'hdd 2',
      'hdd_2_price' => 600,
      'hdd_3' => 'hdd 3',
      'hdd_3_price' => 700,
      'hdd_4' => 'hdd 4',
      'hdd_4_price' => 800,

      'case' => 'test case',
      'case_price' => 100,
      'case_fans_1' => 'case fans 1',
      'case_fans_1_price' => 200,
      'case_fans_2' => 'case fans 2',
      'case_fans_2_price' => 300,
      'case_fans_3' => 'case fans 3',
      'case_fans_3_price' => 400,
      'case_fans_4' => 'case fans 4',
      'case_fans_4_price' => 500,

      'monitor' => 'test monitor',
      'monitor_price' => 100,
      'monitor_sub' => 'monitor subtext',
      'monitor_acc_1' => 'monitor accessory 1',
      'monitor_acc_1_price' => 200,
      'monitor_acc_2' => 'monitor accessory 2',
      'monitor_acc_2_price' => 300,

      'keyboard' => 'test keyboard',
      'keyboard_price' => 100,
      'keyboard_sub' => 'keyboard subtext 1',
      'keyboard_sub2' => 'keyboard subtext 2',
      'keyboard_acc_1' => 'keyboard accessory 1',
      'keyboard_acc_1_price' => 200,
      'keyboard_acc_2' => 'keyboard accessory 2',
      'keyboard_acc_2_price' => 300,

      'mouse' => 'test mouse',
      'mouse_price' => 100,

      'speakers' => 'test speakers',
      'speakers_price' => 200,

      'wifi' => 'test wifi',
      'wifi_price' => 300,

      'headset_1' => 'test headset 1',
      'headset_1_price' => 400,
      'headset_2' => 'test headset 2',
      'headset_2_price' => 500,

      'mic' => 'test mic',
      'mic_price' => 600,
      'mic_acc' => 'mic accessory',
      'mic_acc_price' => 700,

      'audio_interface' => 'test interface',
      'audio_interface_price' => 100,
      'equalizer' => 'test eq',
      'equalizer_price' => 200,
      'amplifier' => 'test amp',
      'amplifier_price' => 300,

      'created_at' => '2000-12-31 23:10:00',
      'updated_at' => '2000-12-31 23:10:00',
      'deleted_at' => null,
    ];


    $actual = array_column($response['data'], null, 'id')[$this->pcSetup_id] ?? false;

    $this->assertEquals($expected, $actual);

    $this->setup_clear();
  }
}
