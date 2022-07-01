<?php

namespace App\Repositories;

use Illuminate\Support\Str;

use App\Models\Catalog;
use App\Models\Partial;

class CatalogRepository {

  public function getAll() {
    return Catalog::select()
      ->orderBy('year', 'desc')
      ->orderByRaw('CASE
        WHEN season=\'Winter\' THEN 1
        WHEN season=\'Spring\' THEN 2
        WHEN season=\'Summer\' THEN 3
        WHEN season=\'Fall\' THEN 4
        ELSE 0 END
      ')->get();
  }

  public function get($uuid) {
    $catalog = Catalog::where('uuid', $uuid)->firstOrFail();

    return Partial::where('id_catalogs', $catalog->id)
      ->orderBy('created_at', 'asc')
      ->get();
  }

  public function add(array $values) {
    $values['uuid'] = Str::uuid()->toString();

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
