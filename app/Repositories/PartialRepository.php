<?php

namespace App\Repositories;

use Illuminate\Support\Str;

use App\Models\Catalog;
use App\Models\Partial;
use App\Models\Priority;

class PartialRepository {

  public function get($uuid) {
    return Partial::select('title', 'id_priority')
      ->addSelect('partials.uuid as uuid')
      ->addSelect('catalogs.uuid as id_catalogs')
      ->leftJoin('catalogs', 'catalogs.id', '=', 'partials.id_catalogs')
      ->where('partials.uuid', $uuid)
      ->firstOrFail();
  }

  public function add(array $values) {
    $catalog = Catalog::where('uuid', $values['id_catalogs'])->firstOrFail();

    $values['uuid'] = Str::uuid()->toString();
    $values['id_catalogs'] = $catalog->id;

    return Partial::create($values);
  }

  public function add_multiple(array $values) {
    $catalog = Catalog::create([
      'uuid' => Str::uuid()->toString(),
      'season' => $values['season'],
      'year' => $values['year'],
    ]);

    $count = 0;

    if (!empty($values['data']['low'])) {
      $priority = Priority::where('priority', 'Low')->first();

      foreach ($values['data']['low'] as $item) {
        $data = [
          'uuid' => Str::uuid()->toString(),
          'id_catalogs' => $catalog->id,
          'id_priority' => $priority->id,
          'title' => $item,
        ];

        Partial::create($data);
        $count++;
      }
    }

    if (!empty($values['data']['normal'])) {
      $priority = Priority::where('priority', 'Normal')->first();

      foreach ($values['data']['normal'] as $item) {
        $data = [
          'uuid' => Str::uuid()->toString(),
          'id_catalogs' => $catalog->id,
          'id_priority' => $priority->id,
          'title' => $item,
        ];

        Partial::create($data);
        $count++;
      }
    }

    if (!empty($values['data']['high'])) {
      $priority = Priority::where('priority', 'High')->first();

      foreach ($values['data']['high'] as $item) {
        $data = [
          'uuid' => Str::uuid()->toString(),
          'id_catalogs' => $catalog->id,
          'id_priority' => $priority->id,
          'title' => $item,
        ];

        Partial::create($data);
        $count++;
      }
    }

    return $count;
  }

  public function edit(array $values, $uuid) {
    $catalog = Catalog::where('uuid', $values['id_catalogs'])->firstOrFail();
    $values['id_catalogs'] = $catalog->id;

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

  public function delete($uuid) {
    return Partial::where('uuid', $uuid)
      ->firstOrFail()
      ->delete();
  }
}
