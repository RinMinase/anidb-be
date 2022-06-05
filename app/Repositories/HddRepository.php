<?php

namespace App\Repositories;

use App\Models\Hdd;

class HddRepository {

  public function getAll()
  {
    return Hdd::all();
  }

}
