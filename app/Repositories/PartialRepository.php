<?php

namespace App\Repositories;

use Illuminate\Support\Str;

use App\Models\Catalog;
use App\Models\Partial;

class PartialRepository {

  public function getAll($catalog_uuid) {
    $catalog = Catalog::where('uuid', $catalog_uuid)->firstOrFail();

    return Partial::where('id_catalogs', $catalog->id)
      ->orderBy('title')
      ->orderBy('created_at')
      ->get();
  }

  public function add(array $values) {
    $catalog = Catalog::where('uuid', $values['id_catalogs'])->firstOrFail();

    $values['uuid'] = Str::uuid()->toString();
    $values['id_catalogs'] = $catalog->id;

    return Partial::create($values);
  }

  public function edit(array $values, $uuid) {
    return Partial::where('uuid', $uuid)->update($values);
  }

  public function delete($id) {
    return Partial::findOrFail($id)->delete();
  }
}
