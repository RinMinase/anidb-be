<?php

namespace App\Repositories;

use App\Models\PCOwner;

class PCOwnerRepository {

  public function getAll() {
    return PCOwner::orderBy('name')
      ->orderBy('id')
      ->get()
      ->toArray();
  }

  public function add(array $values) {
    return PCOwner::create([
      'name' => $values['name'],
    ]);
  }

  public function edit(array $values, $uuid) {
    return PCOwner::where('uuid', $uuid)
      ->firstOrFail()
      ->update($values);
  }

  public function delete($uuid) {
    return PCOwner::where('uuid', $uuid)
      ->firstOrFail()
      ->delete();
  }
}
