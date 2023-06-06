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
      ->addSelect('catalogs.uuid as id_catalog')
      ->leftJoin('catalogs', 'catalogs.id', '=', 'partials.id_catalog')
      ->where('partials.uuid', $uuid)
      ->firstOrFail();
  }

  public function add(array $values) {
    $catalog = Catalog::where('uuid', $values['id_catalog'])->firstOrFail();

    $values['uuid'] = Str::uuid()->toString();
    $values['id_catalog'] = $catalog->id;

    return Partial::create($values);
  }

  public function add_multiple(array $values) {
    $catalog = Catalog::create([
      'uuid' => Str::uuid()->toString(),
      'season' => $values['season'],
      'year' => $values['year'],
    ]);

    return $this->add_partial_data($values, $catalog);
  }

  public function edit(array $values, $uuid) {
    $catalog = Catalog::where('uuid', $values['id_catalog'])->firstOrFail();
    $values['id_catalog'] = $catalog->id;

    return Partial::where('uuid', $uuid)->update($values);
  }

  public function edit_multiple(array $values, $uuid) {
    $catalog = Catalog::where('uuid', $uuid)->firstOrFail();

    $catalog->year = $values['year'];
    $catalog->season = $values['season'];
    $catalog->save();

    Partial::where('id_catalog', $catalog->id)->delete();

    return $this->add_partial_data($values, $catalog);
  }

  public function delete($uuid) {
    return Partial::where('uuid', $uuid)
      ->firstOrFail()
      ->delete();
  }

  private function add_partial_data(array $values, $catalog) {
    $count = 0;

    if (!empty($values['data']['low'])) {
      $priority = Priority::where('priority', 'Low')->first();

      foreach ($values['data']['low'] as $item) {
        $data = [
          'uuid' => Str::uuid()->toString(),
          'id_catalog' => $catalog->id,
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
          'id_catalog' => $catalog->id,
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
          'id_catalog' => $catalog->id,
          'id_priority' => $priority->id,
          'title' => $item,
        ];

        Partial::create($data);
        $count++;
      }
    }

    return $count;
  }
}
