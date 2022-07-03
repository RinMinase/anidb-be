<?php

namespace App\Repositories;

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
    return Partial::create($values);
  }

  public function edit(array $values, $id) {
    return Partial::whereId($id)->update($values);
  }

  public function delete($id) {
    return Partial::findOrFail($id)->delete();
  }
}
