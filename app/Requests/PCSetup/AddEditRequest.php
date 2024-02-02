<?php

namespace App\Requests\PCSetup;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddEditRequest extends FormRequest {

  /**
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_label",
   *   name="label",
   *   in="query",
   *   required=true,
   *   example="Sample Label",
   *   @OA\Schema(type="string", minLength=1, maxLength=64),
   * ),
   *
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_is_current",
   *   name="is_current",
   *   in="query",
   *   example=false,
   *   @OA\Schema(type="boolean"),
   * ),
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_is_future",
   *   name="is_future",
   *   in="query",
   *   example=false,
   *   @OA\Schema(type="boolean"),
   * ),
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_is_server",
   *   name="is_server",
   *   in="query",
   *   example=false,
   *   @OA\Schema(type="boolean"),
   * ),
   *
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_cpu",
   *   name="cpu",
   *   in="query",
   *   example="Sample CPU",
   *   @OA\Schema(type="string", maxLength=64),
   * ),
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_cpu_price",
   *   name="cpu_price",
   *   in="query",
   *   example=100,
   *   @OA\Schema(type="integer", format="int32"),
   * ),
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_cpu_sub",
   *   name="cpu_sub",
   *   in="query",
   *   example="Sample CPU Subtext",
   *   @OA\Schema(type="string", maxLength=64),
   * ),
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_cpu_sub2",
   *   name="cpu_sub2",
   *   in="query",
   *   example="Sample CPU Subtext",
   *   @OA\Schema(type="string", maxLength=64),
   * ),
   *
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_ram",
   *   name="ram",
   *   in="query",
   *   example="Sample RAM",
   *   @OA\Schema(type="string", maxLength=64),
   * ),
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_ram_price",
   *   name="ram_price",
   *   in="query",
   *   example=100,
   *   @OA\Schema(type="integer", format="int32"),
   * ),
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_ram_sub",
   *   name="ram_sub",
   *   in="query",
   *   example="Sample RAM Subtext",
   *   @OA\Schema(type="string", maxLength=64),
   * ),
   *
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_gpu",
   *   name="gpu",
   *   in="query",
   *   example="Sample GPU",
   *   @OA\Schema(type="string", maxLength=64),
   * ),
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_gpu_price",
   *   name="gpu_price",
   *   in="query",
   *   example=100,
   *   @OA\Schema(type="integer", format="int32"),
   * ),
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_gpu_sub",
   *   name="gpu_sub",
   *   in="query",
   *   example="Sample GPU Subtext",
   *   @OA\Schema(type="string", maxLength=64),
   * ),
   *
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_motherboard",
   *   name="motherboard",
   *   in="query",
   *   example="Sample Motherboard",
   *   @OA\Schema(type="string", maxLength=64),
   * ),
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_motherboard_price",
   *   name="motherboard_price",
   *   in="query",
   *   example=100,
   *   @OA\Schema(type="integer", format="int32"),
   * ),
   *
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_psu",
   *   name="psu",
   *   in="query",
   *   example="Sample PSU",
   *   @OA\Schema(type="string", maxLength=64),
   * ),
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_psu_price",
   *   name="psu_price",
   *   in="query",
   *   example=100,
   *   @OA\Schema(type="integer", format="int32"),
   * ),
   *
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_cooler",
   *   name="cooler",
   *   in="query",
   *   example="Sample Cooler",
   *   @OA\Schema(type="string", maxLength=64),
   * ),
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_cooler_price",
   *   name="cooler_price",
   *   in="query",
   *   example=100,
   *   @OA\Schema(type="integer", format="int32"),
   * ),
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_cooler_acc",
   *   name="cooler_acc",
   *   in="query",
   *   example="Sample Cooler Accessory",
   *   @OA\Schema(type="string", maxLength=64),
   * ),
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_cooler_acc_price",
   *   name="cooler_acc_price",
   *   in="query",
   *   example=100,
   *   @OA\Schema(type="integer", format="int32"),
   * ),
   *
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_ssd_1",
   *   name="ssd_1",
   *   in="query",
   *   example="Sample SSD",
   *   @OA\Schema(type="string", maxLength=64),
   * ),
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_ssd_1_price",
   *   name="ssd_1_price",
   *   in="query",
   *   example=100,
   *   @OA\Schema(type="integer", format="int32"),
   * ),
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_ssd_2",
   *   name="ssd_2",
   *   in="query",
   *   example="Sample SSD",
   *   @OA\Schema(type="string", maxLength=64),
   * ),
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_ssd_2_price",
   *   name="ssd_2_price",
   *   in="query",
   *   example=100,
   *   @OA\Schema(type="integer", format="int32"),
   * ),
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_ssd_3",
   *   name="ssd_3",
   *   in="query",
   *   example="Sample SSD",
   *   @OA\Schema(type="string", maxLength=64),
   * ),
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_ssd_3_price",
   *   name="ssd_3_price",
   *   in="query",
   *   example=100,
   *   @OA\Schema(type="integer", format="int32"),
   * ),
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_ssd_4",
   *   name="ssd_4",
   *   in="query",
   *   example="Sample SSD",
   *   @OA\Schema(type="string", maxLength=64),
   * ),
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_ssd_4_price",
   *   name="ssd_4_price",
   *   in="query",
   *   example=100,
   *   @OA\Schema(type="integer", format="int32"),
   * ),
   *
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_hdd_1",
   *   name="hdd_1",
   *   in="query",
   *   example="Sample HDD",
   *   @OA\Schema(type="string", maxLength=64),
   * ),
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_hdd_1_price",
   *   name="hdd_1_price",
   *   in="query",
   *   example=100,
   *   @OA\Schema(type="integer", format="int32"),
   * ),
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_hdd_2",
   *   name="hdd_2",
   *   in="query",
   *   example="Sample HDD",
   *   @OA\Schema(type="string", maxLength=64),
   * ),
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_hdd_2_price",
   *   name="hdd_2_price",
   *   in="query",
   *   example=100,
   *   @OA\Schema(type="integer", format="int32"),
   * ),
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_hdd_3",
   *   name="hdd_3",
   *   in="query",
   *   example="Sample HDD",
   *   @OA\Schema(type="string", maxLength=64),
   * ),
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_hdd_3_price",
   *   name="hdd_3_price",
   *   in="query",
   *   example=100,
   *   @OA\Schema(type="integer", format="int32"),
   * ),
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_hdd_4",
   *   name="hdd_4",
   *   in="query",
   *   example="Sample HDD",
   *   @OA\Schema(type="string", maxLength=64),
   * ),
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_hdd_4_price",
   *   name="hdd_4_price",
   *   in="query",
   *   example=100,
   *   @OA\Schema(type="integer", format="int32"),
   * ),
   *
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_case",
   *   name="case",
   *   in="query",
   *   example="Sample Case",
   *   @OA\Schema(type="string", maxLength=64),
   * ),
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_case_price",
   *   name="case_price",
   *   in="query",
   *   example=100,
   *   @OA\Schema(type="integer", format="int32"),
   * ),
   *
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_case_fans_1",
   *   name="case_fans_1",
   *   in="query",
   *   example="Sample Case Fans",
   *   @OA\Schema(type="string", maxLength=64),
   * ),
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_case_fans_1_price",
   *   name="case_fans_1_price",
   *   in="query",
   *   example=100,
   *   @OA\Schema(type="integer", format="int32"),
   * ),
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_case_fans_2",
   *   name="case_fans_2",
   *   in="query",
   *   example="Sample Case Fans",
   *   @OA\Schema(type="string", maxLength=64),
   * ),
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_case_fans_2_price",
   *   name="case_fans_2_price",
   *   in="query",
   *   example=100,
   *   @OA\Schema(type="integer", format="int32"),
   * ),
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_case_fans_3",
   *   name="case_fans_3",
   *   in="query",
   *   example="Sample Case Fans",
   *   @OA\Schema(type="string", maxLength=64),
   * ),
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_case_fans_3_price",
   *   name="case_fans_3_price",
   *   in="query",
   *   example=100,
   *   @OA\Schema(type="integer", format="int32"),
   * ),
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_case_fans_4",
   *   name="case_fans_4",
   *   in="query",
   *   example="Sample Case Fans",
   *   @OA\Schema(type="string", maxLength=64),
   * ),
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_case_fans_4_price",
   *   name="case_fans_4_price",
   *   in="query",
   *   example=100,
   *   @OA\Schema(type="integer", format="int32"),
   * ),
   *
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_monitor",
   *   name="monitor",
   *   in="query",
   *   example="Sample Monitor",
   *   @OA\Schema(type="string", maxLength=64),
   * ),
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_monitor_price",
   *   name="monitor_price",
   *   in="query",
   *   example=100,
   *   @OA\Schema(type="integer", format="int32"),
   * ),
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_monitor_sub",
   *   name="monitor_sub",
   *   in="query",
   *   example="Sample Monitor Subtext",
   *   @OA\Schema(type="string", maxLength=64),
   * ),
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_monitor_acc_1",
   *   name="monitor_acc_1",
   *   in="query",
   *   example="Sample Monitor Accessory",
   *   @OA\Schema(type="string", maxLength=64),
   * ),
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_monitor_acc_1_price",
   *   name="monitor_acc_1_price",
   *   in="query",
   *   example=100,
   *   @OA\Schema(type="integer", format="int32"),
   * ),
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_monitor_acc_2",
   *   name="monitor_acc_2",
   *   in="query",
   *   example="Sample Monitor Accessory",
   *   @OA\Schema(type="string", maxLength=64),
   * ),
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_monitor_acc_2_price",
   *   name="monitor_acc_2_price",
   *   in="query",
   *   example=100,
   *   @OA\Schema(type="integer", format="int32"),
   * ),
   *
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_keyboard",
   *   name="keyboard",
   *   in="query",
   *   example="Sample Keyboard",
   *   @OA\Schema(type="string", maxLength=64),
   * ),
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_keyboard_price",
   *   name="keyboard_price",
   *   in="query",
   *   example=100,
   *   @OA\Schema(type="integer", format="int32"),
   * ),
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_keyboard_sub",
   *   name="keyboard_sub",
   *   in="query",
   *   example="Sample Keyboard Subtext",
   *   @OA\Schema(type="string", maxLength=64),
   * ),
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_keyboard_sub2",
   *   name="keyboard_sub2",
   *   in="query",
   *   example="Sample Keyboard Subtext",
   *   @OA\Schema(type="string", maxLength=64),
   * ),
   *
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_keyboard_acc_1",
   *   name="keyboard_acc_1",
   *   in="query",
   *   example="Sample Keyboard Accessory 1",
   *   @OA\Schema(type="string", maxLength=64),
   * ),
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_keyboard_acc_1_price",
   *   name="keyboard_acc_1_price",
   *   in="query",
   *   example=100,
   *   @OA\Schema(type="integer", format="int32"),
   * ),
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_keyboard_acc_2",
   *   name="keyboard_acc_2",
   *   in="query",
   *   example="Sample Keyboard Accessory 2",
   *   @OA\Schema(type="string", maxLength=64),
   * ),
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_keyboard_acc_2_price",
   *   name="keyboard_acc_2_price",
   *   in="query",
   *   example=100,
   *   @OA\Schema(type="integer", format="int32"),
   * ),
   *
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_mouse",
   *   name="mouse",
   *   in="query",
   *   example="Sample Mouse",
   *   @OA\Schema(type="string", maxLength=64),
   * ),
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_mouse_price",
   *   name="mouse_price",
   *   in="query",
   *   example=100,
   *   @OA\Schema(type="integer", format="int32"),
   * ),
   *
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_speakers",
   *   name="speakers",
   *   in="query",
   *   example="Sample Speakers",
   *   @OA\Schema(type="string", maxLength=64),
   * ),
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_speakers_price",
   *   name="speakers_price",
   *   in="query",
   *   example=100,
   *   @OA\Schema(type="integer", format="int32"),
   * ),
   *
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_wifi",
   *   name="wifi",
   *   in="query",
   *   example="Sample WiFi Card",
   *   @OA\Schema(type="string", maxLength=64),
   * ),
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_wifi_price",
   *   name="wifi_price",
   *   in="query",
   *   example=100,
   *   @OA\Schema(type="integer", format="int32"),
   * ),
   *
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_headset_1",
   *   name="headset_1",
   *   in="query",
   *   example="Sample Headset 1",
   *   @OA\Schema(type="string", maxLength=64),
   * ),
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_headset_1_price",
   *   name="headset_1_price",
   *   in="query",
   *   example=100,
   *   @OA\Schema(type="integer", format="int32"),
   * ),
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_headset_2",
   *   name="headset_2",
   *   in="query",
   *   example="Sample Headset 2",
   *   @OA\Schema(type="string", maxLength=64),
   * ),
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_headset_2_price",
   *   name="headset_2_price",
   *   in="query",
   *   example=100,
   *   @OA\Schema(type="integer", format="int32"),
   * ),
   *
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_mic",
   *   name="mic",
   *   in="query",
   *   example="Sample Microphone",
   *   @OA\Schema(type="string", maxLength=64),
   * ),
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_mic_price",
   *   name="mic_price",
   *   in="query",
   *   example=100,
   *   @OA\Schema(type="integer", format="int32"),
   * ),
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_mic_acc",
   *   name="mic_acc",
   *   in="query",
   *   example="Sample Microphone Accessory",
   *   @OA\Schema(type="string", maxLength=64),
   * ),
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_mic_acc_price",
   *   name="mic_acc_price",
   *   in="query",
   *   example=100,
   *   @OA\Schema(type="integer", format="int32"),
   * ),
   *
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_audio_interface",
   *   name="audio_interface",
   *   in="query",
   *   example="Sample Audio Interface",
   *   @OA\Schema(type="string", maxLength=64),
   * ),
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_audio_interface_price",
   *   name="audio_interface_price",
   *   in="query",
   *   example=100,
   *   @OA\Schema(type="integer", format="int32"),
   * ),
   *
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_equalizer",
   *   name="equalizer",
   *   in="query",
   *   example="Sample Equalizer",
   *   @OA\Schema(type="string", maxLength=64),
   * ),
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_equalizer_price",
   *   name="equalizer_price",
   *   in="query",
   *   example=100,
   *   @OA\Schema(type="integer", format="int32"),
   * ),
   *
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_amplifier",
   *   name="amplifier",
   *   in="query",
   *   example="Sample Amplifier",
   *   @OA\Schema(type="string", maxLength=64),
   * ),
   * @OA\Parameter(
   *   parameter="pc_setup_add_edit_amplifier_price",
   *   name="amplifier_price",
   *   in="query",
   *   example=100,
   *   @OA\Schema(type="integer", format="int32"),
   * ),
   */
  public function rules() {
    return [
      'label' => ['required', 'string', 'max:64'],

      'is_current' => ['boolean'],
      'is_future' => ['boolean'],
      'is_server' => ['boolean'],

      'cpu' => ['string', 'max:64'],
      'cpu_price' => ['integer', 'min:0', 'max:250000'],
      'cpu_sub' => ['string', 'max:64'],
      'cpu_sub2' => ['string', 'max:64'],

      'ram' => ['string', 'max:64'],
      'ram_price' => ['integer', 'min:0', 'max:250000'],
      'ram_sub' => ['string', 'max:64'],

      'gpu' => ['string', 'max:64'],
      'gpu_price' => ['integer', 'min:0', 'max:250000'],
      'gpu_sub' => ['string', 'max:64'],

      'motherboard' => ['string', 'max:64'],
      'motherboard_price' => ['integer', 'min:0', 'max:250000'],

      'psu' => ['string', 'max:64'],
      'psu_price' => ['integer', 'min:0', 'max:250000'],

      'cooler' => ['string', 'max:64'],
      'cooler_price' => ['integer', 'min:0', 'max:250000'],
      'cooler_acc' => ['string', 'max:64'],
      'cooler_acc_price' => ['integer', 'min:0', 'max:250000'],

      'ssd_1' => ['string', 'max:64'],
      'ssd_1_price' => ['integer', 'min:0', 'max:250000'],
      'ssd_2' => ['string', 'max:64'],
      'ssd_2_price' => ['integer', 'min:0', 'max:250000'],
      'ssd_3' => ['string', 'max:64'],
      'ssd_3_price' => ['integer', 'min:0', 'max:250000'],
      'ssd_4' => ['string', 'max:64'],
      'ssd_4_price' => ['integer', 'min:0', 'max:250000'],

      'hdd_1' => ['string', 'max:64'],
      'hdd_1_price' => ['integer', 'min:0', 'max:250000'],
      'hdd_2' => ['string', 'max:64'],
      'hdd_2_price' => ['integer', 'min:0', 'max:250000'],
      'hdd_3' => ['string', 'max:64'],
      'hdd_3_price' => ['integer', 'min:0', 'max:250000'],
      'hdd_4' => ['string', 'max:64'],
      'hdd_4_price' => ['integer', 'min:0', 'max:250000'],

      'case' => ['string', 'max:64'],
      'case_price' => ['integer', 'min:0', 'max:250000'],

      'case_fans_1' => ['string', 'max:64'],
      'case_fans_1_price' => ['integer', 'min:0', 'max:250000'],
      'case_fans_2' => ['string', 'max:64'],
      'case_fans_2_price' => ['integer', 'min:0', 'max:250000'],
      'case_fans_3' => ['string', 'max:64'],
      'case_fans_3_price' => ['integer', 'min:0', 'max:250000'],
      'case_fans_4' => ['string', 'max:64'],
      'case_fans_4_price' => ['integer', 'min:0', 'max:250000'],

      'monitor' => ['string', 'max:64'],
      'monitor_price' => ['integer', 'min:0', 'max:250000'],
      'monitor_sub' => ['string', 'max:64'],

      'monitor_acc_1' => ['string', 'max:64'],
      'monitor_acc_1_price' => ['integer', 'min:0', 'max:250000'],
      'monitor_acc_2' => ['string', 'max:64'],
      'monitor_acc_2_price' => ['integer', 'min:0', 'max:250000'],

      'keyboard' => ['string', 'max:64'],
      'keyboard_price' => ['integer', 'min:0', 'max:250000'],
      'keyboard_sub' => ['string', 'max:64'],
      'keyboard_sub2' => ['string', 'max:64'],

      'keyboard_acc_1' => ['string', 'max:64'],
      'keyboard_acc_1_price' => ['integer', 'min:0', 'max:250000'],
      'keyboard_acc_2' => ['string', 'max:64'],
      'keyboard_acc_2_price' => ['integer', 'min:0', 'max:250000'],

      'mouse' => ['string', 'max:64'],
      'mouse_price' => ['integer', 'min:0', 'max:250000'],

      'speakers' => ['string', 'max:64'],
      'speakers_price' => ['integer', 'min:0', 'max:250000'],

      'wifi' => ['string', 'max:64'],
      'wifi_price' => ['integer', 'min:0', 'max:250000'],

      'headset_1' => ['string', 'max:64'],
      'headset_1_price' => ['integer', 'min:0', 'max:250000'],
      'headset_2' => ['string', 'max:64'],
      'headset_2_price' => ['integer', 'min:0', 'max:250000'],

      'mic' => ['string', 'max:64'],
      'mic_price' => ['integer', 'min:0', 'max:250000'],
      'mic_acc' => ['string', 'max:64'],
      'mic_acc_price' => ['integer', 'min:0', 'max:250000'],

      'audio_interface' => ['string', 'max:64'],
      'audio_interface_price' => ['integer', 'min:0', 'max:250000'],

      'equalizer' => ['string', 'max:64'],
      'equalizer_price' => ['integer', 'min:0', 'max:250000'],

      'amplifier' => ['string', 'max:64'],
      'amplifier_price' => ['integer', 'min:0', 'max:250000'],
    ];
  }

  protected function prepareForValidation() {
    $this->merge([
      'is_current' => to_boolean($this->is_current),
      'is_future' => to_boolean($this->is_future),
      'is_server' => to_boolean($this->is_server),
    ]);
  }

  public function failedValidation(Validator $validator) {
    throw new HttpResponseException(
      response()->json([
        'status' => 401,
        'data' => $validator->errors(),
      ], 401)
    );
  }

  public function messages() {
    $validation = require config_path('validation.php');

    return array_merge($validation, []);
  }
}
