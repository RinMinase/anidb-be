<?php

namespace App\Repositories;

use App\Models\Group;

class GroupRepository {

  public function getAll() {
    return Group::orderBy('id')->pluck('name');
  }

  public function add(array $values) {
    return Group::create($values);
  }

  public function delete($id) {
    return Group::where('id', $id)
      ->firstOrFail()
      ->delete();
  }
}
