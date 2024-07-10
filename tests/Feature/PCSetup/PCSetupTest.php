<?php

namespace Tests\Feature\PCSetup;

use Tests\BaseTestCase;

use App\Models\PCSetup;

class PCSetupTest extends BaseTestCase {

  private $pcSetup_id_1 = 99999;
  private $pcSetup_id_2 = 99998;
  private $pcSetup_id_3 = 99997;
  private $pcSetup_id_4 = 99996;
  private $pcSetup_label = "Test Run -- Test Data";

  private $pcSetup_is_current_1 = false;
  private $pcSetup_is_current_2 = true;
  private $pcSetup_is_current_3 = false;
  private $pcSetup_is_future = false;
  private $pcSetup_is_server = false;

  private function setup_config() {
    // Clearing possible duplicate data
    $this->setup_clear();

    PCSetup::insert([
      [
        'id' => $this->pcSetup_id_1,
        'label' => $this->pcSetup_label,

        'is_current' => $this->pcSetup_is_current_1,
        'is_future' => $this->pcSetup_is_future,
        'is_server' => $this->pcSetup_is_server,

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
      ]
    ]);

    PCSetup::insert([
      [
        'id' => $this->pcSetup_id_2,
        'label' => $this->pcSetup_label,

        'is_current' => $this->pcSetup_is_current_2,
        'is_future' => false,
        'is_server' => false,
      ], [
        'id' => $this->pcSetup_id_3,
        'label' => $this->pcSetup_label,

        'is_current' => $this->pcSetup_is_current_3,
        'is_future' => false,
        'is_server' => false,
      ], [
        'id' => $this->pcSetup_id_4,
        'label' => $this->pcSetup_label,

        'is_current' => false,
        'is_future' => false,
        'is_server' => $this->pcSetup_is_server,
      ]
    ]);
  }

  private function setup_clear() {
    PCSetup::whereIn('id', [
      $this->pcSetup_id_1,
      $this->pcSetup_id_2,
      $this->pcSetup_id_3,
      $this->pcSetup_id_4,
    ])
      ->forceDelete();
  }

  public function test_should_get_all_data() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->get('/api/pc-setups');

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

