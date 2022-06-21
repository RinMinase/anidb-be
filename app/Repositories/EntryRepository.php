<?php

namespace App\Repositories;

use Illuminate\Http\Request;

use App\Models\Entry;

class EntryRepository {

  public function getAll(Request $request) {
    $needle = $request->query('needle', '');
    $haystack = $request->query('haystack', 'title');
    $column = $request->query('column', 'id_quality');
    $order = $request->query('order', 'asc');
    $limit = $request->query('limit', 30);
    $page = $request->query('page', 1);
    $skip = ($page > 1) ? ($page * $limit - $limit) : 0;

    $data = Entry::select()
      ->with('rating')
      ->where($haystack, 'like', '%' . $needle . '%');

    if ($column === 'date_rewatched') {
      $data = $data->with(['rewatches' => function ($sub_query) {
        $sub_query->orderBy('date_rewatched', 'desc');
      }]);
    } else {
      $data = $data->orderBy($column, $order);
    }

    $data = $data->orderBy('id')
      ->skip($skip)
      ->paginate($limit);

    return $data;
  }

  public function get($id) {
    return Entry::where('entries.uuid', $id)
      ->with('offquels')
      ->with('rewatches')
      ->with('rating')
      ->first();
  }

  public function add(array $values) {
    return Entry::create($values);
  }

  public function edit(array $values, $id) {
    return Entry::whereId($id)->update($values);
  }

  public function delete($id) {
    return Entry::where('uuid', $id)
      ->firstOrFail()
      ->delete();
  }
}
