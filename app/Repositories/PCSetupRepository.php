<?php

namespace App\Repositories;

use Carbon\Carbon;

use App\Models\PCSetup;

class PCSetupRepository {

  public function getAll() {
    return PCSetup::all();
  }

  public function get(int $id) {
    return PCSetup::where('id', $id)->firstOrFail();
  }

  public function add(array $values) {
    PCSetup::create($values);
  }

  public function edit(array $values, int $id) {
    PCSetup::where('id', $id)->update($values);
  }

  public function delete(int $id) {
    PCSetup::where('id', $id)
      ->firstOrFail()
      ->delete();
  }

  public function import(array $file) {
    $import = [];

    foreach ($file as $item) {
      if (!empty($item)) {
        $data = [
          'label' => $item->label,

          'is_current' => is_bool($item->is_current) ? $item->is_current : false,
          'is_future' => is_bool($item->is_future) ? $item->is_future : false,
          'is_server' => is_bool($item->is_server) ? $item->is_server : false,

          'cpu' => $item->cpu,
          'cpu_price' => is_int($item->cpu_price) ? $item->cpu_price : null,
          'cpu_sub' => $item->cpu_sub,
          'cpu_sub2' => $item->cpu_sub2,

          'ram' => $item->ram,
          'ram_price' => is_int($item->ram_price) ? $item->ram_price : null,
          'ram_sub' => $item->ram_sub,

          'gpu' => $item->gpu,
          'gpu_price' => is_int($item->gpu_price) ? $item->gpu_price : null,
          'gpu_sub' => $item->gpu_sub,

          'motherboard' => $item->motherboard,
          'motherboard_price' => is_int($item->motherboard_price) ? $item->motherboard_price : null,

          'psu' => $item->psu,
          'psu_price' => is_int($item->psu_price) ? $item->psu_price : null,

          'cooler' => $item->cooler,
          'cooler_price' => is_int($item->cooler_price) ? $item->cooler_price : null,
          'cooler_acc' => $item->cooler_acc,
          'cooler_acc_price' => is_int($item->cooler_acc_price) ? $item->cooler_acc_price : null,

          'ssd_1' => $item->ssd_1,
          'ssd_1_price' => is_int($item->ssd_1_price) ? $item->ssd_1_price : null,
          'ssd_2' => $item->ssd_2,
          'ssd_2_price' => is_int($item->ssd_2_price) ? $item->ssd_2_price : null,
          'ssd_3' => $item->ssd_3,
          'ssd_3_price' => is_int($item->ssd_3_price) ? $item->ssd_3_price : null,
          'ssd_4' => $item->ssd_4,
          'ssd_4_price' => is_int($item->ssd_4_price) ? $item->ssd_4_price : null,

          'hdd_1' => $item->hdd_1,
          'hdd_1_price' => is_int($item->hdd_1_price) ? $item->hdd_1_price : null,
          'hdd_2' => $item->hdd_2,
          'hdd_2_price' => is_int($item->hdd_2_price) ? $item->hdd_2_price : null,
          'hdd_3' => $item->hdd_3,
          'hdd_3_price' => is_int($item->hdd_3_price) ? $item->hdd_3_price : null,
          'hdd_4' => $item->hdd_4,
          'hdd_4_price' => is_int($item->hdd_4_price) ? $item->hdd_4_price : null,

          'case' => $item->case,
          'case_price' => is_int($item->case_price) ? $item->case_price : null,
          'case_fans_1' => $item->case_fans_1,
          'case_fans_1_price' => is_int($item->case_fans_1_price) ? $item->case_fans_1_price : null,
          'case_fans_2' => $item->case_fans_2,
          'case_fans_2_price' => is_int($item->case_fans_2_price) ? $item->case_fans_2_price : null,
          'case_fans_3' => $item->case_fans_3,
          'case_fans_3_price' => is_int($item->case_fans_3_price) ? $item->case_fans_3_price : null,
          'case_fans_4' => $item->case_fans_4,
          'case_fans_4_price' => is_int($item->case_fans_4_price) ? $item->case_fans_4_price : null,

          'monitor' => $item->monitor,
          'monitor_price' => is_int($item->monitor_price) ? $item->monitor_price : null,
          'monitor_sub' => $item->monitor_sub,
          'monitor_acc_1' => $item->monitor_acc_1,
          'monitor_acc_1_price' => is_int($item->monitor_acc_1_price) ? $item->monitor_acc_1_price : null,
          'monitor_acc_2' => $item->monitor_acc_2,
          'monitor_acc_2_price' => is_int($item->monitor_acc_2_price) ? $item->monitor_acc_2_price : null,

          'keyboard' => $item->keyboard,
          'keyboard_price' => is_int($item->keyboard_price) ? $item->keyboard_price : null,
          'keyboard_sub' => $item->keyboard_sub,
          'keyboard_sub2' => $item->keyboard_sub2,
          'keyboard_acc_1' => $item->keyboard_acc_1,
          'keyboard_acc_1_price' => is_int($item->keyboard_acc_1_price) ? $item->keyboard_acc_1_price : null,
          'keyboard_acc_2' => $item->keyboard_acc_2,
          'keyboard_acc_2_price' => is_int($item->keyboard_acc_2_price) ? $item->keyboard_acc_2_price : null,

          'mouse' => $item->mouse,
          'mouse_price' => is_int($item->mouse_price) ? $item->mouse_price : null,

          'speakers' => $item->speakers,
          'speakers_price' => is_int($item->speakers_price) ? $item->speakers_price : null,

          'wifi' => $item->wifi,
          'wifi_price' => is_int($item->wifi_price) ? $item->wifi_price : null,

          'headset_1' => $item->headset_1,
          'headset_1_price' => is_int($item->headset_1_price) ? $item->headset_1_price : null,
          'headset_2' => $item->headset_2,
          'headset_2_price' => is_int($item->headset_2_price) ? $item->headset_2_price : null,

          'mic' => $item->mic,
          'mic_price' => is_int($item->mic_price) ? $item->mic_price : null,
          'mic_acc' => $item->mic_acc,
          'mic_acc_price' => is_int($item->mic_acc_price) ? $item->mic_acc_price : null,

          'audio_interface' => $item->audio_interface,
          'audio_interface_price' => is_int($item->audio_interface_price) ? $item->audio_interface_price : null,
          'equalizer' => $item->equalizer,
          'equalizer_price' => is_int($item->equalizer_price) ? $item->equalizer_price : null,
          'amplifier' => $item->amplifier,
          'amplifier_price' => is_int($item->amplifier_price) ? $item->amplifier_price : null,

          'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
          'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ];

        array_push($import, $data);
      }
    }

    if (count($import)) {
      PCSetup::truncate();
      PCSetup::insert($import);
    }

    return count($import);
  }

  public function duplicate(int $id) {
    $new_pc_setup = PCSetup::where('id', $id)
      ->firstOrFail()
      ->replicate();

    $new_pc_setup->label = $new_pc_setup->label . ' (copy)';
    $new_pc_setup->is_current = false;
    $new_pc_setup->is_future = false;

    $new_pc_setup->save();

    return $new_pc_setup->id;
  }

  public function toggleCurrent($id) {
    $pc_setup = PCSetup::where('id', $id)->firstOrFail();
    $is_current = $pc_setup->is_current;
    $is_server = $pc_setup->is_server;

    $pc_setup->is_current = !$is_current;

    if (!$is_current) {
      $pc_setup->is_future = false;

      PCSetup::where('is_server', $is_server)
        ->where('id', '!=', $id)
        ->update(['is_current' => false]);
    }

    $pc_setup->save();
  }

  public function toggleFuture($id) {
    $pc_setup = PCSetup::where('id', $id)->firstOrFail();

    $pc_setup->is_current = false;
    $pc_setup->is_future = !$pc_setup->is_future;
    $pc_setup->save();
  }

  public function toggleServer($id) {
    $pc_setup = PCSetup::where('id', $id)->firstOrFail();

    $pc_setup->is_server = !$pc_setup->is_server;
    $pc_setup->save();
  }
}
