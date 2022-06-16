<?php

namespace App\Repositories;

use App\Models\Catalog;
use App\Models\Partial;

class CatalogRepository {

  public function getAll() {
    return Catalog::orderBy('order', 'desc')
      ->orderBy('created_at', 'asc')
      ->get();
  }

  public function get($id) {
    return Partial::where('id_catalogs', $id)
      ->orderBy('created_at', 'asc')
      ->get();
  }

  public function add(array $values) {
    return Catalog::create($values);
  }

  public function edit(array $values, $id) {
    return Catalog::whereId($id)->update($values);
  }

  public function delete($id) {
    return Catalog::findOrFail($id)->delete();
  }
}
