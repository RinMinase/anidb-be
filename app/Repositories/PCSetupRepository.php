<?php

namespace App\Repositories;

use App\Exceptions\JsonParsingException;
use Carbon\Carbon;
use App\Models\PCComponent;
use Illuminate\Support\Str;

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
    foreach ($setups as $value) {
      if (!in_array($value['id_component'], $valid_component_ids)) {
        throw new JsonParsingException();
      }

      $date = Carbon::now()->toString();

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
}
