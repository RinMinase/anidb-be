<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait RefreshableAutoIncrements {
  public static function refreshAutoIncrements() {
    $class = new static;
    $table = $class->getTable();
    $pkey = $class->getKeyName();

    $current_db = env('DB_CONNECTION', 'pgsql');

    if ($current_db === 'pgsql') {
      $max = DB::table($table)->max($pkey) + 1;
      DB::statement('ALTER SEQUENCE ' . $table . '_' . $pkey . '_seq RESTART WITH ' . $max);
    } else if ($current_db === 'mysql') {
      $max = DB::table($table)->max($pkey) + 1;
      DB::statement('ALTER TABLE ' . $table . ' AUTO_INCREMENT = ' . $max);
    }
  }
}
