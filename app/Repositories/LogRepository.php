<?php

namespace App\Repositories;

use App\Models\Log;

class LogRepository {

  public function getAll() {
    return Log::all();
  }
}
