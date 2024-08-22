<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\RefreshableAutoIncrements;

/**
 * @OA\Schema(
 *   @OA\Property(property="id", type="integer", format="int64", example=1),
 *   @OA\Property(property="label", type="string"),
 *
 *   @OA\Property(property="isCurrent", type="boolean", default=false, example=false),
 *   @OA\Property(property="isFuture", type="boolean", default=false, example=false),
 *   @OA\Property(property="isServer", type="boolean", default=false, example=false),
 *
 *   @OA\Property(property="cpu", type="string"),
 *   @OA\Property(property="cpuPrice", type="integer", format="int64"),
 *   @OA\Property(property="cpuSub", type="string"),
 *   @OA\Property(property="cpuSub2", type="string"),
 *
 *   @OA\Property(property="ram", type="string"),
 *   @OA\Property(property="ramPrice", type="integer", format="int64"),
 *   @OA\Property(property="ramSub", type="string"),
 *
 *   @OA\Property(property="gpu", type="string"),
 *   @OA\Property(property="gpuPrice", type="integer", format="int64"),
 *   @OA\Property(property="gpuSub", type="string"),
 *
 *   @OA\Property(property="motherboard", type="string"),
 *   @OA\Property(property="motherboardPrice", type="integer", format="int64"),
 *
 *   @OA\Property(property="psu", type="string"),
 *   @OA\Property(property="psuPrice", type="integer", format="int64"),
 *
 *   @OA\Property(property="cooler", type="string"),
 *   @OA\Property(property="coolerPrice", type="integer", format="int64"),
 *   @OA\Property(property="coolerAcc", type="string"),
 *   @OA\Property(property="coolerAccPrice", type="integer", format="int64"),
 *
 *   @OA\Property(property="ssd1", type="string"),
 *   @OA\Property(property="ssd1Price", type="integer", format="int64"),
 *   @OA\Property(property="ssd2", type="string"),
 *   @OA\Property(property="ssd2Price", type="integer", format="int64"),
 *   @OA\Property(property="ssd3", type="string"),
 *   @OA\Property(property="ssd3Price", type="integer", format="int64"),
 *   @OA\Property(property="ssd4", type="string"),
 *   @OA\Property(property="ssd4Price", type="integer", format="int64"),
 *
 *   @OA\Property(property="hdd1", type="string"),
 *   @OA\Property(property="hdd1Price", type="integer", format="int64"),
 *   @OA\Property(property="hdd2", type="string"),
 *   @OA\Property(property="hdd2Price", type="integer", format="int64"),
 *   @OA\Property(property="hdd3", type="string"),
 *   @OA\Property(property="hdd3Price", type="integer", format="int64"),
 *   @OA\Property(property="hdd4", type="string"),
 *   @OA\Property(property="hdd4Price", type="integer", format="int64"),
 *
 *   @OA\Property(property="case", type="string"),
 *   @OA\Property(property="casePrice", type="integer", format="int64"),
 *
 *   @OA\Property(property="caseFans1", type="string"),
 *   @OA\Property(property="caseFans1Price", type="integer", format="int64"),
 *   @OA\Property(property="caseFans2", type="string"),
 *   @OA\Property(property="caseFans2Price", type="integer", format="int64"),
 *   @OA\Property(property="caseFans3", type="string"),
 *   @OA\Property(property="caseFans3Price", type="integer", format="int64"),
 *   @OA\Property(property="caseFans4", type="string"),
 *   @OA\Property(property="caseFans4Price", type="integer", format="int64"),
 *
 *   @OA\Property(property="monitor", type="string"),
 *   @OA\Property(property="monitorPrice", type="integer", format="int64"),
 *   @OA\Property(property="monitorSub", type="string"),
 *
 *   @OA\Property(property="monitorAcc1", type="string"),
 *   @OA\Property(property="monitorAcc1Price", type="integer", format="int64"),
 *   @OA\Property(property="monitorAcc2", type="string"),
 *   @OA\Property(property="monitorAcc2Price", type="integer", format="int64"),
 *
 *   @OA\Property(property="keyboard", type="string"),
 *   @OA\Property(property="keyboardPrice", type="integer", format="int64"),
 *   @OA\Property(property="keyboardSub", type="string"),
 *   @OA\Property(property="keyboardSub2", type="string"),
 *
 *   @OA\Property(property="keyboardAcc1", type="string"),
 *   @OA\Property(property="keyboardAcc1Price", type="integer", format="int64"),
 *   @OA\Property(property="keyboardAcc2", type="string"),
 *   @OA\Property(property="keyboardAcc2Price", type="integer", format="int64"),
 *
 *   @OA\Property(property="mouse", type="string"),
 *   @OA\Property(property="mousePrice", type="integer", format="int64"),
 *
 *   @OA\Property(property="speakers", type="string"),
 *   @OA\Property(property="speakersPrice", type="integer", format="int64"),
 *
 *   @OA\Property(property="wifi", type="string"),
 *   @OA\Property(property="wifiPrice", type="integer", format="int64"),
 *
 *   @OA\Property(property="headset1", type="string"),
 *   @OA\Property(property="headset1Price", type="integer", format="int64"),
 *   @OA\Property(property="headset2", type="string"),
 *   @OA\Property(property="headset2Price", type="integer", format="int64"),
 *
 *   @OA\Property(property="mic", type="string"),
 *   @OA\Property(property="micPrice", type="integer", format="int64"),
 *   @OA\Property(property="micAcc", type="string"),
 *   @OA\Property(property="micAccPrice", type="integer", format="int64"),
 *
 *   @OA\Property(property="audioInterface", type="string"),
 *   @OA\Property(property="audioInterfacePrice", type="integer", format="int64"),
 *
 *   @OA\Property(property="equalizer", type="string"),
 *   @OA\Property(property="equalizerPrice", type="integer", format="int64"),
 *
 *   @OA\Property(property="amplifier", type="string"),
 *   @OA\Property(property="amplifierPrice", type="string"),
 *
 *   @OA\Property(property="createdAt", type="string", example="2023-05-21 21:05:57"),
 *   @OA\Property(property="updatedAt", type="string", example="2023-05-21 21:05:57"),
 *   @OA\Property(property="deletedAt", type="string", example="2023-05-21 21:05:57"),
 * )
 */
class PCSetup extends Model {

  use RefreshableAutoIncrements, SoftDeletes;

  protected $table = 'pc_setups';

  protected $fillable = [
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
  ];

  protected $hidden = [];

  protected $casts = [
    'created_at' => 'datetime:Y-m-d H:i:s',
    'updated_at' => 'datetime:Y-m-d H:i:s',
  ];
}
