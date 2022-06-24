<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Entry;
use App\Models\EntryRewatch;
use App\Models\Bucket;

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
    $ctrTitles = array_fill(0, 27, 0);
    $ctrSizes = array_fill(0, 27, 0);
    $data = Entry::select('title', 'filesize')
      ->orderBy('title', 'asc')
      ->get();

    foreach ($data as $item) {
      if ($item->title) {
        $first_letter = $item->title[0];
        if (is_numeric($first_letter)) {
          $ctrTitles[0]++;
          $ctrSizes[0] += $item->filesize;
        } else {
          // convert first letter to ascii value
          // A = 65
          $ascii = ord(strtoupper($first_letter));
          $ctrTitles[$ascii - 64]++;
          $ctrSizes[$ascii - 64] += $item->filesize;
        }
      }
    }

    $letters = [];
    foreach ($ctrTitles as $index => $item) {
      if ($index == 0) {
        $letters['#'] = [
          'titles' => $item,
          'filesize' => parse_filesize($ctrSizes[$index]),
        ];
      } else {
        $letters[chr($index + 64)] = [
          'titles' => $item,
          'filesize' => parse_filesize($ctrSizes[$index]),
        ];
      }
    }

    return $letters;
  }

  public function getByLetter($letter) {
    $data = Entry::select()
      ->with('rating')
      ->where('title', 'like', $letter[0] . '%')
      ->orderBy('title', 'asc')
      ->orderBy('id')
      ->get();

    return $data;
  }

  public function getByYear() {
    $data = Entry::select('release_season', 'release_year')
      ->addSelect(DB::raw('count(*) as count'))
      ->groupBy('release_year', 'release_season')
      ->orderBy('release_year', 'desc')
      ->orderByRaw('CASE
        WHEN release_season=\'Winter\' THEN 1
        WHEN release_season=\'Spring\' THEN 2
        WHEN release_season=\'Summer\' THEN 3
        WHEN release_season=\'Fall\' THEN 4
        ELSE 0 END
      ');

    return $data->get();
  }

  public function getBySeason($year) {
    $data = Entry::select()
      ->with('rating')
      ->where('release_year', '=', $year)
      ->orderByRaw('CASE
        WHEN release_season=\'Winter\' THEN 1
        WHEN release_season=\'Spring\' THEN 2
        WHEN release_season=\'Summer\' THEN 3
        WHEN release_season=\'Fall\' THEN 4
        ELSE 0 END
      ');

    return $data->get();
  }

  public function getBuckets() {
    $buckets = Bucket::all();
    $returnValue = [];
    $bucket_full_size = 0;
    $entries_full_size = 0;
    $count_full_size = 0;

    foreach ($buckets as $bucket) {
      $bucket_full_size += $bucket->size;

      $entries = Entry::select('filesize')
        ->whereBetween('title', [$bucket->from, $bucket->to])
        ->orWhereBetween(
          'title',
          [
            strtoupper($bucket->from),
            strtoupper($bucket->to)
          ]
        )->get();

      $entries_size = 0;
      foreach ($entries as $entry) {
        $entries_size += $entry->filesize;
      }

      $free = $bucket->size - $entries_size;
      $used = $entries_size;
      $total = $bucket->size;
      $percent = round(($used / $total) * 100, 0);
      $titles = count($entries);

      array_push($returnValue, [
        'from' => $bucket->from,
        'to' => $bucket->to,
        'free' => parse_filesize($free),
        'freeTB' => null,
        'used' => parse_filesize($used),
        'percent' => $percent,
        'total' => parse_filesize($total),
        'titles' => $titles,
      ]);

      $entries_full_size += $entries_size;
      $count_full_size += $titles;
    }

    $free = $bucket_full_size - $entries_full_size;
    $percent = round(($entries_full_size / $bucket_full_size) * 100, 0);

    array_push($returnValue, [
      'from' => null,
      'to' => null,
      'free' => parse_filesize($free),
      'freeTB' => parse_filesize($free, 'TB'),
      'used' => parse_filesize($entries_full_size),
      'percent' => $percent,
      'total' => parse_filesize($bucket_full_size),
      'titles' => $count_full_size,
    ]);

    return $returnValue;
  }

  public function getByBucket($id) {
    $bucket = Bucket::where('id', $id)->firstOrFail();

    $data = Entry::select('uuid', 'id_quality', 'title', 'filesize')
      ->whereBetween('title', [$bucket->from, $bucket->to])
      ->orWhereBetween(
        'title',
        [
          strtoupper($bucket->from),
          strtoupper($bucket->to)
        ]
      );

    if ($bucket->from === 'a') {
      $data = $data->orWhereBetween('title', [0, 9]);
    }

    $data = $data->orderBy('title', 'asc')->get();

    return $data;
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
