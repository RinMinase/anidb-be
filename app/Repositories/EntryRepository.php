<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Fuse\Fuse;
use Cloudinary;

use App\Models\Entry;
use App\Models\EntryRewatch;
use App\Models\Bucket;
use App\Models\Sequence;

use App\Resources\Entry\EntryBySeqDataCollection;
use App\Resources\Entry\EntryCollection;

class EntryRepository {

  public function getAll(array $values) {
    // Search Parameters
    $needle = $values['needle'] ?? '';
    $haystack = $values['haystack'] ?? 'title';

    // Ordering Parameters
    $column = $values['column'] ?? 'id_quality';
    $order = $values['order'] ?? 'asc';

    // Pagination Parameters
    $limit = $values['limit'] ?? 30;
    $page = $values['page'] ?? 1;
    $skip = ($page > 1) ? ($page * $limit - $limit) : 0;

    if (empty($needle)) {
      $total = Entry::count();
      $total_pages = ceil($total / $limit);
      $has_next = intval($page) < $total_pages;
    }

    $data = Entry::select()
      ->with('rating');

    if ($haystack === 'title' && !empty($needle)) {
      $names = Entry::select('uuid', 'title')->get()->toArray();

      $fuse = new Fuse($names, ['keys' => ['title']]);
      $fuzzy_names = $fuse->search($needle, ['limit' => 10]);

      $fuzzy_ids = [];
      foreach ($fuzzy_names as $fuzzy_name) {
        $fuzzy_ids[] = $fuzzy_name['item']['uuid'];
      }

      $case_string = 'CASE ';
      foreach ($fuzzy_ids as $key => $fuzzy_id) {
        $data = $data->orWhere('uuid', $fuzzy_id);
        $case_string .= 'WHEN uuid=\'' . $fuzzy_id . '\' THEN ' . $key + 1 . ' ';
      }
      $case_string .= 'END';

      $data = $data->orderByRaw($case_string);
    } else {
      $data = $data->where($haystack, 'ilike', '%' . $needle . '%')
        ->orderBy($column, $order)
        ->orderBy('title')
        ->orderBy('id');
    }

    $data = $data->skip($skip)
      ->paginate($limit);

    $return_value = [
      'data' => EntryCollection::collection($data),
    ];

    if (empty($needle)) {
      $return_value['meta'] = [
        'page' => $page,
        'limit' => $limit,
        'total' => $total_pages,
        'has_next' => $has_next,
      ];
    }

    return $return_value;
  }

