<?php

namespace App\Repositories;

use App\Models\Priority;

class PriorityRepository {

  public function getAll() {
    return Priority::all();
  }
}
