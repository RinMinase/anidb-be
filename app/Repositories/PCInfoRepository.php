<?php

namespace App\Repositories;

use Carbon\Carbon;
use Illuminate\Support\Str;

use App\Models\PCInfo;
use App\Models\PCOwner;

class PCInfoRepository {

  public function getAll() {
    return PCInfo::with('owner')
      ->orderBy('label')
      ->orderBy('id')
      ->get();
  }

  public function get($uuid) {
    $info = PCInfo::with('owner')
      ->with('setups')
      ->where('uuid', $uuid)
      ->firstOrFail();

    return $info;
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
}
