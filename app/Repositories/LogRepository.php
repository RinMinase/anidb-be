<?php

namespace App\Repositories;

use Illuminate\Support\Str;
use Carbon\Carbon;

use App\Models\Log;

class LogRepository {

  public function getAll() {
    return Log::orderBy('created_at', 'desc')
      ->orderBy('id', 'asc')
      ->get();
  }

  public static function generateLogs(
    $table_changed = null,
    $id_changed = null,
    $desc = null,
    $action = null,
  ) {
    $data = [
      'uuid' => Str::uuid()->toString(),
      'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
      'table_changed' => $table_changed,
      'id_changed' => $id_changed,
      'desc' => $desc,
      'action' => $action,
    ];

    Log::create($data);
  }
}
