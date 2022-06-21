<?php

namespace App\Repositories;

use App\Models\Sequence;

class SequenceRepository {

  public function getAll() {
    return Sequence::all();
  }
}
