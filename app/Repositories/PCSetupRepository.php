<?php

namespace App\Repositories;

use Carbon\Carbon;

use App\Exceptions\JsonParsingException;

use App\Models\PCComponent;
use App\Models\PCInfo;
use App\Models\PCOwner;
use App\Models\PCSetup;

class PCSetupRepository {

  public function add(array $values) {
    $id_owner = PCOwner::where('uuid', $values['id_owner'])->firstOrFail()->id;
    $id_info = PCInfo::where('uuid', $values['id_info'])->firstOrFail()->id;

    $valid_component_ids = PCComponent::select('id')->get()->pluck('id')->toArray();
    $setups = json_decode($values['components'], true);

    $setups_to_save = [];
    $date = Carbon::now()->toString();
    foreach ($setups as $value) {
      // If component ID is invalid
      if (!in_array($value['id_component'], $valid_component_ids)) {
        continue;
      }

      $data = [
        'id_owner' => $id_owner,
        'id_info' => $id_info,
        'id_component' => $value['id_component'],
        'count' => $value['count'] ? intval($value['count']) : 0,
        'is_hidden' => $value['is_hidden'] ? to_boolean($value['is_hidden']) : null,
        'created_at' => $date,
        'updated_at' => $date,
      ];

      array_push($setups_to_save, $data);
    }

    PCSetup::where('id_owner', $id_owner)
      ->where('id_info', $id_info)
      ->forceDelete();

    PCSetup::insert($setups_to_save);
    PCSetup::refreshAutoIncrements();
  }

  public function import(array $contents) {
    $import = [];

    $owners = PCOwner::select('id', 'name')->get()->makeVisible('id');
    $infos = PCInfo::select('id', 'id_owner', 'label')->get()->makeVisible('id');
    $components = PCComponent::select('id', 'name', 'description')->get();

    foreach ($contents as $item) {
      if (!empty($item)) {
        if (!$item->id_owner) continue;
        if (!$item->id_info) continue;
        if (!$item->id_component) continue;
        if (!$item->component_name) continue;

        // Find the actual owner ID
        $actual_owner = $owners->first(fn($owner) => $owner->name == $item->id_owner);
        if (!$actual_owner) continue;

        // Find the actual info ID
        $actual_info = $infos->first(fn($info) => $info->label == $item->id_info);
        if (!$actual_info) continue;
        if ($actual_info->id_owner !== $actual_owner->id) continue;

        // Find the actual component to retrieve component ID
        $actual_component = $components->first(function ($cmp) use ($item) {
          return $cmp->name == $item->component_name &&
            $cmp->description == $item->component_description;
        });

        if (!$actual_component) continue;

        $data = [
          'id_owner' => $actual_owner->id,
          'id_info' => $actual_info->id,
          'id_component' => $actual_component->id,
          'count' => $item->count,
          'is_hidden' => to_boolean($item->is_hidden, true),

          'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
          'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ];

        array_push($import, $data);
      }
    }

    PCSetup::refreshAutoIncrements();
    PCSetup::insert($import);
    PCSetup::refreshAutoIncrements();

    return count($import);
  }
}
