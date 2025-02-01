<?php

namespace App\Repositories;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\DB;

use App\Models\Bucket;
use App\Models\Entry;
use App\Models\Partial;

class ManagementRepository {

  public function index() {
    $rating_subquery = DB::table('entries as entry_sub')->select('entry_sub.id')
      ->addSelect(DB::raw('(
          select round(avg(x))
          from unnest(array[
            entries_rating.audio,
            entries_rating.enjoyment,
            entries_rating.graphics,
            entries_rating.plot
          ]) as x
        ) as avg_rating'))
      ->leftJoin('entries_rating', 'entry_sub.id', '=', 'entries_rating.id_entries');

    $entries = Entry::select('entries.id', 'id_quality')
      ->addSelect('duration', 'filesize', 'season_number', 'date_finished')
      ->addSelect(DB::raw('(episodes + ovas + specials) as episodes'))
      ->addSelect(DB::raw('count(entries_rewatch) + 1 as total_watches'))
      ->addSelect('avg_rating')
      ->leftJoin('entries_rewatch', 'entries.id', '=', 'entries_rewatch.id_entries')
      ->groupBy('entries.id', 'avg_rating')
      ->orderBy('entries.id')
      ->leftJoinSub($rating_subquery, 'derived_table', function ($query) {
        $query->on('derived_table.id', '=', 'entries.id');
      })
      ->get();

    $buckets = Bucket::select('size')->get();
    $partials = Partial::count();

    $episodes = 0;
    $seasons = 0;
    $entry_size = 0;
    $bucket_size = 0;
    $watch_time = 0;
    $rewatch_time = 0;

    $quality_2160 = 0;
    $quality_1080 = 0;
    $quality_720 = 0;
    $quality_480 = 0;
    $quality_360 = 0;

    $ratings = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

    foreach ($entries as $entry) {
      $episodes += $entry->episodes;
      $entry_size += $entry->filesize;
      $watch_time += $entry->duration;
      $rewatch_time += $entry->duration * $entry->total_watches;

      if ($entry->season_number === null || $entry->season_number === 1) {
        $seasons++;
      }

      if ($entry->id_quality === 1) $quality_2160++;
      if ($entry->id_quality === 2) $quality_1080++;
      if ($entry->id_quality === 3) $quality_720++;
      if ($entry->id_quality === 4) $quality_480++;
      if ($entry->id_quality === 5) $quality_360++;

      if ($entry->avg_rating) {
        if ($entry->avg_rating <= 10 && $entry->avg_rating > 0) {
          $ratings[$entry->avg_rating]++;
        }
      } else {
        $ratings[0]++;
      }
    }

    foreach ($buckets as $bucket) {
      $bucket_size += $bucket->size;
    }

    $seconds_in_days = 86_400;
    $watch_days = floor($watch_time / $seconds_in_days);
    $watch_remainder = $watch_time % $seconds_in_days;

    $watch_text = $watch_days . ' days';
    $watch_subtext = CarbonInterval::seconds($watch_remainder)->cascade()->forHumans();

    $rewatch_days = floor($rewatch_time / $seconds_in_days);
    $rewatch_remainder = $rewatch_time % $seconds_in_days;
    $rewatch_text = $rewatch_days . ' days';
    $rewatch_subtext = CarbonInterval::seconds($rewatch_remainder)->cascade()->forHumans();

    $watched_by_month = $this->calculate_watched_by_month();
    $watched_by_year = $this->calculate_watched_by_year();
    $watched_by_season = $this->calculate_watched_by_season();

    return [
      'count' => [
        'entries' => count($entries),
        'buckets' => count($buckets),
        'partials' => $partials,
      ],
      'stats' => [
        'watchSeconds' => $watch_time,
        'watch' => $watch_text,
        'watchSubtext' => $watch_subtext,
        'rewatchSeconds' => $rewatch_time,
        'rewatch' => $rewatch_text,
        'rewatchSubtext' => $rewatch_subtext,
        'bucketSize' => parse_filesize($bucket_size ?? '0 TB'),
        'entrySize' => parse_filesize($entry_size ?? '0 TB'),
        'episodes' => $episodes,
        'titles' => count($entries),
        'seasons' => $seasons,
      ],
      'graph' => [
        'quality' => [
          'quality_2160' => $quality_2160,
          'quality_1080' => $quality_1080,
          'quality_720' => $quality_720,
          'quality_480' => $quality_480,
          'quality_360' => $quality_360,
        ],
        'ratings' => $ratings,
        'months' => [
          'jan' => $watched_by_month[0],
          'feb' => $watched_by_month[1],
          'mar' => $watched_by_month[2],
          'apr' => $watched_by_month[3],
          'may' => $watched_by_month[4],
          'jun' => $watched_by_month[5],
          'jul' => $watched_by_month[6],
          'aug' => $watched_by_month[7],
          'sep' => $watched_by_month[8],
          'oct' => $watched_by_month[9],
          'nov' => $watched_by_month[10],
          'dec' => $watched_by_month[11],
        ],
        'years' => $watched_by_year,
        'seasons' => $watched_by_season,
      ],
    ];
  }

  public function get_by_year(array $values) {
    $year = $values['year'] ?? null;
    $end = Carbon::createFromDate($year + 1, 1, 1)->startOfDay();
    $start = Carbon::createFromDate($year, 1, 1)->startOfDay();

    $data = DB::table('entries')
      ->select('entries.title', 'entries.date_finished', 'entries_rewatch.date_rewatched')
      ->leftJoin('entries_rewatch', 'entries.id', '=', 'entries_rewatch.id_entries')
      ->where(function ($query) use ($start, $end) {
        $query->whereBetween('entries.date_finished', [$start, $end])
          ->orWhereBetween('entries_rewatch.date_rewatched', [$start, $end]);
      })
      ->get()
      ->toArray();

    $months = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
    foreach ($data as $value) {
      $date_finished = Carbon::parse($value->date_finished)->startOfDay();

      if ($date_finished->between($start, $end)) {
        $months[$date_finished->month - 1]++;
      }

      if ($value->date_rewatched) {
        $date_rewatched = Carbon::parse($value->date_rewatched)->startOfDay();

        if ($date_rewatched->between($start, $end)) {
          $months[$date_rewatched->month - 1]++;
        }
      }
    }

    return $months;
  }

  /**
   * Calculation Functions
   */
  private function calculate_watched_by_month() {
    $data = DB::table('entries')
      ->select(DB::raw('DATE_PART(\'month\', entries.date_finished) AS date_finished'))
      ->addSelect(DB::raw('DATE_PART(\'month\', entries_rewatch.date_rewatched) AS date_rewatched'))
      ->leftJoin('entries_rewatch', 'entries.id', '=', 'entries_rewatch.id_entries')
      ->orderBy('entries.id', 'asc')
      ->get()
      ->toArray();

    $watched_by_month = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
    foreach ($data as $value) {
      if ($value->date_finished) {
        $watched_by_month[$value->date_finished - 1]++;
      }

      if ($value->date_rewatched) {
        $watched_by_month[$value->date_rewatched - 1]++;
      }
    }

    return $watched_by_month;
  }

  private function calculate_watched_by_year() {
    $data = DB::table('entries')
      ->select(DB::raw('DATE_PART(\'year\', entries.date_finished) AS date_finished'))
      ->addSelect(DB::raw('DATE_PART(\'year\', entries_rewatch.date_rewatched) AS date_rewatched'))
      ->leftJoin('entries_rewatch', 'entries.id', '=', 'entries_rewatch.id_entries')
      ->orderBy('entries.id', 'asc')
      ->get()
      ->toArray();

    $watched_by_year_pre = [];
    foreach ($data as $value) {
      if ($value->date_finished) {
        if (!isset($watched_by_year_pre[$value->date_finished])) {
          $watched_by_year_pre[$value->date_finished] = 0;
        }

        $watched_by_year_pre[$value->date_finished]++;
      }

      if ($value->date_rewatched) {
        if (!isset($watched_by_year_pre[$value->date_rewatched])) {
          $watched_by_year_pre[$value->date_rewatched] = 0;
        }

        $watched_by_year_pre[$value->date_rewatched]++;
      }
    }

    $watched_by_year = [];
    foreach ($watched_by_year_pre as $key => $value) {
      array_push($watched_by_year, [
        'year' => $key,
        'value' => $value,
      ]);
    }

    usort($watched_by_year, function ($item1, $item2) {
      return $item1['year'] <=> $item2['year'];
    });

    return $watched_by_year;
  }

  private function calculate_watched_by_season() {
    $data = DB::table('entries')
      ->select('release_season')
      ->addSelect(DB::raw('COUNT(*) AS count'))
      ->groupBy('release_season')
      ->orderByRaw('CASE
        WHEN release_season=\'Winter\' THEN 1
        WHEN release_season=\'Spring\' THEN 2
        WHEN release_season=\'Summer\' THEN 3
        WHEN release_season=\'Fall\' THEN 4
        ELSE 0 END
      ')
      ->get()
      ->toArray();

    $watched_by_season = [];
    foreach ($data as $value) {
      array_push($watched_by_season, [
        'season' => $value->release_season ?? "None",
        'value' => $value->count,
      ]);
    }

    return $watched_by_season;
  }
}
