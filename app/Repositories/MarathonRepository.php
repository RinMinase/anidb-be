<?php

namespace App\Repositories;

use App\Models\Marathon;

class MarathonRepository {

  public function getAll() {
    return Marathon::all();
  }
}
