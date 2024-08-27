<?php

namespace App\Repositories;

use Error;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Cloudinary\Api\Upload\UploadApi;
use Fuse\Fuse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Enums\EntrySearchHasEnum;
use App\Exceptions\Entry\SearchFilterParsingException;

use App\Models\Entry;
use App\Models\EntryRewatch;
use App\Models\Bucket;
use App\Models\Quality;
use App\Models\Sequence;

use App\Resources\Entry\EntryBySequenceResource;
use App\Resources\Entry\EntrySummaryResource;

class EntryRepository {

  public function getAll(array $values) {
    // Search Parameters
    $query = $values['query'] ?? '';

    // Ordering Parameters
    $column = $values['column'] ?? 'id_quality';
    $order = $values['order'] ?? 'asc';

    // Pagination Parameters
    $limit = isset($values['limit']) ? intval($values['limit']) : 30;
    $page = isset($values['page']) ? intval($values['page']) : 1;
    $skip = ($page > 1) ? ($page * $limit - $limit) : 0;

    $data = Entry::select()->with('rating');
    $fuzzy_ids = [];

    if (!empty($query)) {
      $names = Entry::select('uuid', 'title')->get()->toArray();

      $fuse = new Fuse($names, ['keys' => ['title']]);
      $fuzzy_names = $fuse->search($query, ['limit' => 10]);

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
          $data = $data->orderBy($column, $order);
        }

        $data = $data->orderByRaw($case_string)
          ->orderBy('id');
      }
    }

    $total = $data->count();
    $total_pages = ceil($total / $limit);
    $has_next = $page < $total_pages;

    $data = $data->skip($skip)->paginate($limit);

    if (!empty($needle) && !count($fuzzy_ids)) {
      $data = [];
    }

    $return_value = [
      'data' => EntrySummaryResource::collection($data),
    ];

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

  public function search(array $values) {
    // Search Parameters
    $search_quality = self::search_parse_quality($values['quality']);
    $search_title = $values['title'] ?? null;

    $search_date = self::search_parse_date($values['date']);
    $search_filesize = self::search_parse_filesize($values['filesize']);

    $search_episodes = self::search_parse_count($values['episodes'], 'episodes');
    $search_ovas = self::search_parse_count($values['ovas'], 'ovas');
    $search_specials = self::search_parse_count($values['specials'], 'specials');

    $search_encoder = $values['encoder'] ?? null;
    $search_encoder_video = $values['encoder_video'] ?? null;
    $search_encoder_audio = $values['encoder_audio'] ?? null;
    $search_encoder_subs = $values['encoder_subs'] ?? null;

    $search_release = self::search_parse_release($values['release']);
    $search_remarks = $values['remarks'] ?? null;

    $search_has_remarks = self::search_parse_has_value($values['has_remarks']);
    $search_has_image = self::search_parse_has_value($values['has_image']);

    // Ordering Parameters
    $column = $values['column'] ?? 'id_quality';
    $order = $values['order'] ?? 'asc';

    $data = [];

    return $data;
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
      'data' => EntrySummaryResource::collection($data),
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
    } else if ($letter[0] === "0") {
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
        $season = $entry->release_season ? strtolower($entry->release_season) : "uncategorized";

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

    foreach ($data as $index => $value) {
      if (!$value['seasons']) continue;

      $data[$index]['seasons'] = [
        'winter' => $value['seasons']['winter'] ?? 0,
        'spring' => $value['seasons']['spring'] ?? 0,
        'summer' => $value['seasons']['summer'] ?? 0,
        'fall' => $value['seasons']['fall'] ?? 0,
        'uncategorized' => $value['seasons']['uncategorized'] ?? 0,
      ];
    }

    return $data;
  }

  public function getBySeason($year) {
    $year = intval($year, 10);

    if ($year < 1970 || $year > 2999) {
      $year = null;
    }

    $entries_by_year = Entry::select('uuid', 'title', 'id_quality', 'release_season')
      ->where('release_year', '=', $year)
      ->get();

    $entries_winter = $entries_by_year->filter(function ($value) {
      return ($value['release_season'] === 'Winter');
    });

    $entries_spring = $entries_by_year->filter(function ($value) {
      return ($value['release_season'] === 'Spring');
    });

    $entries_summer = $entries_by_year->filter(function ($value) {
      return ($value['release_season'] === 'Summer');
    });

    $entries_fall = $entries_by_year->filter(function ($value) {
      return ($value['release_season'] === 'Fall');
    });

    $entries_uncategorized = $entries_by_year->filter(function ($value) {
      return ($value['release_season'] === null);
    });

    $data = [
      'winter' => EntrySummaryResource::collection($entries_winter),
      'spring' => EntrySummaryResource::collection($entries_spring),
      'summer' => EntrySummaryResource::collection($entries_summer),
      'fall' => EntrySummaryResource::collection($entries_fall),
      'uncategorized' => EntrySummaryResource::collection($entries_uncategorized),
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

    foreach ($buckets as $index => $bucket) {
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
        'id' => $bucket->id ?? $index + 1,
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
      'data' => EntrySummaryResource::collection($data),
      'stats' => $bucket,
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
      'data' => EntryBySequenceResource::collection($data),
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
      fn($key) => in_array($key, $entryInsertColumns),
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
      fn($key) => in_array($key, $entryUpdateColumns),
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
      (new UploadApi())->destroy('entries/' . $image_id['filename']);
    }

    $imageSettings = [
      'quality' => '90',
      'folder' => 'entries',
    ];

    $imageUpload = (new UploadApi())->upload($image, $imageSettings);
    $imageUrl = $imageUpload['secure_url'];

    $entry->image = $imageUrl;
    $entry->save();
  }

  public function deleteImage($uuid) {
    $entry = Entry::where('uuid', $uuid)->firstOrFail();

    if (!empty($entry->image)) {
      $image_id = pathinfo($entry->image);
      (new UploadApi())->destroy('entries/' . $image_id['filename']);

      $entry->image = null;
      $entry->save();
    }
  }

  public function ratings(array $values, $uuid) {
    $entry = Entry::where('uuid', $uuid)->firstOrFail();

    $entry->rating()->updateOrCreate(['id_entries' => $entry->id], $values);
  }

  public function rewatchAdd(array $values, $uuid) {
    $entry = Entry::where('uuid', $uuid)->firstOrFail();

    EntryRewatch::insert([
      'uuid' => Str::uuid()->toString(),
      'id_entries' => $entry->id,
      'date_rewatched' => $values['date_rewatched'],
    ]);
  }

  public function rewatchDelete($uuid) {
    EntryRewatch::where('uuid', $uuid)
      ->firstOrFail()
      ->delete();
  }

  public function getTitles($id, ?string $needle) {
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

    // total_days is inclusive of the whole $end_date therefore + 1
    $total_days = $start_date->diffInDays($end_date) + 1;

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

  // Search Functions
  public static function search_parse_quality($value) {
    if (!$value) return null;

    // Note:
    // 'p' suffix should not matter in parsing vertical pixels,
    // 2160p has 2 common terms: UHD and 4K

    // Multiple :: {value}, {value}, {value}
    if (str_contains($value, ',')) {
      $qualities = [];
      $values = explode(',', $value);

      foreach ($values as $item) {
        $raw_filter = trim($item);
        $quality = QualityRepository::parseQuality($raw_filter);

        if ($quality) {
          array_push($qualities, $quality);
        }
      }

      if (!count($qualities)) {
        throw new SearchFilterParsingException('quality', 'Error in parsing string');
      }

      return $qualities;
    }

    // Comparators :: {comparator} {value}
    $comparator = get_comparator($value);

    if ($comparator) {
      $value = strtolower($value);
      $quality = trim(str_replace($comparator, '', $value));

      try {
        $comparator = parse_comparator($comparator);
      } catch (Error) {
        throw new SearchFilterParsingException('quality', 'Error in parsing comparator');
      }

      $quality = QualityRepository::parseQuality($quality);

      if (!$quality) {
        throw new SearchFilterParsingException('quality', 'Error in parsing string');
      }

      $qualities = [];

      $quality_list = Quality::select('quality')
        ->orderBy('id', 'asc')
        ->pluck('quality')
        ->toArray();

      if ($comparator === '>') {
        $id = array_search($quality, $quality_list);

        foreach ($quality_list as $key => $item) {
          if ($id > $key) {
            array_push($qualities, $item);
          }
        }
      } else if ($comparator === '>=') {
        $id = array_search($quality, $quality_list);

        foreach ($quality_list as $key => $item) {
          if ($id >= $key) {
            array_push($qualities, $item);
          }
        }
      } else if ($comparator === '<=') {
        $id = array_search($quality, $quality_list);

        foreach ($quality_list as $key => $item) {
          if ($id <= $key) {
            array_push($qualities, $item);
          }
        }
      } else if ($comparator === '<') {
        $id = array_search($quality, $quality_list);

        foreach ($quality_list as $key => $item) {
          if ($id < $key) {
            array_push($qualities, $item);
          }
        }
      }

      if (!count($qualities)) {
        throw new SearchFilterParsingException('quality', 'Error in parsing string');
      }

      return $qualities;
    }

    // Absolute Value :: {value}
    $quality = QualityRepository::parseQuality($value);

    if ($quality) {
      return [$quality];
    }

    // Invalid
    throw new SearchFilterParsingException('quality', 'Error in parsing string');
  }

  public static function search_parse_date($value) {
    if (!$value) return null;

    // Range ::
    // - {date} to {date}
    // - from {date} to {date}
    if (str_contains($value, ' to ')) {
      $value = str_ireplace('from', '', $value);
      $value = trim($value);

      $parts = explode(' to ', $value);
      $date_from = trim($parts[0]);
      $date_to = trim(end($parts));

      try {
        $date_from = Carbon::parse($date_from);
      } catch (InvalidFormatException) {
        throw new SearchFilterParsingException('date', 'Error in parsing from date');
      }

      try {
        $date_to = Carbon::parse($date_to);
      } catch (InvalidFormatException) {
        throw new SearchFilterParsingException('date', 'Error in parsing to date');
      }

      if ($date_from->gte($date_to)) {
        throw new SearchFilterParsingException('date', 'Date to should be later than date from');
      }

      return [
        'date_from' => $date_from->format('Y-m-d'),
        'date_to' => $date_to->format('Y-m-d'),
        'comparator' => null,
      ];
    }

    // Comparators :: {comparator} {date}
    $comparator = get_comparator($value);

    if ($comparator) {
      $value = strtolower($value);
      $date = trim(str_replace($comparator, '', $value));

      try {
        $comparator = parse_comparator($comparator);
      } catch (Error) {
        throw new SearchFilterParsingException('date', 'Error in parsing comparator');
      }

      try {
        $date = Carbon::parse($date);
      } catch (InvalidFormatException) {
        throw new SearchFilterParsingException('date', 'Error in parsing date');
      }

      return [
        'date_from' => $date->format('Y-m-d'),
        'date_to' => null,
        'comparator' => $comparator ?? null,
      ];
    }

    // Absolute Value :: {date}
    try {
      $date = Carbon::parse($value);

      return [
        'date_from' => $date->format('Y-m-d'),
        'date_to' => null,
        'comparator' => null,
      ];
    } catch (InvalidFormatException) {
      throw new SearchFilterParsingException('date', 'Error in parsing date');
    }

    // Invalid
    throw new SearchFilterParsingException('date', 'Error in parsing string');
  }

  public static function search_parse_filesize($value) {
    if (!$value) return null;

    // Range
    // - {value} {unit?} to {value} {unit?}
    // - from {value} {unit?} to {value} {unit?}
    if (str_contains($value, ' to ')) {
      $value = str_ireplace('from', '', $value);
      $value = trim($value);

      $parts = explode(' to ', $value);
      $filesize_from = strtolower(trim($parts[0]));
      $filesize_to = strtolower(trim(end($parts)));

      $valid_units = ['tb', 'gb', 'mb', 'kb'];

      if (!is_numeric($filesize_from)) {
        $flag_from = true;

        foreach ($valid_units as $unit) {
          if (strpos($filesize_from, $unit) !== false) {
            $raw_filesize = preg_match('![0-9]+!', $filesize_from, $matches);
            $raw_filesize = implode('', $matches);

            if ($unit === 'kb') $filesize_from = $raw_filesize * 1024;
            if ($unit === 'mb') $filesize_from = $raw_filesize * 1024 * 1024;
            if ($unit === 'gb') $filesize_from = $raw_filesize * 1024 * 1024 * 1024;
            if ($unit === 'tb') $filesize_from = $raw_filesize * 1024 * 1024 * 1024 * 1024;

            $flag_from = false;
            break;
          }
        }

        if ($flag_from) {
          throw new SearchFilterParsingException('filesize', 'Error in parsing from filesize');
        }
      }

      if (!is_numeric($filesize_to)) {
        $flag_to = true;

        foreach ($valid_units as $unit) {
          if (strpos($filesize_to, $unit) !== false) {
            $raw_filesize = preg_match('![0-9]+!', $filesize_to, $matches);
            $raw_filesize = implode('', $matches);

            if ($unit === 'kb') $filesize_to = $raw_filesize * 1024;
            if ($unit === 'mb') $filesize_to = $raw_filesize * 1024 * 1024;
            if ($unit === 'gb') $filesize_to = $raw_filesize * 1024 * 1024 * 1024;
            if ($unit === 'tb') $filesize_to = $raw_filesize * 1024 * 1024 * 1024 * 1024;

            $flag_to = false;
            break;
          }
        }

        if ($flag_to) {
          throw new SearchFilterParsingException('filesize', 'Error in parsing to filesize');
        }
      }

      if ($filesize_from >= $filesize_to) {
        throw new SearchFilterParsingException('filesize', 'Filesize to to should be smaller than filesize from');
      }

      return [
        'filesize_from' => $filesize_from,
        'filesize_to' => $filesize_to,
        'comparator' => null,
      ];
    }

    // Comparators :: {comparator} {value} {unit?}
    $comparator = get_comparator($value);

    if ($comparator) {
      $value = strtolower($value);
      $filesize = trim(str_replace($comparator, '', $value));

      try {
        $comparator = parse_comparator($comparator);
      } catch (Error) {
        throw new SearchFilterParsingException('filesize', 'Error in parsing comparator');
      }

      if (!is_numeric($filesize)) {
        $valid_units = ['tb', 'gb', 'mb', 'kb'];
        $flag_invalid = true;

        foreach ($valid_units as $unit) {
          if (strpos($filesize, $unit) !== false) {
            $raw_filesize = preg_match('![0-9]+!', $filesize, $matches);
            $raw_filesize = implode('', $matches);

            if ($unit === 'kb') $filesize = $raw_filesize * 1024;
            if ($unit === 'mb') $filesize = $raw_filesize * 1024 * 1024;
            if ($unit === 'gb') $filesize = $raw_filesize * 1024 * 1024 * 1024;
            if ($unit === 'tb') $filesize = $raw_filesize * 1024 * 1024 * 1024 * 1024;

            $flag_invalid = false;

            break;
          }
        }

        if ($flag_invalid) {
          throw new SearchFilterParsingException('filesize', 'Error in parsing filesize');
        }
      }

      return [
        'filesize_from' => $filesize,
        'filesize_to' => null,
        'comparator' => $comparator ?? null,
      ];
    }

    // Invalid
    throw new SearchFilterParsingException('filesize', 'Error in parsing string');
  }

  public static function search_parse_count($value, string $field) {
    if (!$value) return null;

    // Range ::
    // - {value} to {value}
    // - from {value} to {value}
    if (str_contains($value, ' to ')) {
      $value = str_ireplace('from', '', $value);
      $value = trim($value);

      $parts = explode(' to ', $value);
      $count_from = trim($parts[0]);
      $count_to = trim(end($parts));

      if (!is_numeric($count_from)) {
        throw new SearchFilterParsingException($field, 'Error in parsing from ' . $field);
      }

      if (!is_numeric($count_to)) {
        throw new SearchFilterParsingException($field, 'Error in parsing to ' . $field);
      }

      if ($count_from >= $count_to) {
        throw new SearchFilterParsingException($field, $field . ' to should be smaller than ' . $field . ' from');
      }

      return [
        'count_from' => $count_from,
        'count_to' => $count_to,
        'comparator' => null,
      ];
    }

    // Comparators :: {comparator} {value}
    $comparator = get_comparator($value);

    if ($comparator) {
      $count = trim(str_replace($comparator, '', $value));

      try {
        $comparator = parse_comparator($comparator);
      } catch (Error) {
        throw new SearchFilterParsingException($field, 'Error in parsing comparator');
      }

      if (!is_numeric($count)) {
        throw new SearchFilterParsingException($field, 'Error in parsing to ' . $field);
      }

      return [
        'count_from' => $count,
        'count_to' => null,
        'comparator' => $comparator ?? null,
      ];
    }

    // Absolute Value :: {value}
    if (!is_numeric($value)) {
      throw new SearchFilterParsingException($field, 'Error in parsing to ' . $field);
    }

    return [
      'count_from' => $value,
      'count_to' => null,
      'comparator' => $comparator ?? null,
    ];

    // Invalid
    throw new SearchFilterParsingException($field, 'Error in parsing string');
  }

  public static function search_parse_release($value) {
    if (!$value) return null;

    // Range ::
    // - {season?} {year} to {season?} {year}
    // - {year} {season?} to {year} {season?}
    // - from {season?} {year} to {season?} {year}
    // - from {year} {season?} to {year} {season?}
    // - from {year} to {year}
    if (str_contains($value, ' to ')) {
      $value = str_ireplace('from', '', $value);
      $value = trim($value);

      $parts = explode(' to ', $value);
      $release_from = strtolower(trim($parts[0]));
      $release_to = strtolower(trim(end($parts)));

      $formatted_release_from = null;
      $formatted_release_to = null;

      try {
        $formatted_release_from = parse_season($release_from);
        $formatted_release_to = parse_season($release_to);
      } catch (Error) {
        throw new SearchFilterParsingException('release', 'Error in parsing string');
      }

      $date_from = Carbon::parse($formatted_release_from[0] . '-01-01');
      $date_to = Carbon::parse($formatted_release_to[0] . '-01-01');

      if ($formatted_release_from[1] ?? null) {
        if ($formatted_release_from[1] === 'winter') {
          $date_from = Carbon::parse($formatted_release_from[0] . '-02-01');
        } else if ($formatted_release_from[1] === 'spring') {
          $date_from = Carbon::parse($formatted_release_from[0] . '-03-01');
        } else if ($formatted_release_from[1] === 'summer') {
          $date_from = Carbon::parse($formatted_release_from[0] . '-04-01');
        } else if ($formatted_release_from[1] === 'fall') {
          $date_from = Carbon::parse($formatted_release_from[0] . '-05-01');
        }
      }

      if ($formatted_release_to[1] ?? null) {
        if ($formatted_release_to[1] === 'winter') {
          $date_to = Carbon::parse($formatted_release_to[0] . '-02-01');
        } else if ($formatted_release_to[1] === 'spring') {
          $date_to = Carbon::parse($formatted_release_to[0] . '-03-01');
        } else if ($formatted_release_to[1] === 'summer') {
          $date_to = Carbon::parse($formatted_release_to[0] . '-04-01');
        } else if ($formatted_release_to[1] === 'fall') {
          $date_to = Carbon::parse($formatted_release_to[0] . '-05-01');
        }
      }

      if ($date_from->gte($date_to)) {
        throw new SearchFilterParsingException('release', 'Release to should be earlier than release from');
      }

      return [
        'release_from_year' => $formatted_release_from[0],
        'release_from_season' => $formatted_release_from[1] ?? 'winter',
        'release_to_year' => $formatted_release_to[0],
        'release_to_season' => $formatted_release_to[1] ?? 'fall',
        'comparator' => null,
      ];
    }

    // Comparators ::
    // - {comparator} {season?} {year}
    // - {comparator} {year} {season?}
    $comparator = get_comparator($value);

    if ($comparator) {
      $value = strtolower($value);
      $release = trim(str_replace($comparator, '', $value));

      try {
        $comparator = parse_comparator($comparator);
      } catch (Error) {
        throw new SearchFilterParsingException('release', 'Error in parsing comparator');
      }

      try {
        $release = parse_season($release);
      } catch (Error) {
        throw new SearchFilterParsingException('release', 'Error in parsing string');
      }

      return [
        'release_from_year' => $release[0],
        'release_from_season' => $release[1] ?? 'winter',
        'release_to_year' => null,
        'release_to_season' => null,
        'comparator' => $comparator,
      ];
    }

    // Absolute Value ::
    // - {season?} {year}
    // - {year} {season?}
    try {
      $release = parse_season($value);

      return [
        'release_from_year' => $release[0],
        'release_from_season' => $release[1] ?? null,
        'release_to_year' => null,
        'release_to_season' => null,
        'comparator' => null,
      ];
    } catch (Error) {
      throw new SearchFilterParsingException('release', 'Error in parsing string');
    }

    // Invalid
    throw new SearchFilterParsingException('release', 'Error in parsing string');
  }

  public static function search_parse_has_value($value) {
    if (!is_bool($value)) {
      $value = strtolower($value);
    }

    if ($value === 'yes' || $value === 'true' || $value === true) {
      return EntrySearchHasEnum::YES;
    }

    if ($value === 'no' || $value === 'false' || $value === false) {
      return EntrySearchHasEnum::NO;
    }

    return EntrySearchHasEnum::ANY;
  }
}
