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

  public function import(array $contents) {
    $import = [];

    foreach ($contents as $item) {
      if (!empty($item) && is_string($item)) {
        $data = [
          'uuid' => Str::uuid()->toString(),
          'name' => $item,
        ];

        array_push($import, $data);
      }
    }

    PCOwner::refreshAutoIncrements();
    PCOwner::insert($import);
    PCOwner::refreshAutoIncrements();

    return count($import);
  }
}
