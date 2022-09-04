<?php

namespace App\Repositories;

use Illuminate\Support\Str;

use App\Models\Catalog;
use App\Models\Partial;

class PartialRepository {

  public function get($uuid) {
    return Partial::where('uuid', $uuid)->firstOrFail();
  }

  public function add(array $values) {
    $catalog = Catalog::where('uuid', $values['id_catalogs'])->firstOrFail();

    $values['uuid'] = Str::uuid()->toString();
    $values['id_catalogs'] = $catalog->id;

    return Partial::create($values);
  }

  public function add_multiple(array $values, $catalog_uuid) {
    $catalog = Catalog::where('uuid', $catalog_uuid)->firstOrFail();
    $count = 0;

    foreach ($values as $value) {
      $data = array_merge((array) $value, [
        'uuid' => Str::uuid()->toString(),
        'id_catalogs' => $catalog->id,
      ]);

      Partial::create($data);
      $count++;
    }

    return $count;
  }

  public function edit(array $values, $uuid) {
    return Partial::where('uuid', $uuid)->update($values);
  }

  public function edit_multiple(array $values,) {
    $count = 0;

    foreach ($values as $value) {
      Partial::where('uuid', $value->id)
        ->update([
          'title' => $value->title,
          'id_priority' => $value->id_priority,
        ]);
      $count++;
    }

    return $count;
  }

  public function delete($id) {
    return Partial::findOrFail($id)->delete();
  }
}
