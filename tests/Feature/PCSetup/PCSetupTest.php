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
          'isCurrent',
          'isFuture',
          'isServer',
          'cpu',
          'cpuPrice',
          'cpuSub',
          'cpuSub2',
          'ram',
          'ramPrice',
          'ramSub',
          'gpu',
          'gpuPrice',
          'gpuSub',
          'motherboard',
          'motherboardPrice',
          'psu',
          'psuPrice',
          'cooler',
          'coolerPrice',
          'coolerAcc',
          'coolerAccPrice',
          'ssd1',
          'ssd1Price',
          'ssd2',
          'ssd2Price',
          'ssd3',
          'ssd3Price',
          'ssd4',
          'ssd4Price',
          'hdd1',
          'hdd1Price',
          'hdd2',
          'hdd2Price',
          'hdd3',
          'hdd3Price',
          'hdd4',
          'hdd4Price',
          'case',
          'casePrice',
          'caseFans1',
          'caseFans1Price',
          'caseFans2',
          'caseFans2Price',
          'caseFans3',
          'caseFans3Price',
          'caseFans4',
          'caseFans4Price',
          'monitor',
          'monitorPrice',
          'monitorSub',
          'monitorAcc1',
          'monitorAcc1Price',
          'monitorAcc2',
          'monitorAcc2Price',
          'keyboard',
          'keyboardPrice',
          'keyboardSub',
          'keyboardSub2',
          'keyboardAcc1',
          'keyboardAcc1Price',
          'keyboardAcc2',
          'keyboardAcc2Price',
          'mouse',
          'mousePrice',
          'speakers',
          'speakersPrice',
          'wifi',
          'wifiPrice',
          'headset1',
          'headset1Price',
          'headset2',
          'headset2Price',
          'mic',
          'micPrice',
          'micAcc',
          'micAccPrice',
          'audioInterface',
          'audioInterfacePrice',
          'equalizer',
          'equalizerPrice',
          'amplifier',
          'amplifierPrice',
          'createdAt',
          'updatedAt',
        ]],
      ]);

    $expected = [
      'id' => $this->pcSetup_id,
      'label' => $this->pcSetup_label,

      'isCurrent' => false,
      'isFuture' => true,
      'isServer' => false,

      'cpu' => 'test cpu',
      'cpuPrice' => 100,
      'cpuSub' => 'cpu subtext',
      'cpuSub2' => 'cpu subtext 2',

      'ram' => 'test ram',
      'ramPrice' => 200,
      'ramSub' => 'ram subtext',

      'gpu' => 'test gpu',
      'gpuPrice' => 300,
      'gpuSub' => 'gpu subtext',

      'motherboard' => 'test motherboard',
      'motherboardPrice' => 400,

      'psu' => 'test psu',
      'psuPrice' => 500,

      'cooler' => 'test cooler',
      'coolerPrice' => 600,
      'coolerAcc' => 'cooler accessory',
      'coolerAccPrice' => 700,

      'ssd1' => 'ssd 1',
      'ssd1Price' => 100,
      'ssd2' => 'ssd 2',
      'ssd2Price' => 200,
      'ssd3' => 'ssd 3',
      'ssd3Price' => 300,
      'ssd4' => 'ssd 4',
      'ssd4Price' => 400,

      'hdd1' => 'hdd 1',
      'hdd1Price' => 500,
      'hdd2' => 'hdd 2',
      'hdd2Price' => 600,
      'hdd3' => 'hdd 3',
      'hdd3Price' => 700,
      'hdd4' => 'hdd 4',
      'hdd4Price' => 800,

      'case' => 'test case',
      'casePrice' => 100,
      'caseFans1' => 'case fans 1',
      'caseFans1Price' => 200,
      'caseFans2' => 'case fans 2',
      'caseFans2Price' => 300,
      'caseFans3' => 'case fans 3',
      'caseFans3Price' => 400,
      'caseFans4' => 'case fans 4',
      'caseFans4Price' => 500,

      'monitor' => 'test monitor',
      'monitorPrice' => 100,
      'monitorSub' => 'monitor subtext',
      'monitorAcc1' => 'monitor accessory 1',
      'monitorAcc1Price' => 200,
      'monitorAcc2' => 'monitor accessory 2',
      'monitorAcc2Price' => 300,

      'keyboard' => 'test keyboard',
      'keyboardPrice' => 100,
      'keyboardSub' => 'keyboard subtext 1',
      'keyboardSub2' => 'keyboard subtext 2',
      'keyboardAcc1' => 'keyboard accessory 1',
      'keyboardAcc1Price' => 200,
      'keyboardAcc2' => 'keyboard accessory 2',
      'keyboardAcc2Price' => 300,

      'mouse' => 'test mouse',
      'mousePrice' => 100,

      'speakers' => 'test speakers',
      'speakersPrice' => 200,

      'wifi' => 'test wifi',
      'wifiPrice' => 300,

      'headset1' => 'test headset 1',
      'headset1Price' => 400,
      'headset2' => 'test headset 2',
      'headset2Price' => 500,

      'mic' => 'test mic',
      'micPrice' => 600,
      'micAcc' => 'mic accessory',
      'micAccPrice' => 700,

      'audioInterface' => 'test interface',
      'audioInterfacePrice' => 100,
      'equalizer' => 'test eq',
      'equalizerPrice' => 200,
      'amplifier' => 'test amp',
      'amplifierPrice' => 300,

      'createdAt' => '2000-12-31 23:10:00',
      'updatedAt' => '2000-12-31 23:10:00',
      'deletedAt' => null,
    ];


    $actual = array_column($response['data'], null, 'id')[$this->pcSetup_id] ?? false;

    $this->assertEquals($expected, $actual);

    $this->setup_clear();
  }
}
