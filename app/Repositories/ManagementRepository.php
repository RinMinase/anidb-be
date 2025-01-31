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

    $titles_month = [];
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

      if ($entry->date_finished) {
        $month_finished = Carbon::parse($entry->date_finished)->month;

        if (!array_key_exists($month_finished, $titles_month)) {
          $titles_month[$month_finished] = 0;
        };

        $titles_month[$month_finished]++;
      }

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
    $watch_subtext = CarbonInterval::seconds($watch_remainder)
      ->cascade()
      ->forHumans();

    $rewatch_days = floor($rewatch_time / $seconds_in_days);
    $rewatch_remainder = $rewatch_time % $seconds_in_days;
    $rewatch_text = $rewatch_days . ' days';
    $rewatch_subtext = CarbonInterval::seconds($rewatch_remainder)
      ->cascade()
      ->forHumans();

    $watched_by_year = $this->calculate_watched_by_year();

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
          'jan' => $titles_month[1] ?? 0,
          'feb' => $titles_month[2] ?? 0,
          'mar' => $titles_month[3] ?? 0,
          'apr' => $titles_month[4] ?? 0,
          'may' => $titles_month[5] ?? 0,
          'jun' => $titles_month[6] ?? 0,
          'jul' => $titles_month[7] ?? 0,
          'aug' => $titles_month[8] ?? 0,
          'sep' => $titles_month[9] ?? 0,
          'oct' => $titles_month[10] ?? 0,
          'nov' => $titles_month[11] ?? 0,
          'dec' => $titles_month[12] ?? 0,
        ],
        'year' => $watched_by_year,
      ],
    ];
  }

  /**
   * Calculation Functions
   */
  private function calculate_watched_by_year() {
    $entries_watched_by_year_raw = DB::table('entries')
      ->select(DB::raw('DATE_PART(\'year\', date_finished) AS year, COUNT(*) AS count'))
      ->groupBy('year')
      ->orderBy('year', 'asc')
      ->get()
      ->toArray();

    $data = [];
    foreach ($entries_watched_by_year_raw as $value) {
      array_push($data, [
        'year' => strval($value->year),
        'value' => $value->count,
      ]);
    }

    return $data;
  }
}
