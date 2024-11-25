<?php

namespace App\Repositories;

use Illuminate\Support\Str;

use App\Models\PCInfo;

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
}
