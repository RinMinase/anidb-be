<?php

namespace App\Repositories;

use App\Models\Catalog;
use App\Models\Partial;

class CatalogRepository {

  public function getAll() {
    return Catalog::orderBy('order', 'asc')
      ->orderBy('created_at', 'asc')
      ->get();
  }

  public function get($uuid) {
    $catalog = Catalog::where('uuid', $uuid)->firstOrFail();

    return Partial::where('id_catalogs', $catalog->id)
      ->orderBy('created_at', 'asc')
      ->get();
  }

  public function add(array $values) {
    return Catalog::create($values);
  }

  public function edit(array $values, $id) {
    return Catalog::where('uuid', $id)->update($values);
  }

  public function delete($id) {
    return Catalog::where('uuid', $id)
      ->firstOrFail()
      ->delete();
  }
}