  public function get($id) {
    $entry = Entry::where('entries.uuid', $id)
      ->with('offquels')
      ->with('rewatches', function ($rewatches) {
        $rewatches->orderBy('date_rewatched', 'desc');
      })
      ->with('rating')
      ->firstOrFail();

    return $entry;
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

    $data = $data->limit(20)->get();

    return [
      'data' => EntryCollection::collection($data),
      'stats' => $this->calculate_last_stats($data),
    ];
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
        array_push($letters, [
          'letter' => '#',
          'titles' => $item,
          'filesize' => parse_filesize($ctrSizes[$index]),
        ]);
      } else {
        array_push($letters, [
          'letter' => chr($index + 64),
          'titles' => $item,
          'filesize' => parse_filesize($ctrSizes[$index]),
        ]);
      }
    }

    return $letters;
  }

  public function getByLetter($letter) {
    $data = Entry::select()
      ->with('rating')
      ->orderBy('title', 'asc')
      ->orderBy('id');

    if (ctype_alpha($letter)) {
      $data = $data->where('title', 'ilike', $letter[0] . '%');
    } elseif ($letter[0] === "0") {
      $data = $data->whereRaw('title ~ \'^[0-9]\'');
    } else {
      throw new ModelNotFoundException;
    }

    return $data->get();
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

    $data = [];

    foreach ($entries as $entry) {
      if ($entry->release_year == null) {
        $to_push = [
          'year' => null,
          'seasons' => null,
          'count' => $entry->count,
        ];

        $data_keys = array_column($data, 'year');
        $data_index = array_search(null, $data_keys);

        if ($data_index !== false) {
          $data[$data_index]['count'] += $entry->count;
        } else {
          array_push($data, $to_push);
        }
      } else {
        $season = $entry->release_season ?? "None";

        $to_push = [
          'year' => $entry->release_year,
          'seasons' => [
            $season => $entry->count,
          ],
          'count' => null,
        ];

        $data_keys = array_column($data, 'year');
        $data_index = array_search($entry->release_year, $data_keys);

        if ($data_index !== false) {
          if (!array_key_exists('seasons', $data[$data_index])) {
            $data[$data_index]['seasons'] = [];
          }

          $data[$data_index]['seasons'][$season] = $entry->count;
        } else {
          array_push($data, $to_push);
        }
      }
    }

    return $data;
  }

  public function getBySeason($year) {
    $year = intval($year, 10);

    if ($year < 1970 && $year > 2999) {
      $year = null;
    }

    function entriesBySeason($season, $year) {
      return Entry::select('uuid', 'title', 'id_quality')
        ->where('release_year', '=', $year)
        ->where('release_season', '=', $season)
        ->get();
    }

    $data = [
      'Winter' => EntryCollection::collection(entriesBySeason('Winter', $year)),
      'Spring' => EntryCollection::collection(entriesBySeason('Spring', $year)),
      'Summer' => EntryCollection::collection(entriesBySeason('Summer', $year)),
      'Fall' => EntryCollection::collection(entriesBySeason('Fall', $year)),
      'Uncategorized' => EntryCollection::collection(entriesBySeason(null, $year)),
    ];

    return $data;
  }

  public function getBuckets($bucket = null) {
    $buckets = $bucket ?? Bucket::all();
    $returnValue = [];
    $bucketValues = [];
    $bucket_full_size = 0;
    $entries_full_size = 0;
    $count_full_size = 0;

    foreach ($buckets as $bucket) {
      $bucket_full_size += $bucket->size;

      $upper_from = strtoupper($bucket->from);
      $upper_to = strtoupper($bucket->to);
      $regex_lower = '\'^[' . $bucket->from . '-' . $bucket->to . ']\'';
      $regex_upper = '\'^[' . $upper_from . '-' . $upper_to . ']\'';

      $entries = Entry::select('filesize')
        ->whereRaw('title ~ ' . $regex_lower)
        ->orWhere(function ($query) use ($regex_upper) {
          $query->whereRaw('title ~ ' . $regex_upper);
        });

      if (strtoupper($bucket->from) === 'A') {
        $entries = $entries->orWhereRaw('title ~ \'^[0-9]\'');
      }

      $entries = $entries->get();

      $entries_size = 0;
      foreach ($entries as $entry) {
        $entries_size += $entry->filesize;
      }

      $free = $bucket->size - $entries_size;
      $used = $entries_size;
      $total = $bucket->size;
      $percent = round(($used / $total) * 100, 0);
      $titles = count($entries);

      array_push($bucketValues, [
        'id' => $bucket->id,
        'from' => $bucket->from,
        'to' => $bucket->to,
        'free' => parse_filesize($free),
        'freeTB' => null,
        'used' => parse_filesize($used),
        'percent' => $percent,
        'total' => parse_filesize($total),
        'rawTotal' => $total,
        'titles' => $titles,
      ]);

      $entries_full_size += $entries_size;
      $count_full_size += $titles;
    }

    $free = $bucket_full_size - $entries_full_size;
    $percent = round(($entries_full_size / $bucket_full_size) * 100, 0);

    array_push($returnValue, [
      'id' => null,
      'from' => null,
      'to' => null,
      'free' => parse_filesize($free),
      'freeTB' => parse_filesize($free, 'TB'),
      'used' => parse_filesize($entries_full_size),
      'percent' => $percent,
      'total' => parse_filesize($bucket_full_size),
      'rawTotal' => $bucket_full_size,
      'titles' => $count_full_size,
    ]);

    $returnValue = array_merge($returnValue, $bucketValues);

    return $returnValue;
  }

  public function getByBucket($id) {
    $bucket = Bucket::where('id', $id)->firstOrFail();
    $range = join(range($bucket->from, $bucket->to,));

    if (strtoupper($bucket->from) === 'A') {
      $range .= '\\d';
    }

    $range = '^[' . $range . ']+';

    $data = Entry::select('uuid', 'id_quality', 'title', 'filesize')
      ->where('title', '~*', $range);

    $data = $data->orderBy('title', 'asc')->get();

    return [
      'data' => EntryCollection::collection($data),
      'stats' => [
        'from' => $bucket->from,
        'to' => $bucket->to,
      ],
    ];
  }

  public function getBySequence($id) {
    $sequence = Sequence::where('id', $id)->firstOrFail();
    $is_seq_future = Carbon::parse($sequence->date_to)->greaterThan(Carbon::now());

    if ($is_seq_future) {
      $date_to = Carbon::now('+8:00')->format('Y-m-d');
    } else {
      $date_to = $sequence->date_to;
    }

    $rewatch_subquery = EntryRewatch::select('id_entries', 'date_rewatched')
      ->whereIn('date_rewatched', function ($where_in) use ($sequence, $date_to) {
        $where_in->select(DB::raw('max(date_rewatched)'))
          ->from('entries_rewatch')
          ->where('date_rewatched', '>=', $sequence->date_from)
          ->where('date_rewatched', '<=', $date_to)
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
      ->leftJoin('qualities', function ($join) {
        $join->on('entries.id_quality', '=', 'qualities.id');
      })
      ->whereNotNull('rewatch.date_rewatched')
      ->orWhereNotNull('date_finished')
      ->orderBy('date_lookup');

    $data = DB::query()->fromSub($subquery, 'data')
      ->where('data.date_lookup', '>=', $sequence->date_from)
      ->where('data.date_lookup', '<=', $date_to)
      ->get();

    return [
      'data' => EntryBySeqDataCollection::collection($data),
      'stats' => $this->calculate_sequence_stats($data, $sequence, $date_to),
    ];
  }

  public function add(array $values) {
    $values['uuid'] = Str::uuid()->toString();
    $values['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
    $values['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');

    $entryInsertColumns = [
      'uuid',
      'created_at',
      'updated_at',
      'id_quality',
      'title',
      'date_finished',
      'duration',
      'filesize',
      'episodes',
      'ovas',
      'specials',
      'encoder_video',
      'encoder_audio',
      'encoder_subs',
      'release_year',
      'release_season',
      'variants',
      'remarks',
      'id_codec_audio',
      'id_codec_video',
      'id_codec_video',
      'codec_hdr',
    ];

    $entryInsertValues = array_filter(
      $values,
      fn ($key) => in_array($key, $entryInsertColumns),
      ARRAY_FILTER_USE_KEY,
    );

    $id = Entry::insertGetId($entryInsertValues);

    $this->update_season($values, $id);
    $this->update_prequel_sequel($values, $id);

    LogRepository::generateLogs('entry', $values['uuid'], null, 'add');
  }

  public function edit(array $values, $uuid) {
    $entry = Entry::where('uuid', $uuid)->firstOrFail();

    $entryUpdateColumns = [
      'id_quality',
      'title',
      'date_finished',
      'duration',
      'filesize',
      'episodes',
      'ovas',
      'specials',
      'encoder_video',
      'encoder_audio',
      'encoder_subs',
      'release_year',
      'release_season',
      'variants',
      'remarks',
      'id_codec_audio',
      'id_codec_video',
      'id_codec_video',
      'codec_hdr',
    ];

    $entryUpdateValues = array_filter(
      $values,
      fn ($key) => in_array($key, $entryUpdateColumns),
      ARRAY_FILTER_USE_KEY,
    );

    $entry->update($entryUpdateValues);

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
    $repo = new EntryImportRepository();

    return $repo->import($values);
  }

  public function upload($image, $uuid) {
    $entry = Entry::where('uuid', $uuid)->firstOrFail();

    if (!empty($entry->image)) {
      $image_id = pathinfo($entry->image);
      Cloudinary::destroy('entries/' . $image_id['filename']);
    }

    $imageSettings = [
      'quality' => '90',
      'folder' => 'entries',
    ];

    $imageURL = Cloudinary::upload($image, $imageSettings)->getSecurePath();

    $entry->image = $imageURL;
    $entry->save();
  }

  public function ratings(array $values, $uuid) {
    $entry = Entry::where('uuid', $uuid)->firstOrFail();

    $entry->rating()->updateOrCreate(['id_entries' => $entry->id], $values);
  }

  public function rewatchAdd(Request $request, $uuid) {
    $entry = Entry::where('uuid', $uuid)->firstOrFail();

    EntryRewatch::insert([
      'uuid' => Str::uuid()->toString(),
      'id_entries' => $entry->id,
      'date_rewatched' => $request->get('date_rewatched'),
    ]);
  }

  public function rewatchDelete($uuid) {
    EntryRewatch::where('uuid', $uuid)
      ->firstOrFail()
      ->delete();
  }

  public function getTitles(?string $id, ?string $needle) {
    if (!empty($needle)) {
      $names = Entry::select('title')
        ->where('uuid', '!=', $id)
        ->get()
        ->toArray();

      $fuse = new Fuse($names, ['keys' => ['title']]);
      $fuzzy_names = $fuse->search($needle, ['limit' => 10]);

      $final_array = [];
      foreach ($fuzzy_names as $fuzzy_name) {
        array_push($final_array, $fuzzy_name['item']['title']);
      }

      return $final_array;
    } else {
      return Entry::select('title')
        ->where('uuid', '!=', $id)
        ->orderBy('title')
        ->take(10)
        ->pluck('title')
        ->toArray();
    }
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
      $prequel = Entry::where('uuid', $values['prequel_id'])->first();

      if ($prequel) {
        Entry::where('id', $inserted_id)
          ->update(['prequel_id' => $prequel->id]);

        if (empty($prequel->sequel_id)) {
          $prequel->sequel_id = $inserted_id;
          $prequel->save();
        }
      }
    }

    if (!empty($values['sequel_id'])) {
      $sequel = Entry::where('uuid', $values['sequel_id'])->first();

      if ($sequel) {
        Entry::where('id', $inserted_id)
          ->update(['sequel_id' => $sequel->id ?? null]);

        if (empty($sequel->prequel_id)) {
          $sequel->prequel_id = $inserted_id;
          $sequel->save();
        }
      }
    }
  }

  private function calc_date_finished($item) {
    $last_date_finished = '';

    if ($item->date_finished) {
      $last_date_finished = $item->date_finished;
    }

    if ($item->date_rewatched) {
      $last_date_finished = $item->date_rewatched;
    }

    return $last_date_finished;
  }

  private function calculate_last_stats($data) {
    if (count($data)) {
      $now = Carbon::now()->addHours(8);

      $date_last_entry = Carbon::parse(
        $this->calc_date_finished($data[0])
      );
      $days_last_entry = $date_last_entry->diffInDays($now);
      $date_last_entry = $date_last_entry->format('M d, Y');

      $date_oldest_entry = Carbon::parse(
        $this->calc_date_finished($data[count($data) - 1])
      );
      $weeks_since_oldest_entry = $date_oldest_entry->floatDiffInWeeks($now);
      $days_oldest_entry = $date_oldest_entry->diffInDays($now);
      $date_oldest_entry = $date_oldest_entry->format('M d, Y');

      $total_titles = count($data);
      $total_cours = 0;
      $total_eps = 0;
      foreach ($data as $item) {
        if ($item->episodes) $total_eps += $item->episodes;
        if ($item->ovas) $total_eps += $item->ovas;
        if ($item->specials) $total_eps += $item->specials;

        if ($item->episodes) {
          if ($item->episodes > 12) {
            $total_cours += round($item->episodes / 12, 0);
          } else {
            $total_cours++;
          }
        }
      }

      $titles_per_week = $total_titles / $weeks_since_oldest_entry;
      $cours_per_week = $total_cours / $weeks_since_oldest_entry;
      $eps_per_week = $total_eps / $weeks_since_oldest_entry;
      $eps_per_day = $total_eps / $days_oldest_entry;

      return [
        'dateLastEntry' => $date_last_entry,
        'daysLastEntry' => $days_last_entry,
        'dateOldestEntry' => $date_oldest_entry,
        'daysOldestEntry' => $days_oldest_entry,
        'totalEps' => $total_eps,
        'totalTitles' => $total_titles,
        'totalCours' => $total_cours,
        'titlesPerWeek' => round($titles_per_week, 2),
        'coursPerWeek' => round($cours_per_week, 2),
        'epsPerWeek' => round($eps_per_week, 2),
        'epsPerDay' => round($eps_per_day, 2),
      ];
    }
  }

  private function calculate_sequence_stats($data, $sequence, $date_to) {
    $start_date = Carbon::parse($sequence->date_from);
    $end_date = Carbon::parse($date_to);
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
