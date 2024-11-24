<?php

namespace App\Repositories;

use Illuminate\Support\Str;

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
      'uuid' => Str::uuid()->toString(),
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
