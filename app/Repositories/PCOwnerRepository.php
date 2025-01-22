<?php

namespace App\Repositories;

use Illuminate\Support\Str;

use App\Models\PCOwner;

class PCOwnerRepository {

  public function getAll(array $values) {
    $show_hidden = $values['show_hidden'];

    return PCOwner::with(['infos' => function ($query) use ($show_hidden) {
      $query->orderBy('label');

      if (!$show_hidden) {
        $query->where('is_hidden', false);
      }
    }])
      ->orderBy('name')
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
