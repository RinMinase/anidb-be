<?php

namespace App\Repositories;

use Fuse\Fuse;
use Illuminate\Support\Str;

use App\Exceptions\Partial\ParsingException;

use App\Models\Catalog;
use App\Models\Partial;
use App\Models\Priority;

use App\Resources\Partial\PartialWithCatalogResource;

class PartialRepository {

  public function getAll(array $values) {
    // Search Parameters
    $query = $values['query'] ?? '';

    // Ordering Parameters
    $column = $values['column'] ?? 'id_catalog';
    $order = $values['order'] ?? 'asc';

    // Pagination Parameters
    $limit = isset($values['limit']) ? intval($values['limit']) : 30;
    $page = isset($values['page']) ? intval($values['page']) : 1;
    $skip = ($page > 1) ? ($page * $limit - $limit) : 0;

    $data = Partial::select()->with('catalog');
    $fuzzy_ids = [];

    if (!empty($query)) {
      $names = Partial::select('uuid', 'title')->get()->toArray();

      $fuse = new Fuse($names, [
        'keys' => ['title'],
        'threshold' => 0.4,
      ]);

      $fuzzy_names = $fuse->search($query);

      foreach ($fuzzy_names as $fuzzy_name) {
        $fuzzy_ids[] = $fuzzy_name['item']['uuid'];
      }

      if (count($fuzzy_ids)) {
        $case_string = 'CASE ';
        foreach ($fuzzy_ids as $key => $fuzzy_id) {
          $data = $data->orWhere('uuid', $fuzzy_id);
          $case_string .= 'WHEN uuid=\'' . $fuzzy_id . '\' THEN ' . $key + 1 . ' ';
        }
        $case_string .= 'END';

        if (isset($column) && isset($order)) {
          $nulls = $order === 'asc' ? 'first' : 'last';
          $data = $data->orderByRaw($column . ' ' . $order . ' NULLS ' . $nulls);
        } else {
          // if no order and column, sort by fuzzy search
          $data = $data->orderByRaw($case_string);
        }

        $data = $data->orderBy('id');
      }
    } else {
      if (isset($column) && isset($order)) {
        $nulls = $order === 'asc' ? 'first' : 'last';
        $data = $data->orderByRaw($column . ' ' . $order . ' NULLS ' . $nulls);
      }
    }

    $total = $data->count();
    $total_pages = intval(ceil($total / $limit));
    $has_next = $page < $total_pages;

    $data = $data->skip($skip)->paginate($limit);

    if (!empty($needle) && !count($fuzzy_ids)) {
      $data = [];
    }

    $return_value['data'] = PartialWithCatalogResource::collection($data);

    $return_value['meta'] = [
      'page' => $page,
      'limit' => $limit,
      'results' => count($data),
      'total_results' => $total,
      'total_pages' => $total_pages,
      'has_next' => $has_next,
    ];

    return $return_value;
  }

  public function get($uuid) {
    return Partial::select('title', 'id_priority', 'priority')
      ->addSelect('partials.uuid as uuid')
      ->addSelect('catalogs.uuid as id_catalog')
      ->leftJoin('catalogs', 'catalogs.id', '=', 'partials.id_catalog')
      ->leftJoin('priorities', 'priorities.id', '=', 'id_priority')
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
    $this->validate_partial_entries($values);

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

    $this->validate_partial_entries($values);

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

  private function validate_partial_entries(array $values) {
    if (!empty($values['data']['low'])) {
      foreach ($values['data']['low'] as $item) {
        if (strlen($item) > 256) {
          throw new ParsingException('low');
        }
      }
    }

    if (!empty($values['data']['normal'])) {
      foreach ($values['data']['normal'] as $item) {
        if (strlen($item) > 256) {
          throw new ParsingException('normal');
        }
      }
    }

    if (!empty($values['data']['high'])) {
      foreach ($values['data']['high'] as $item) {
        if (strlen($item) > 256) {
          throw new ParsingException('high');
        }
      }
    }
  }
}
