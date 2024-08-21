<?php

namespace App\Repositories;

use Carbon\Carbon;

use App\Models\PCSetup;

class PCSetupRepository {

  public function getAll() {
    return PCSetup::all();
  }

  public function get($id) {
    return PCSetup::where('id', $id)->firstOrFail();
  }

  public function add(array $values) {
    PCSetup::create($values);
  }

  public function edit(array $values, $id) {
    PCSetup::where('id', $id)->update($values);
  }

  public function delete($id) {
    PCSetup::where('id', $id)
      ->firstOrFail()
      ->delete();
  }

  public function import(array $file) {
    $import = [];

    foreach ($file as $item) {
      if (!empty($item)) {
        $data = [
          'label' => $item->label ?? null,

          'is_current' => is_bool($item->is_current ?? null) ? $item->is_current : false,
          'is_future' => is_bool($item->is_future ?? null) ? $item->is_future : false,
          'is_server' => is_bool($item->is_server ?? null) ? $item->is_server : false,

          'cpu' => $item->cpu ?? null,
          'cpu_price' => is_int($item->cpu_price ?? null) ? $item->cpu_price : null,
          'cpu_sub' => $item->cpu_sub ?? null,
          'cpu_sub2' => $item->cpu_sub2 ?? null,

          'ram' => $item->ram ?? null,
          'ram_price' => is_int($item->ram_price ?? null) ? $item->ram_price : null,
          'ram_sub' => $item->ram_sub ?? null,

          'gpu' => $item->gpu ?? null,
          'gpu_price' => is_int($item->gpu_price ?? null) ? $item->gpu_price : null,
          'gpu_sub' => $item->gpu_sub ?? null,

          'motherboard' => $item->motherboard ?? null,
          'motherboard_price' => is_int($item->motherboard_price ?? null) ? $item->motherboard_price : null,

          'psu' => $item->psu ?? null,
          'psu_price' => is_int($item->psu_price ?? null) ? $item->psu_price : null,

          'cooler' => $item->cooler ?? null,
          'cooler_price' => is_int($item->cooler_price ?? null) ? $item->cooler_price : null,
          'cooler_acc' => $item->cooler_acc ?? null,
          'cooler_acc_price' => is_int($item->cooler_acc_price ?? null) ? $item->cooler_acc_price : null,

          'ssd_1' => $item->ssd_1 ?? null,
          'ssd_1_price' => is_int($item->ssd_1_price ?? null) ? $item->ssd_1_price : null,
          'ssd_2' => $item->ssd_2 ?? null,
          'ssd_2_price' => is_int($item->ssd_2_price ?? null) ? $item->ssd_2_price : null,
          'ssd_3' => $item->ssd_3 ?? null,
          'ssd_3_price' => is_int($item->ssd_3_price ?? null) ? $item->ssd_3_price : null,
          'ssd_4' => $item->ssd_4 ?? null,
          'ssd_4_price' => is_int($item->ssd_4_price ?? null) ? $item->ssd_4_price : null,

          'hdd_1' => $item->hdd_1 ?? null,
          'hdd_1_price' => is_int($item->hdd_1_price ?? null) ? $item->hdd_1_price : null,
          'hdd_2' => $item->hdd_2 ?? null,
          'hdd_2_price' => is_int($item->hdd_2_price ?? null) ? $item->hdd_2_price : null,
          'hdd_3' => $item->hdd_3 ?? null,
          'hdd_3_price' => is_int($item->hdd_3_price ?? null) ? $item->hdd_3_price : null,
          'hdd_4' => $item->hdd_4 ?? null,
          'hdd_4_price' => is_int($item->hdd_4_price ?? null) ? $item->hdd_4_price : null,

          'case' => $item->case ?? null,
          'case_price' => is_int($item->case_price ?? null) ? $item->case_price : null,
          'case_fans_1' => $item->case_fans_1 ?? null,
          'case_fans_1_price' => is_int($item->case_fans_1_price ?? null) ? $item->case_fans_1_price : null,
          'case_fans_2' => $item->case_fans_2 ?? null,
          'case_fans_2_price' => is_int($item->case_fans_2_price ?? null) ? $item->case_fans_2_price : null,
          'case_fans_3' => $item->case_fans_3 ?? null,
          'case_fans_3_price' => is_int($item->case_fans_3_price ?? null) ? $item->case_fans_3_price : null,
          'case_fans_4' => $item->case_fans_4 ?? null,
          'case_fans_4_price' => is_int($item->case_fans_4_price ?? null) ? $item->case_fans_4_price : null,

          'monitor' => $item->monitor ?? null,
          'monitor_price' => is_int($item->monitor_price ?? null) ? $item->monitor_price : null,
          'monitor_sub' => $item->monitor_sub ?? null,
          'monitor_acc_1' => $item->monitor_acc_1 ?? null,
          'monitor_acc_1_price' => is_int($item->monitor_acc_1_price ?? null) ? $item->monitor_acc_1_price : null,
          'monitor_acc_2' => $item->monitor_acc_2 ?? null,
          'monitor_acc_2_price' => is_int($item->monitor_acc_2_price ?? null) ? $item->monitor_acc_2_price : null,

          'keyboard' => $item->keyboard ?? null,
          'keyboard_price' => is_int($item->keyboard_price ?? null) ? $item->keyboard_price : null,
          'keyboard_sub' => $item->keyboard_sub ?? null,
          'keyboard_sub2' => $item->keyboard_sub2 ?? null,
          'keyboard_acc_1' => $item->keyboard_acc_1 ?? null,
          'keyboard_acc_1_price' => is_int($item->keyboard_acc_1_price ?? null) ? $item->keyboard_acc_1_price : null,
          'keyboard_acc_2' => $item->keyboard_acc_2 ?? null,
          'keyboard_acc_2_price' => is_int($item->keyboard_acc_2_price ?? null) ? $item->keyboard_acc_2_price : null,

          'mouse' => $item->mouse ?? null,
          'mouse_price' => is_int($item->mouse_price ?? null) ? $item->mouse_price : null,

          'speakers' => $item->speakers ?? null,
          'speakers_price' => is_int($item->speakers_price ?? null) ? $item->speakers_price : null,

          'wifi' => $item->wifi ?? null,
          'wifi_price' => is_int($item->wifi_price ?? null) ? $item->wifi_price : null,

          'headset_1' => $item->headset_1 ?? null,
          'headset_1_price' => is_int($item->headset_1_price ?? null) ? $item->headset_1_price : null,
          'headset_2' => $item->headset_2 ?? null,
          'headset_2_price' => is_int($item->headset_2_price ?? null) ? $item->headset_2_price : null,

          'mic' => $item->mic ?? null,
          'mic_price' => is_int($item->mic_price ?? null) ? $item->mic_price : null,
          'mic_acc' => $item->mic_acc ?? null,
          'mic_acc_price' => is_int($item->mic_acc_price ?? null) ? $item->mic_acc_price : null,

          'audio_interface' => $item->audio_interface ?? null,
          'audio_interface_price' => is_int($item->audio_interface_price ?? null) ? $item->audio_interface_price : null,
          'equalizer' => $item->equalizer ?? null,
          'equalizer_price' => is_int($item->equalizer_price ?? null) ? $item->equalizer_price : null,
          'amplifier' => $item->amplifier ?? null,
          'amplifier_price' => is_int($item->amplifier_price ?? null) ? $item->amplifier_price : null,

          'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
          'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ];

        array_push($import, $data);
      }
    }

    if (count($import)) {
      PCSetup::truncate();
      PCSetup::insert($import);
      PCSetup::refreshAutoIncrements();
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
