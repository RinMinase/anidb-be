<?php

namespace App\Repositories;

use App\Models\Group;

class GroupRepository {

  public function getAll() {
    return Group::orderBy('id')->pluck('name');
  }
}
