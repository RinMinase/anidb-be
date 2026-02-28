<?php

namespace App\Models\Helpers;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

// Notes:
// Make sure to use ::create (add) and ::find (update) to trigger custom casting methods
// You could also use ::where->firstOrFail->update or ::where->first?->update
// Model::where->update bypasses this casting methods below

class ArrayCast implements CastsAttributes {

  // Converts Postgres "{item1,item2}" to PHP ["item1", "item2"]
  public function get($model, $key, $value, $attributes) {
    if (is_null($value) || $value === '{}') return [];

    return str_getcsv(trim($value, '{}'));
  }

  // Converts PHP ["item1", "item2"] to Postgres "{item1,item2}"
  public function set($model, $key, $value, $attributes) {
    if (is_null($value)) return null;

    $array = is_array($value) ? $value : [$value];
    $formatted = array_map(function ($item) {
      $item = str_replace('"', '\"', $item);
      return '"' . $item . '"';
    }, $array);

    return '{' . implode(',', $formatted) . '}';
  }
}
