<?php

namespace App\Repositories;

use Illuminate\Support\Str;

use App\Models\Catalog;
use App\Models\Partial;

use App\Resources\Partial\PartialResource;

class CatalogRepository {

  public function getAll() {
    return Catalog::select('uuid', 'year', 'season')
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
    $catalog = Catalog::select('id', 'uuid', 'year', 'season')
      ->where('uuid', $uuid)
      ->firstOrFail();

    $partials = Partial::where('id_catalog', $catalog->id)
      ->orderBy('title')
      ->orderBy('created_at', 'asc')
      ->get();

    return [
      'data' => PartialResource::collection($partials),
      'stats' => $catalog,
    ];
  }

  public function add(array $values) {
    $values['uuid'] = Str::uuid()->toString();

    return Catalog::create($values);
  }

  public function edit(array $values, $id) {
    return Catalog::where('uuid', $id)
      ->firstOrFail()
      ->update($values);
  }

  public function delete($id) {
    return Catalog::where('uuid', $id)
      ->firstOrFail()
      ->delete();
  }
}