    $this->setup_clear();
  }

  public function test_should_get_single_data() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->get('/api/pc-setups/' . $this->pcSetup_id_1);

    $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
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
        ],
      ]);

    $expected = [
      'id' => $this->pcSetup_id_1,
      'label' => $this->pcSetup_label,

      'isCurrent' => false,
      'isFuture' => false,
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

    $this->assertEquals($expected, $response['data']);
  }

  public function test_should_add_data_successfully() {
    $data = [
      'label' => 'testing data pc-setup label',

      'is_current' => false,
      'is_future' => false,
      'is_server' => false,

      'cpu' => 'test cpu',
      'cpu_price' => 250_000,
      'cpu_sub' => 'cpu subtext',
      'cpu_sub2' => 'cpu subtext 2',

      'ram' => 'test ram',
      'ram_price' => 250_000,
      'ram_sub' => 'ram subtext',

      'gpu' => 'test gpu',
      'gpu_price' => 250_000,
      'gpu_sub' => 'gpu subtext',

      'motherboard' => 'test motherboard',
      'motherboard_price' => 250_000,

      'psu' => 'test psu',
      'psu_price' => 250_000,

      'cooler' => 'test cooler',
      'cooler_price' => 250_000,
      'cooler_acc' => 'cooler accessory',
      'cooler_acc_price' => 250_000,

      'ssd_1' => 'ssd 1',
      'ssd_1_price' => 250_000,
      'ssd_2' => 'ssd 2',
      'ssd_2_price' => 250_000,
      'ssd_3' => 'ssd 3',
      'ssd_3_price' => 250_000,
      'ssd_4' => 'ssd 4',
      'ssd_4_price' => 250_000,

      'hdd_1' => 'hdd 1',
      'hdd_1_price' => 250_000,
      'hdd_2' => 'hdd 2',
      'hdd_2_price' => 250_000,
      'hdd_3' => 'hdd 3',
      'hdd_3_price' => 250_000,
      'hdd_4' => 'hdd 4',
      'hdd_4_price' => 250_000,

      'case' => 'test case',
      'case_price' => 250_000,
      'case_fans_1' => 'case fans 1',
      'case_fans_1_price' => 250_000,
      'case_fans_2' => 'case fans 2',
      'case_fans_2_price' => 250_000,
      'case_fans_3' => 'case fans 3',
      'case_fans_3_price' => 250_000,
      'case_fans_4' => 'case fans 4',
      'case_fans_4_price' => 250_000,

      'monitor' => 'test monitor',
      'monitor_price' => 250_000,
      'monitor_sub' => 'monitor subtext',
      'monitor_acc_1' => 'monitor accessory 1',
      'monitor_acc_1_price' => 250_000,
      'monitor_acc_2' => 'monitor accessory 2',
      'monitor_acc_2_price' => 250_000,

      'keyboard' => 'test keyboard',
      'keyboard_price' => 250_000,
      'keyboard_sub' => 'keyboard subtext 1',
      'keyboard_sub2' => 'keyboard subtext 2',
      'keyboard_acc_1' => 'keyboard accessory 1',
      'keyboard_acc_1_price' => 250_000,
      'keyboard_acc_2' => 'keyboard accessory 2',
      'keyboard_acc_2_price' => 250_000,

      'mouse' => 'test mouse',
      'mouse_price' => 250_000,

      'speakers' => 'test speakers',
      'speakers_price' => 250_000,

      'wifi' => 'test wifi',
      'wifi_price' => 250_000,

      'headset_1' => 'test headset 1',
      'headset_1_price' => 250_000,
      'headset_2' => 'test headset 2',
      'headset_2_price' => 250_000,

      'mic' => 'test mic',
      'mic_price' => 250_000,
      'mic_acc' => 'mic accessory',
      'mic_acc_price' => 250_000,

      'audio_interface' => 'test interface',
      'audio_interface_price' => 250_000,
      'equalizer' => 'test eq',
      'equalizer_price' => 250_000,
      'amplifier' => 'test amp',
      'amplifier_price' => 250_000,
    ];

    // Clearing possible duplicate data
    PCSetup::where('label', 'testing data pc-setup label')->delete();

    $response = $this->withoutMiddleware()->post('/api/pc-setups', $data);

    $response->assertStatus(200);

    $actual = PCSetup::where('label', 'testing data pc-setup label')
      ->first()
      ->toArray();

    $this->assertNotNull($actual);
    $this->assertEquals($data['label'], $actual['label']);
    $this->assertEquals($data['is_current'], $actual['is_current']);
    $this->assertEquals($data['is_future'], $actual['is_future']);
    $this->assertEquals($data['is_server'], $actual['is_server']);
    $this->assertEquals($data['cpu'], $actual['cpu']);
    $this->assertEquals($data['cpu_price'], $actual['cpu_price']);
    $this->assertEquals($data['cpu_sub'], $actual['cpu_sub']);
    $this->assertEquals($data['cpu_sub2'], $actual['cpu_sub2']);
    $this->assertEquals($data['ram'], $actual['ram']);
    $this->assertEquals($data['ram_price'], $actual['ram_price']);
    $this->assertEquals($data['ram_sub'], $actual['ram_sub']);
    $this->assertEquals($data['gpu'], $actual['gpu']);
    $this->assertEquals($data['gpu_price'], $actual['gpu_price']);
    $this->assertEquals($data['gpu_sub'], $actual['gpu_sub']);
    $this->assertEquals($data['motherboard'], $actual['motherboard']);
    $this->assertEquals($data['motherboard_price'], $actual['motherboard_price']);
    $this->assertEquals($data['psu'], $actual['psu']);
    $this->assertEquals($data['psu_price'], $actual['psu_price']);
    $this->assertEquals($data['cooler'], $actual['cooler']);
    $this->assertEquals($data['cooler_price'], $actual['cooler_price']);
    $this->assertEquals($data['cooler_acc'], $actual['cooler_acc']);
    $this->assertEquals($data['cooler_acc_price'], $actual['cooler_acc_price']);
    $this->assertEquals($data['ssd_1'], $actual['ssd_1']);
    $this->assertEquals($data['ssd_1_price'], $actual['ssd_1_price']);
    $this->assertEquals($data['ssd_2'], $actual['ssd_2']);
    $this->assertEquals($data['ssd_2_price'], $actual['ssd_2_price']);
    $this->assertEquals($data['ssd_3'], $actual['ssd_3']);
    $this->assertEquals($data['ssd_3_price'], $actual['ssd_3_price']);
    $this->assertEquals($data['ssd_4'], $actual['ssd_4']);
    $this->assertEquals($data['ssd_4_price'], $actual['ssd_4_price']);
    $this->assertEquals($data['hdd_1'], $actual['hdd_1']);
    $this->assertEquals($data['hdd_1_price'], $actual['hdd_1_price']);
    $this->assertEquals($data['hdd_2'], $actual['hdd_2']);
    $this->assertEquals($data['hdd_2_price'], $actual['hdd_2_price']);
    $this->assertEquals($data['hdd_3'], $actual['hdd_3']);
    $this->assertEquals($data['hdd_3_price'], $actual['hdd_3_price']);
    $this->assertEquals($data['hdd_4'], $actual['hdd_4']);
    $this->assertEquals($data['hdd_4_price'], $actual['hdd_4_price']);
    $this->assertEquals($data['case'], $actual['case']);
    $this->assertEquals($data['case_price'], $actual['case_price']);
    $this->assertEquals($data['case_fans_1'], $actual['case_fans_1']);
    $this->assertEquals($data['case_fans_1_price'], $actual['case_fans_1_price']);
    $this->assertEquals($data['case_fans_2'], $actual['case_fans_2']);
    $this->assertEquals($data['case_fans_2_price'], $actual['case_fans_2_price']);
    $this->assertEquals($data['case_fans_3'], $actual['case_fans_3']);
    $this->assertEquals($data['case_fans_3_price'], $actual['case_fans_3_price']);
    $this->assertEquals($data['case_fans_4'], $actual['case_fans_4']);
    $this->assertEquals($data['case_fans_4_price'], $actual['case_fans_4_price']);
    $this->assertEquals($data['keyboard'], $actual['keyboard']);
    $this->assertEquals($data['keyboard_price'], $actual['keyboard_price']);
    $this->assertEquals($data['keyboard_sub'], $actual['keyboard_sub']);
    $this->assertEquals($data['keyboard_sub2'], $actual['keyboard_sub2']);
    $this->assertEquals($data['keyboard_acc_1'], $actual['keyboard_acc_1']);
    $this->assertEquals($data['keyboard_acc_1_price'], $actual['keyboard_acc_1_price']);
    $this->assertEquals($data['keyboard_acc_2'], $actual['keyboard_acc_2']);
    $this->assertEquals($data['keyboard_acc_2_price'], $actual['keyboard_acc_2_price']);
    $this->assertEquals($data['mouse'], $actual['mouse']);
    $this->assertEquals($data['mouse_price'], $actual['mouse_price']);
    $this->assertEquals($data['speakers'], $actual['speakers']);
    $this->assertEquals($data['speakers_price'], $actual['speakers_price']);
    $this->assertEquals($data['wifi'], $actual['wifi']);
    $this->assertEquals($data['wifi_price'], $actual['wifi_price']);
    $this->assertEquals($data['headset_1'], $actual['headset_1']);
    $this->assertEquals($data['headset_1_price'], $actual['headset_1_price']);
    $this->assertEquals($data['headset_2'], $actual['headset_2']);
    $this->assertEquals($data['headset_2_price'], $actual['headset_2_price']);
    $this->assertEquals($data['mic'], $actual['mic']);
    $this->assertEquals($data['mic_price'], $actual['mic_price']);
    $this->assertEquals($data['mic_acc'], $actual['mic_acc']);
    $this->assertEquals($data['mic_acc_price'], $actual['mic_acc_price']);
    $this->assertEquals($data['audio_interface'], $actual['audio_interface']);
    $this->assertEquals($data['audio_interface_price'], $actual['audio_interface_price']);
    $this->assertEquals($data['equalizer'], $actual['equalizer']);
    $this->assertEquals($data['equalizer_price'], $actual['equalizer_price']);
    $this->assertEquals($data['amplifier'], $actual['amplifier']);
    $this->assertEquals($data['amplifier_price'], $actual['amplifier_price']);

    // Clearing test data
    PCSetup::where('label', 'testing data pc-setup label')->delete();
  }

  public function test_should_not_add_data_on_form_errors() {
    $data = [
      'label' => 'testing data pc-setup label, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',

      'cpu' => 'test cpu, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'cpu_price' => 250_001,
      'cpu_sub' => 'cpu subtext, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'cpu_sub2' => 'cpu subtext 2, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',

      'ram' => 'test ram, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'ram_price' => 250_001,
      'ram_sub' => 'ram subtext, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',

      'gpu' => 'test gpu, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'gpu_price' => 250_001,
      'gpu_sub' => 'gpu subtext, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',

      'motherboard' => 'test motherboard, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'motherboard_price' => 250_001,

      'psu' => 'test psu, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'psu_price' => 250_001,

      'cooler' => 'test cooler, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'cooler_price' => 250_001,
      'cooler_acc' => 'cooler accessory, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'cooler_acc_price' => 250_001,

      'ssd_1' => 'ssd 1, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'ssd_1_price' => 250_001,
      'ssd_2' => 'ssd 2, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'ssd_2_price' => 250_001,
      'ssd_3' => 'ssd 3, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'ssd_3_price' => 250_001,
      'ssd_4' => 'ssd 4, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'ssd_4_price' => 250_001,

      'hdd_1' => 'hdd 1, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'hdd_1_price' => 250_001,
      'hdd_2' => 'hdd 2, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'hdd_2_price' => 250_001,
      'hdd_3' => 'hdd 3, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'hdd_3_price' => 250_001,
      'hdd_4' => 'hdd 4, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'hdd_4_price' => 250_001,

      'case' => 'test case, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'case_price' => 250_001,
      'case_fans_1' => 'case fans 1, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'case_fans_1_price' => 250_001,
      'case_fans_2' => 'case fans 2, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'case_fans_2_price' => 250_001,
      'case_fans_3' => 'case fans 3, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'case_fans_3_price' => 250_001,
      'case_fans_4' => 'case fans 4, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'case_fans_4_price' => 250_001,

      'monitor' => 'test monitor, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'monitor_price' => 250_001,
      'monitor_sub' => 'monitor subtext, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'monitor_acc_1' => 'monitor accessory 1, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'monitor_acc_1_price' => 250_001,
      'monitor_acc_2' => 'monitor accessory 2, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'monitor_acc_2_price' => 250_001,

      'keyboard' => 'test keyboard, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'keyboard_price' => 250_001,
      'keyboard_sub' => 'keyboard subtext 1, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'keyboard_sub2' => 'keyboard subtext 2, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'keyboard_acc_1' => 'keyboard accessory 1, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'keyboard_acc_1_price' => 250_001,
      'keyboard_acc_2' => 'keyboard accessory 2, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'keyboard_acc_2_price' => 250_001,

      'mouse' => 'test mouse, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'mouse_price' => 250_001,

      'speakers' => 'test speakers, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'speakers_price' => 250_001,

      'wifi' => 'test wifi, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'wifi_price' => 250_001,

      'headset_1' => 'test headset 1, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'headset_1_price' => 250_001,
      'headset_2' => 'test headset 2, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'headset_2_price' => 250_001,

      'mic' => 'test mic, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'mic_price' => 250_001,
      'mic_acc' => 'mic accessory, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'mic_acc_price' => 250_001,

      'audio_interface' => 'test interface, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'audio_interface_price' => 250_001,
      'equalizer' => 'test eq, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'equalizer_price' => 250_001,
      'amplifier' => 'test am, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEANp',
      'amplifier_price' => 250_001,
    ];

    // Clearing possible duplicate data
    PCSetup::where('label', 'testing data pc-setup label')->delete();

    $response = $this->withoutMiddleware()->post('/api/pc-setups', $data);

    $response->assertStatus(401)
      ->assertJsonStructure([
        'data' => [
          'label',
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
        ],
      ]);
  }

  public function test_should_edit_data_successfully() {
    $this->setup_config();

    $test_label = 'new testing data pc-setup label';
    $data = [
      'label' => $test_label,

      'is_current' => true,
      'is_future' => false,
      'is_server' => true,

      'cpu' => 'new test cpu',
      'cpu_price' => 250_000,
      'cpu_sub' => 'new cpu subtext',
      'cpu_sub2' => 'new cpu subtext 2',

      'ram' => 'new test ram',
      'ram_price' => 250_000,
      'ram_sub' => 'new ram subtext',

      'gpu' => 'new test gpu',
      'gpu_price' => 250_000,
      'gpu_sub' => 'new gpu subtext',

      'motherboard' => 'new test motherboard',
      'motherboard_price' => 250_000,

      'psu' => 'new test psu',
      'psu_price' => 250_000,

      'cooler' => 'new test cooler',
      'cooler_price' => 250_000,
      'cooler_acc' => 'new cooler accessory',
      'cooler_acc_price' => 250_000,

      'ssd_1' => 'new ssd 1',
      'ssd_1_price' => 250_000,
      'ssd_2' => 'new ssd 2',
      'ssd_2_price' => 250_000,
      'ssd_3' => 'new ssd 3',
      'ssd_3_price' => 250_000,
      'ssd_4' => 'new ssd 4',
      'ssd_4_price' => 250_000,

      'hdd_1' => 'new hdd 1',
      'hdd_1_price' => 250_000,
      'hdd_2' => 'new hdd 2',
      'hdd_2_price' => 250_000,
      'hdd_3' => 'new hdd 3',
      'hdd_3_price' => 250_000,
      'hdd_4' => 'new hdd 4',
      'hdd_4_price' => 250_000,

      'case' => 'new test case',
      'case_price' => 250_000,
      'case_fans_1' => 'new case fans 1',
      'case_fans_1_price' => 250_000,
      'case_fans_2' => 'new case fans 2',
      'case_fans_2_price' => 250_000,
      'case_fans_3' => 'new case fans 3',
      'case_fans_3_price' => 250_000,
      'case_fans_4' => 'new case fans 4',
      'case_fans_4_price' => 250_000,

      'monitor' => 'new test monitor',
      'monitor_price' => 250_000,
      'monitor_sub' => 'new monitor subtext',
      'monitor_acc_1' => 'new monitor accessory 1',
      'monitor_acc_1_price' => 250_000,
      'monitor_acc_2' => 'new monitor accessory 2',
      'monitor_acc_2_price' => 250_000,

      'keyboard' => 'new test keyboard',
      'keyboard_price' => 250_000,
      'keyboard_sub' => 'new keyboard subtext 1',
      'keyboard_sub2' => 'new keyboard subtext 2',
      'keyboard_acc_1' => 'new keyboard accessory 1',
      'keyboard_acc_1_price' => 250_000,
      'keyboard_acc_2' => 'new keyboard accessory 2',
      'keyboard_acc_2_price' => 250_000,

      'mouse' => 'new test mouse',
      'mouse_price' => 250_000,

      'speakers' => 'new test speakers',
      'speakers_price' => 250_000,

      'wifi' => 'new test wifi',
      'wifi_price' => 250_000,

      'headset_1' => 'new test headset 1',
      'headset_1_price' => 250_000,
      'headset_2' => 'new test headset 2',
      'headset_2_price' => 250_000,

      'mic' => 'new test mic',
      'mic_price' => 250_000,
      'mic_acc' => 'new mic accessory',
      'mic_acc_price' => 250_000,

      'audio_interface' => 'new test interface',
      'audio_interface_price' => 250_000,
      'equalizer' => 'new test eq',
      'equalizer_price' => 250_000,
      'amplifier' => 'new test amp',
      'amplifier_price' => 250_000,
    ];

    // Clearing possible duplicate data
    PCSetup::where('label', 'testing data pc-setup label')->delete();

    $response = $this->withoutMiddleware()->put('/api/pc-setups/' . $this->pcSetup_id_1, $data);

    $response->assertStatus(200);

    $actual = PCSetup::where('id', $this->pcSetup_id_1)
      ->where('label', $test_label)
      ->first()
      ->toArray();

    $this->assertEquals($data['label'], $actual['label']);
    $this->assertEquals($data['is_current'], $actual['is_current']);
    $this->assertEquals($data['is_future'], $actual['is_future']);
    $this->assertEquals($data['is_server'], $actual['is_server']);
    $this->assertEquals($data['cpu'], $actual['cpu']);
    $this->assertEquals($data['cpu_price'], $actual['cpu_price']);
    $this->assertEquals($data['cpu_sub'], $actual['cpu_sub']);
    $this->assertEquals($data['cpu_sub2'], $actual['cpu_sub2']);
    $this->assertEquals($data['ram'], $actual['ram']);
    $this->assertEquals($data['ram_price'], $actual['ram_price']);
    $this->assertEquals($data['ram_sub'], $actual['ram_sub']);
    $this->assertEquals($data['gpu'], $actual['gpu']);
    $this->assertEquals($data['gpu_price'], $actual['gpu_price']);
    $this->assertEquals($data['gpu_sub'], $actual['gpu_sub']);
    $this->assertEquals($data['motherboard'], $actual['motherboard']);
    $this->assertEquals($data['motherboard_price'], $actual['motherboard_price']);
    $this->assertEquals($data['psu'], $actual['psu']);
    $this->assertEquals($data['psu_price'], $actual['psu_price']);
    $this->assertEquals($data['cooler'], $actual['cooler']);
    $this->assertEquals($data['cooler_price'], $actual['cooler_price']);
    $this->assertEquals($data['cooler_acc'], $actual['cooler_acc']);
    $this->assertEquals($data['cooler_acc_price'], $actual['cooler_acc_price']);
    $this->assertEquals($data['ssd_1'], $actual['ssd_1']);
    $this->assertEquals($data['ssd_1_price'], $actual['ssd_1_price']);
    $this->assertEquals($data['ssd_2'], $actual['ssd_2']);
    $this->assertEquals($data['ssd_2_price'], $actual['ssd_2_price']);
    $this->assertEquals($data['ssd_3'], $actual['ssd_3']);
    $this->assertEquals($data['ssd_3_price'], $actual['ssd_3_price']);
    $this->assertEquals($data['ssd_4'], $actual['ssd_4']);
    $this->assertEquals($data['ssd_4_price'], $actual['ssd_4_price']);
    $this->assertEquals($data['hdd_1'], $actual['hdd_1']);
    $this->assertEquals($data['hdd_1_price'], $actual['hdd_1_price']);
    $this->assertEquals($data['hdd_2'], $actual['hdd_2']);
    $this->assertEquals($data['hdd_2_price'], $actual['hdd_2_price']);
    $this->assertEquals($data['hdd_3'], $actual['hdd_3']);
    $this->assertEquals($data['hdd_3_price'], $actual['hdd_3_price']);
    $this->assertEquals($data['hdd_4'], $actual['hdd_4']);
    $this->assertEquals($data['hdd_4_price'], $actual['hdd_4_price']);
    $this->assertEquals($data['case'], $actual['case']);
    $this->assertEquals($data['case_price'], $actual['case_price']);
    $this->assertEquals($data['case_fans_1'], $actual['case_fans_1']);
    $this->assertEquals($data['case_fans_1_price'], $actual['case_fans_1_price']);
    $this->assertEquals($data['case_fans_2'], $actual['case_fans_2']);
    $this->assertEquals($data['case_fans_2_price'], $actual['case_fans_2_price']);
    $this->assertEquals($data['case_fans_3'], $actual['case_fans_3']);
    $this->assertEquals($data['case_fans_3_price'], $actual['case_fans_3_price']);
    $this->assertEquals($data['case_fans_4'], $actual['case_fans_4']);
    $this->assertEquals($data['case_fans_4_price'], $actual['case_fans_4_price']);
    $this->assertEquals($data['keyboard'], $actual['keyboard']);
    $this->assertEquals($data['keyboard_price'], $actual['keyboard_price']);
    $this->assertEquals($data['keyboard_sub'], $actual['keyboard_sub']);
    $this->assertEquals($data['keyboard_sub2'], $actual['keyboard_sub2']);
    $this->assertEquals($data['keyboard_acc_1'], $actual['keyboard_acc_1']);
    $this->assertEquals($data['keyboard_acc_1_price'], $actual['keyboard_acc_1_price']);
    $this->assertEquals($data['keyboard_acc_2'], $actual['keyboard_acc_2']);
    $this->assertEquals($data['keyboard_acc_2_price'], $actual['keyboard_acc_2_price']);
    $this->assertEquals($data['mouse'], $actual['mouse']);
    $this->assertEquals($data['mouse_price'], $actual['mouse_price']);
    $this->assertEquals($data['speakers'], $actual['speakers']);
    $this->assertEquals($data['speakers_price'], $actual['speakers_price']);
    $this->assertEquals($data['wifi'], $actual['wifi']);
    $this->assertEquals($data['wifi_price'], $actual['wifi_price']);
    $this->assertEquals($data['headset_1'], $actual['headset_1']);
    $this->assertEquals($data['headset_1_price'], $actual['headset_1_price']);
    $this->assertEquals($data['headset_2'], $actual['headset_2']);
    $this->assertEquals($data['headset_2_price'], $actual['headset_2_price']);
    $this->assertEquals($data['mic'], $actual['mic']);
    $this->assertEquals($data['mic_price'], $actual['mic_price']);
    $this->assertEquals($data['mic_acc'], $actual['mic_acc']);
    $this->assertEquals($data['mic_acc_price'], $actual['mic_acc_price']);
    $this->assertEquals($data['audio_interface'], $actual['audio_interface']);
    $this->assertEquals($data['audio_interface_price'], $actual['audio_interface_price']);
    $this->assertEquals($data['equalizer'], $actual['equalizer']);
    $this->assertEquals($data['equalizer_price'], $actual['equalizer_price']);
    $this->assertEquals($data['amplifier'], $actual['amplifier']);
    $this->assertEquals($data['amplifier_price'], $actual['amplifier_price']);

    // Clearing test data
    PCSetup::where('label', $test_label)->delete();

    $this->setup_clear();
  }

  public function test_should_not_edit_data_on_form_errors() {
    $this->setup_config();

    $data = [
      'label' => 'testing data pc-setup label, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',

      'cpu' => 'test cpu, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'cpu_price' => 250_001,
      'cpu_sub' => 'cpu subtext, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'cpu_sub2' => 'cpu subtext 2, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',

      'ram' => 'test ram, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'ram_price' => 250_001,
      'ram_sub' => 'ram subtext, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',

      'gpu' => 'test gpu, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'gpu_price' => 250_001,
      'gpu_sub' => 'gpu subtext, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',

      'motherboard' => 'test motherboard, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'motherboard_price' => 250_001,

      'psu' => 'test psu, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'psu_price' => 250_001,

      'cooler' => 'test cooler, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'cooler_price' => 250_001,
      'cooler_acc' => 'cooler accessory, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'cooler_acc_price' => 250_001,

      'ssd_1' => 'ssd 1, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'ssd_1_price' => 250_001,
      'ssd_2' => 'ssd 2, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'ssd_2_price' => 250_001,
      'ssd_3' => 'ssd 3, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'ssd_3_price' => 250_001,
      'ssd_4' => 'ssd 4, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'ssd_4_price' => 250_001,

      'hdd_1' => 'hdd 1, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'hdd_1_price' => 250_001,
      'hdd_2' => 'hdd 2, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'hdd_2_price' => 250_001,
      'hdd_3' => 'hdd 3, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'hdd_3_price' => 250_001,
      'hdd_4' => 'hdd 4, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'hdd_4_price' => 250_001,

      'case' => 'test case, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'case_price' => 250_001,
      'case_fans_1' => 'case fans 1, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'case_fans_1_price' => 250_001,
      'case_fans_2' => 'case fans 2, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'case_fans_2_price' => 250_001,
      'case_fans_3' => 'case fans 3, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'case_fans_3_price' => 250_001,
      'case_fans_4' => 'case fans 4, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'case_fans_4_price' => 250_001,

      'monitor' => 'test monitor, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'monitor_price' => 250_001,
      'monitor_sub' => 'monitor subtext, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'monitor_acc_1' => 'monitor accessory 1, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'monitor_acc_1_price' => 250_001,
      'monitor_acc_2' => 'monitor accessory 2, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'monitor_acc_2_price' => 250_001,

      'keyboard' => 'test keyboard, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'keyboard_price' => 250_001,
      'keyboard_sub' => 'keyboard subtext 1, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'keyboard_sub2' => 'keyboard subtext 2, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'keyboard_acc_1' => 'keyboard accessory 1, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'keyboard_acc_1_price' => 250_001,
      'keyboard_acc_2' => 'keyboard accessory 2, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'keyboard_acc_2_price' => 250_001,

      'mouse' => 'test mouse, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'mouse_price' => 250_001,

      'speakers' => 'test speakers, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'speakers_price' => 250_001,

      'wifi' => 'test wifi, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'wifi_price' => 250_001,

      'headset_1' => 'test headset 1, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'headset_1_price' => 250_001,
      'headset_2' => 'test headset 2, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'headset_2_price' => 250_001,

      'mic' => 'test mic, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'mic_price' => 250_001,
      'mic_acc' => 'mic accessory, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'mic_acc_price' => 250_001,

      'audio_interface' => 'test interface, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'audio_interface_price' => 250_001,
      'equalizer' => 'test eq, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEAN',
      'equalizer_price' => 250_001,
      'amplifier' => 'test am, IGXONXUYHAASQJGNAVJFZTYUZTPHWSCUTFVOGKXVMGLLSDUUAGDLQNVOQLNQFEANp',
      'amplifier_price' => 250_001,
    ];

    $response = $this->withoutMiddleware()->put('/api/pc-setups/' . $this->pcSetup_id_1, $data);

    $response->assertStatus(401)
      ->assertJsonStructure([
        'data' => [
          'label',
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
        ],
      ]);

    $this->setup_clear();
  }

  public function test_should_clone_or_duplicate_data_then_return_id() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->post('/api/pc-setups/duplicate/' . $this->pcSetup_id_1);

    $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => ['newID']
      ]);

    $this->assertIsNumeric($response['data']['newID']);

    $this->setup_clear();
  }

  public function test_should_toggle_setup_as_current_and_toggle_others_as_not_current() {
    $this->setup_config();

    $expected = !$this->pcSetup_is_current_1;

    $response = $this->withoutMiddleware()->put('/api/pc-setups/current/' . $this->pcSetup_id_1);

    $response->assertStatus(200);

    $actual_current = PCSetup::where('id', $this->pcSetup_id_1)->first();
    $actual_non_current = PCSetup::whereIn('id', [$this->pcSetup_id_2, $this->pcSetup_id_3])
      ->get();

    $this->assertSame($expected, $actual_current->is_current);

    foreach ($actual_non_current as $value) {
      $this->assertSame(false, $value->is_current);
    }

    $this->setup_clear();
  }

  public function test_should_toggle_setup_as_future() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->put('/api/pc-setups/future/' . $this->pcSetup_id_1);

    $response->assertStatus(200);

    $expected = !$this->pcSetup_is_future;
    $actual = PCSetup::where('id', $this->pcSetup_id_1)->first();

    $this->assertSame($expected, $actual->is_future);

    $this->setup_clear();
  }

  public function test_should_toggle_setup_as_server() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->put('/api/pc-setups/server/' . $this->pcSetup_id_1);

    $response->assertStatus(200);

    $expected = !$this->pcSetup_is_server;
    $actual = PCSetup::where('id', $this->pcSetup_id_1)->first();

    $this->assertSame($expected, $actual->is_server);

    $this->setup_clear();
  }

  public function test_should_not_be_accessible_on_no_auth() {
    $response = $this->get('/api/pc-setups');

    $response->assertStatus(401)
      ->assertJson(['message' => 'Unauthorized']);
  }
}
