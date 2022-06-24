<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Entry;
use App\Models\EntryRewatch;

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
      ->where($haystack, 'like', '%' . $needle . '%')
      ->orderBy($column, $order)
      ->orderBy('id')
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

  public function getLast() {
    $sub_query = EntryRewatch::select('id_entries', 'date_rewatched')
      ->whereIn('date_rewatched', function ($where_in) {
        $where_in->select(DB::raw('max(date_rewatched)'))
          ->from('entries_rewatch')
          ->groupBy('id_entries');
      });

    $data = Entry::select()
      ->with('rating')
      ->leftJoinSub($sub_query, 'rewatch', function ($join) {
        $join->on('entries.id', '=', 'rewatch.id_entries');
      })->orderByRaw('
        CASE WHEN date_rewatched > date_finished
        THEN date_rewatched ELSE date_finished
        END DESC
      ');

    return $data->get();
  }

  public function getByName() {
    return [];
  }

  public function getByLetter($letter) {
    return [];
  }

  public function getByYear() {
    return [];
  }

  public function getBySeason($year) {
    return [];
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
