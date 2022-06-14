<?php

namespace App\Repositories;

use App\Models\Catalog;

class CatalogRepository {

  public function getAll() {
    return Catalog::orderBy('order', 'desc')
      ->orderBy('created_at', 'asc')
      ->with('partials')
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
