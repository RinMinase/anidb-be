<?php

namespace App\Repositories;

use App\Models\Quality;

class QualityRepository {

  public function getAll() {
    return Quality::select('id', 'quality')
      ->orderBy('id', 'asc')
      ->get();
  }
}
