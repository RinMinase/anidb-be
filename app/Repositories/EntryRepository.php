<?php

namespace App\Repositories;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

use App\Models\Entry;
use App\Models\EntryRewatch;
use App\Models\Bucket;
use App\Models\Sequence;

use App\Resources\Entry\EntryBySeqDataCollection;

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
      ->orderBy('title')
      ->orderBy('id')
      ->skip($skip)
      ->paginate($limit);

    return $data;
  }

  public function get($id) {
    return Entry::where('entries.uuid', $id)
      ->with('offquels')
      ->with('rewatches', function ($rewatches) {
        $rewatches->orderBy('date_rewatched', 'desc');
      })
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
      })
      ->whereNotNull('rewatch.date_rewatched')
      ->orWhereNotNull('date_finished')
      ->orderByRaw('
        CASE WHEN date_rewatched > date_finished
        THEN date_rewatched ELSE date_finished
        END DESC
      ');

    return $data->limit(20)->get();
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
    $entries = Entry::select('release_season', 'release_year')
      ->addSelect(DB::raw('count(*) as count'))
      ->groupBy('release_year', 'release_season')
      ->orderBy('release_year', 'desc')
      ->orderByRaw('CASE
        WHEN release_season=\'Winter\' THEN 1
        WHEN release_season=\'Spring\' THEN 2
        WHEN release_season=\'Summer\' THEN 3
        WHEN release_season=\'Fall\' THEN 4
        ELSE 0 END
      ')->get();

    $data = [
      'Uncategorized' => [],
    ];

    foreach ($entries as $entry) {
      $to_push = [
        'release_season' => $entry->release_season,
        'count' => $entry->count,
      ];

      if ($entry->release_year == null) {
        array_push($data['Uncategorized'], $to_push);
      } else {
        if (!array_key_exists($entry->release_year, $data)) {
          $data[$entry->release_year] = [];
        }

        array_push($data[$entry->release_year], $to_push);
      }
    }

    return $data;
  }

  public function getBySeason($year) {
    function entriesBySeason($season, $year) {
      return Entry::select('uuid', 'title')
        ->where('release_year', '=', $year)
        ->where('release_season', '=', $season)
        ->get();
    }

    $data = [
      'Winter' => entriesBySeason('Winter', $year),
      'Spring' => entriesBySeason('Spring', $year),
      'Summer' => entriesBySeason('Summer', $year),
      'Fall' => entriesBySeason('Fall', $year),
      'Uncategorized' => entriesBySeason(null, $year),
    ];

    return $data;
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

  public function getBySequence($id) {
    $sequence = Sequence::where('id', $id)->first();

    $rewatch_subquery = EntryRewatch::select('id_entries', 'date_rewatched')
      ->whereIn('date_rewatched', function ($where_in) use ($sequence) {
        $where_in->select(DB::raw('max(date_rewatched)'))
          ->from('entries_rewatch')
          ->where('date_rewatched', '>=', $sequence->date_from)
          ->where('date_rewatched', '<=', $sequence->date_to)
          ->groupBy('id_entries');
      });

    $subquery = Entry::select()
      ->addSelect(DB::raw('
        CASE
          WHEN rewatch.date_rewatched IS NULL AND date_finished IS NOT NULL
          THEN date_finished
          ELSE rewatch.date_rewatched
        END AS date_lookup'))
      ->with('rating')
      ->leftJoinSub($rewatch_subquery, 'rewatch', function ($join) {
        $join->on('entries.id', '=', 'rewatch.id_entries');
      })
      ->whereNotNull('rewatch.date_rewatched')
      ->orWhereNotNull('date_finished')
      ->orderBy('date_lookup');

    $data = DB::query()->fromSub($subquery, 'data')
      ->where('data.date_lookup', '>=', $sequence->date_from)
      ->where('data.date_lookup', '<=', $sequence->date_to)
      ->get();

    return [
      'data' => EntryBySeqDataCollection::collection($data),
      'stats' => $this->calculate_sequence_stats($data, $sequence),
    ];
  }

  public function add(FormRequest $values) {
    $values['uuid'] = Str::uuid()->toString();
    $id = Entry::insertGetId($values->except([
      'season_number',
      'season_first_title_id',
      'prequel_id',
      'sequel_id',
    ]));

    $this->update_season($values, $id);
    $this->update_prequel_sequel($values, $id);

    LogRepository::generateLogs('entry', $values['uuid'], null, 'add');
  }

  public function edit(FormRequest $values, $uuid) {
    $entry = Entry::where('uuid', $uuid)->first();

    $entry->update($values->except([
      '_method',
      'season_number',
      'season_first_title_id',
      'prequel_id',
      'sequel_id',
    ]));

    $this->update_season($values, $entry->id);
    $this->update_prequel_sequel($values, $entry->id);

    LogRepository::generateLogs('entry', $entry->uuid, null, 'edit');
  }

  public function delete($id) {
    return Entry::where('uuid', $id)
      ->firstOrFail()
      ->delete();
  }

  public function import(array $values) {
    $repo = new EntryInputRepository();

    return $repo->import($values);
  }

  private function update_season($values, $inserted_id) {
    $has_season = empty($values['season_number'])
      || $values['season_number'] === 1;

    if ($has_season) {
      Entry::where('id', $inserted_id)
        ->update([
          'season_number' => 1,
          'season_first_title_id' => $inserted_id,
        ]);
    } else {
      $entry = Entry::where('uuid', $values['season_first_title_id'])
        ->first();

      Entry::where('id', $inserted_id)
        ->update([
          'season_number' => $values['season_number'],
          'season_first_title_id' => $entry->id ?? null,
        ]);
    }
  }

  private function update_prequel_sequel($values, $inserted_id) {
    if (!empty($values['prequel_id'])) {
      $entry = Entry::where('uuid', $values['prequel_id'])
        ->first();

      Entry::where('id', $inserted_id)
        ->update(['prequel_id' => $entry->id ?? null]);
    }

    if (!empty($values['sequel_id'])) {
      $entry = Entry::where('uuid', $values['sequel_id'])
        ->first();

      Entry::where('id', $inserted_id)
        ->update(['sequel_id' => $entry->id ?? null]);
    }
  }

  private function calculate_sequence_stats($data, $sequence) {
    $start_date = Carbon::parse($sequence->date_from);
    $end_date = Carbon::parse($sequence->date_to);
    $total_days = $end_date->diffInDays($start_date);

    $total_size = 0;
    $total_eps = 0;
    $total_titles = count($data);
    $titles_per_day = round($total_titles / $total_days, 2);

    $quality_2160 = 0;
    $quality_1080 = 0;
    $quality_720 = 0;
    $quality_480 = 0;
    $quality_360 = 0;

    foreach ($data as $item) {
      if ($item->id_quality === 1) $quality_2160++;
      if ($item->id_quality === 2) $quality_1080++;
      if ($item->id_quality === 3) $quality_720++;
      if ($item->id_quality === 4) $quality_480++;
      if ($item->id_quality === 5) $quality_360++;

      if ($item->episodes) $total_eps += $item->episodes;
      if ($item->ovas) $total_eps += $item->ovas;
      if ($item->specials) $total_eps += $item->specials;

      if ($item->filesize) $total_size += $item->filesize;
    }

    return [
      'titles_per_day' => $titles_per_day,
      'eps_per_day' => round($total_eps / $total_days, 2),
      'quality_2160' => $quality_2160,
      'quality_1080' => $quality_1080,
      'quality_720' => $quality_720,
      'quality_480' => $quality_480,
      'quality_360' => $quality_360,
      'total_titles' => $total_titles,
      'total_eps' => $total_eps,
      'total_size' => parse_filesize($total_size),
      'total_days' => $total_days,
      'start_date' => $start_date->format('M d, Y'),
      'end_date' => $end_date->format('M d, Y'),
    ];
  }
}
