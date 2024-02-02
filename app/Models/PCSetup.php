<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *   @OA\Property(property="label", type="string"),
 *
 *   @OA\Property(property="is_current", type="boolean", default=false, example=false),
 *   @OA\Property(property="is_future", type="boolean", default=false, example=false),
 *   @OA\Property(property="is_server", type="boolean", default=false, example=false),
 *
 *   @OA\Property(property="cpu", type="string"),
 *   @OA\Property(property="cpu_price", type="integer", format="int64"),
 *   @OA\Property(property="cpu_sub", type="string"),
 *   @OA\Property(property="cpu_sub2", type="string"),
 *
 *   @OA\Property(property="ram", type="string"),
 *   @OA\Property(property="ram_price", type="integer", format="int64"),
 *   @OA\Property(property="ram_sub", type="string"),
 *
 *   @OA\Property(property="gpu", type="string"),
 *   @OA\Property(property="gpu_price", type="integer", format="int64"),
 *   @OA\Property(property="gpu_sub", type="string"),
 *
 *   @OA\Property(property="motherboard", type="string"),
 *   @OA\Property(property="motherboard_price", type="integer", format="int64"),
 *
 *   @OA\Property(property="psu", type="string"),
 *   @OA\Property(property="psu_price", type="integer", format="int64"),
 *
 *   @OA\Property(property="cooler", type="string"),
 *   @OA\Property(property="cooler_price", type="integer", format="int64"),
 *   @OA\Property(property="cooler_acc", type="string"),
 *   @OA\Property(property="cooler_acc_price", type="integer", format="int64"),
 *
 *   @OA\Property(property="ssd_1", type="string"),
 *   @OA\Property(property="ssd_1_price", type="integer", format="int64"),
 *   @OA\Property(property="ssd_2", type="string"),
 *   @OA\Property(property="ssd_2_price", type="integer", format="int64"),
 *   @OA\Property(property="ssd_3", type="string"),
 *   @OA\Property(property="ssd_3_price", type="integer", format="int64"),
 *   @OA\Property(property="ssd_4", type="string"),
 *   @OA\Property(property="ssd_4_price", type="integer", format="int64"),
 *
 *   @OA\Property(property="hdd_1", type="string"),
 *   @OA\Property(property="hdd_1_price", type="integer", format="int64"),
 *   @OA\Property(property="hdd_2", type="string"),
 *   @OA\Property(property="hdd_2_price", type="integer", format="int64"),
 *   @OA\Property(property="hdd_3", type="string"),
 *   @OA\Property(property="hdd_3_price", type="integer", format="int64"),
 *   @OA\Property(property="hdd_4", type="string"),
 *   @OA\Property(property="hdd_4_price", type="integer", format="int64"),
 *
 *   @OA\Property(property="case", type="string"),
 *   @OA\Property(property="case_price", type="integer", format="int64"),
 *
 *   @OA\Property(property="case_fans_1", type="string"),
 *   @OA\Property(property="case_fans_1_price", type="integer", format="int64"),
 *   @OA\Property(property="case_fans_2", type="string"),
 *   @OA\Property(property="case_fans_2_price", type="integer", format="int64"),
 *   @OA\Property(property="case_fans_3", type="string"),
 *   @OA\Property(property="case_fans_3_price", type="integer", format="int64"),
 *   @OA\Property(property="case_fans_4", type="string"),
 *   @OA\Property(property="case_fans_4_price", type="integer", format="int64"),
 *
 *   @OA\Property(property="monitor", type="string"),
 *   @OA\Property(property="monitor_price", type="integer", format="int64"),
 *   @OA\Property(property="monitor_sub", type="string"),
 *
 *   @OA\Property(property="monitor_acc_1", type="string"),
 *   @OA\Property(property="monitor_acc_1_price", type="integer", format="int64"),
 *   @OA\Property(property="monitor_acc_2", type="string"),
 *   @OA\Property(property="monitor_acc_2_price", type="integer", format="int64"),
 *
 *   @OA\Property(property="keyboard", type="string"),
 *   @OA\Property(property="keyboard_price", type="integer", format="int64"),
 *   @OA\Property(property="keyboard_sub", type="string"),
 *   @OA\Property(property="keyboard_sub2", type="string"),
 *
 *   @OA\Property(property="keyboard_acc_1", type="string"),
 *   @OA\Property(property="keyboard_acc_1_price", type="integer", format="int64"),
 *   @OA\Property(property="keyboard_acc_2", type="string"),
 *   @OA\Property(property="keyboard_acc_2_price", type="integer", format="int64"),
 *
 *   @OA\Property(property="mouse", type="string"),
 *   @OA\Property(property="mouse_price", type="integer", format="int64"),
 *
 *   @OA\Property(property="speakers", type="string"),
 *   @OA\Property(property="speakers_price", type="integer", format="int64"),
 *
 *   @OA\Property(property="wifi", type="string"),
 *   @OA\Property(property="wifi_price", type="integer", format="int64"),
 *
 *   @OA\Property(property="headset_1", type="string"),
 *   @OA\Property(property="headset_1_price", type="integer", format="int64"),
 *   @OA\Property(property="headset_2", type="string"),
 *   @OA\Property(property="headset_2_price", type="integer", format="int64"),
 *
 *   @OA\Property(property="mic", type="string"),
 *   @OA\Property(property="mic_price", type="integer", format="int64"),
 *   @OA\Property(property="mic_acc", type="string"),
 *   @OA\Property(property="mic_acc_price", type="integer", format="int64"),
 *
 *   @OA\Property(property="audio_interface", type="string"),
 *   @OA\Property(property="audio_interface_price", type="integer", format="int64"),
 *
 *   @OA\Property(property="equalizer", type="string"),
 *   @OA\Property(property="equalizer_price", type="integer", format="int64"),
 *
 *   @OA\Property(property="amplifier", type="string"),
 *   @OA\Property(property="amplifier_price", type="string"),
 *
 *   @OA\Property(property="created_at", type="string", example="2023-05-21 21:05:57"),
 *   @OA\Property(property="updated_at", type="string", example="2023-05-21 21:05:57"),
 *   @OA\Property(property="deleted_at", type="string", example="2023-05-21 21:05:57"),
 * )
 */
class PCSetup extends Model {

  protected $table = 'pc_setups';

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
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

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = [];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [
    'created_at' => 'datetime:Y-m-d H:i:s',
    'updated_at' => 'datetime:Y-m-d H:i:s',
  ];
}
