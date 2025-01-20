<?php

namespace App\Repositories;

use Carbon\Carbon;
use Illuminate\Support\Str;

use App\Models\PCInfo;
use App\Models\PCOwner;
use App\Models\PCSetup;
use App\Resources\PC\PCInfoResource;

class PCInfoRepository {

  public function get_all() {
    return PCInfo::with('owner')
      ->orderBy('label')
      ->orderBy('id')
      ->get();
  }

  public function get($uuid) {
    $info_resource = PCInfo::with('owner')
      ->with('setups')
      ->where('uuid', $uuid)
      ->firstOrFail();

    // Check if info contains any setup
    PCSetup::where('id_info', $info_resource->id)->firstOrFail();

    $stats = $this->calculate_info_stats($info_resource);

    return [
      'data' => new PCInfoResource($info_resource),
      'stats' => $stats,
    ];
  }

  public function add(array $values) {
    return PCInfo::create([
      'uuid' => Str::uuid()->toString(),
      'id_owner' => $values['id_owner'],
      'label' => $values['label'],
      'is_active' => $values['is_active'],
      'is_hidden' => $values['is_hidden'],
    ]);
  }

  public function edit(array $values, $uuid) {
    return PCInfo::where('uuid', $uuid)
      ->firstOrFail()
      ->update($values);
  }

  public function delete($uuid) {
    return PCInfo::where('uuid', $uuid)
      ->firstOrFail()
      ->delete();
  }

  public function import(array $contents) {
    $import = [];

    $owners = PCOwner::select('id', 'name')->get()->makeVisible('id');

    foreach ($contents as $item) {
      if (!empty($item)) {
        if (!$item->id_owner) continue;
        if (!$item->label) continue;

        // Find the actual owner ID
        $actual_owner = $owners->first(fn($owner) => $owner->name == $item->id_owner);
        if (!$actual_owner) continue;

        $data = [
          'uuid' => Str::uuid()->toString(),
          'id_owner' => $actual_owner->id,
          'label' => $item->label,
          'is_active' => to_boolean($item->is_active, true),
          'is_hidden' => to_boolean($item->is_hidden, true),

          'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
          'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ];

        array_push($import, $data);
      }
    }

    PCInfo::refreshAutoIncrements();
    PCInfo::insert($import);
    PCInfo::refreshAutoIncrements();

    return count($import);
  }

  /**
   * Calculation Functions
   */
  private function calculate_info_stats($info_resource) {
    // Cost Calculations
    $totalSetupCost = 0;
    $totalSystemCost = 0;
    $totalPeripheralCost = 0;

    foreach ($info_resource->setups as $item) {
      $price = $item->component->price ?? $item->component->price_estimate;
      $totalSetupCost += $price;

      if ($item->component->type->is_peripheral) {
        $totalPeripheralCost += $price;
      } else {
        $totalSystemCost += $price;
      }
    }

    // CPU Highlight
    $cpu_value = $info_resource->setups->first(fn($item) => $item->component->type->type === 'cpu');
    $highlight_cpu = $cpu_value->component->name ?? '';
    $highlight_cpu = str_ireplace(['amd', 'intel'], '', $highlight_cpu);

    // GPU Highlight
    $gpu_value = $info_resource->setups->first(fn($item) => $item->component->type->type === 'gpu');
    $gpu_search_value = null;
    preg_match('/[GR]TX?\ \d{3,5}(TI)?/i', $gpu_value->component->name ?? '', $gpu_search_value);
    $highlight_gpu = $gpu_search_value[0] ?? '';

    // RAM Highlight
    $ram_values = $info_resource->setups->filter(fn($item) => $item->component->type->type === 'ram');
    $actual_ram_size = 0;

    foreach ($ram_values as $value) {
      $ram_name_search_value = null;
      preg_match('/\d{1,2}\ ?GB/', $value->component->name, $ram_name_search_value);
      $actual_ram_size += intval($ram_name_search_value[0]);
    }

    $ram_name = $actual_ram_size . 'GB';
    $ram_desc_search_value = null;
    preg_match('/\d{4,5}\ ?MHz/i', $ram_values->first()->component->description ?? '', $ram_desc_search_value);
    $ram_desc = str_replace(' ', '', $ram_desc_search_value[0] ?? '');
    $highlight_ram = trim($ram_name . ' ' . $ram_desc);

    // HDD Highlight
    $hdd_values = $info_resource->setups->filter(fn($item) => $item->component->type->type === 'hdd');
    $ssd_values = $info_resource->setups->filter(fn($item) => $item->component->type->type === 'ssd');
    $actual_hdd_size = 0;
    $actual_ssd_size = 0;

    foreach ($hdd_values as $value) {
      $hdd_search_value = null;
      preg_match('/\d{1,3}\ ?[GT]B/i', $value->component->name, $hdd_search_value);
      $hdd_search_value = $hdd_search_value[0];

      if (str_contains(strtolower($hdd_search_value), 'gb')) {
        $hdd_size = (float) (intval($hdd_search_value) / 1000);
        $actual_hdd_size += $hdd_size;
      } else if (str_contains(strtolower($hdd_search_value), 'tb')) {
        $actual_hdd_size += intval($hdd_search_value);
      }
    }

    foreach ($ssd_values as $value) {
      $ssd_search_value = null;
      preg_match('/\d{1,3}\ ?[GT]B/i', $value->component->name, $ssd_search_value);
      $ssd_search_value = $ssd_search_value[0];

      if (str_contains(strtolower($ssd_search_value), 'gb')) {
        $ssd_size = (float) (intval($ssd_search_value) / 1000);
        $actual_ssd_size += $ssd_size;
      } else if (str_contains(strtolower($ssd_search_value), 'tb')) {
        $actual_ssd_size += intval($ssd_search_value);
      }
    }

    $highlight_storage = '';
    if ($actual_ssd_size) {

      if ($actual_ssd_size < 1) {
        $highlight_storage .= ($actual_ssd_size * 1000) . 'G SSD';
      } else {
        $highlight_storage .= $actual_ssd_size . 'T SSD';
      }
    }

    if ($actual_hdd_size) {
      if ($actual_ssd_size) $highlight_storage .= ', ';

      if ($actual_hdd_size < 1) {
        $highlight_storage .= ($actual_hdd_size * 1000) . 'G HDD';
      } else {
        $highlight_storage .= $actual_hdd_size . 'T HDD';
      }
    }

    return [
      'totalSetupCost' => $totalSetupCost,
      'totalSetupCostFormat' => number_format($totalSetupCost),
      'totalSystemCost' => $totalSystemCost,
      'totalSystemCostFormat' => number_format($totalSystemCost),
      'totalPeripheralCost' => $totalPeripheralCost,
      'totalPeripheralCostFormat' => number_format($totalPeripheralCost),
      'highlightCpu' => $highlight_cpu,
      'highlightGpu' => $highlight_gpu,
      'highlightRam' => $highlight_ram,
      'highlightStorage' => $highlight_storage,
    ];
  }
}
