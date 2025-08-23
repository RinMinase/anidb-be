<?php

namespace App\Repositories;

use App\Enums\EntryOrderColumnsEnum;
use Error;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Support\Facades\DB;

use App\Enums\EntrySearchHasEnum;
use App\Exceptions\Entry\SearchFilterParsingException;

use App\Models\CodecAudio;
use App\Models\CodecVideo;
use App\Models\Entry;
use App\Models\EntryWatcher;
use App\Models\Genre;
use App\Models\Quality;

use App\Resources\Entry\EntrySummaryResource;

class EntrySearchRepository {

  public function search(array $values) {
    // Search Parameters
    $search_quality = self::search_parse_quality($values['quality'] ?? null);
    $search_title = $values['title'] ?? null;

    $search_date = self::search_parse_date($values['date'] ?? null);
    $search_filesize = self::search_parse_filesize($values['filesize'] ?? null);

    $search_episodes = self::search_parse_count($values['episodes'] ?? null, 'episodes');
    $search_ovas = self::search_parse_count($values['ovas'] ?? null, 'ovas');
    $search_specials = self::search_parse_count($values['specials'] ?? null, 'specials');

    $search_encoder = $values['encoder'] ?? null;
    $search_encoder_video = $values['encoder_video'] ?? null;
    $search_encoder_audio = $values['encoder_audio'] ?? null;
    $search_encoder_subs = $values['encoder_subs'] ?? null;

    $search_release = self::search_parse_release($values['release'] ?? null);
    $search_remarks = $values['remarks'] ?? null;
    $search_rating = self::search_parse_rating($values['rating'] ?? null);

    $search_has_remarks = self::search_parse_has_value($values['has_remarks'] ?? null);
    $search_has_image = self::search_parse_has_value($values['has_image'] ?? null);

    $search_is_hdr = self::search_parse_has_value($values['is_hdr'] ?? null);
    $search_codec_video = self::search_parse_codec($values['codec_video'] ?? null, 'video');
    $search_codec_audio = self::search_parse_codec($values['codec_audio'] ?? null, 'audio');
    $search_genres = self::search_parse_genres($values['genres'] ?? null);
    $search_rewatches = self::search_parse_count($values['rewatches'] ?? null, 'rewatches');
    $search_watcher = $values['watcher'] ?? null;

    // Ordering Parameters
    $column = $values['column'] ?? 'id_quality';
    $order = $values['order'] ?? 'asc';

    $data = Entry::select('entries.*')->with('rating')->with('genres');

    if (!empty($search_title)) {
      $data = $data->where('title', 'ilike', '%' . $search_title . '%')
        ->orWhere('variants', 'ilike', '%' . $search_title . '%');
    }

    if (!empty($search_encoder)) {
      $data = $data->where(function ($query) use ($search_encoder) {
        $query->where('encoder_video', 'ilike', '%' . $search_encoder . '%')
          ->orWhere('encoder_audio', 'ilike', '%' . $search_encoder . '%')
          ->orWhere('encoder_subs', 'ilike', '%' . $search_encoder . '%');
      });
    }

    if (!empty($search_encoder_video)) {
      $data = $data->where('encoder_video', 'ilike', '%' . $search_encoder_video . '%');
    }

    if (!empty($search_encoder_audio)) {
      $data = $data->where('encoder_audio', 'ilike', '%' . $search_encoder_audio . '%');
    }

    if (!empty($search_encoder_subs)) {
      $data = $data->where('encoder_subs', 'ilike', '%' . $search_encoder_subs . '%');
    }

    if (!empty($search_remarks)) {
      $data = $data->where('remarks', 'ilike', '%' . $search_remarks . '%');
    }

    if (isset($search_quality)) {
      $data = $data->where(function ($query) use ($search_quality) {
        $quality_list = Quality::all();
        $total_values = 0;

        foreach ($search_quality as $quality_value) {
          $id_quality = $quality_list
            ->filter(fn($item) => $item->quality === $quality_value)
            ->first()
            ->id;

          if ($total_values === 0) {
            $query->where('id_quality', $id_quality);
          } else {
            $query->orWhere('id_quality', $id_quality);
          }

          $total_values++;
        }
      });
    }

    if (isset($search_date)) {
      $from = $search_date['date_from'];
      $to = $search_date['date_to'];
      $comparator = $search_date['comparator'];

      $data = $data->distinct()->leftJoin('entries_rewatch', 'entries.id', '=', 'entries_rewatch.id_entries');

      if ($comparator) {
        $data = $data->where(function ($query) use ($comparator, $from) {
          $query->where('date_finished', $comparator, $from)
            ->orWhere('entries_rewatch.date_rewatched', $comparator, $from);
        });
      } else {
        $data = $data->where(function ($query) use ($from, $to) {
          $query->whereBetween('date_finished', [$from, $to])
            ->orWhereBetween('entries_rewatch.date_rewatched', [$from, $to]);
        });
      }
    }

    if (!empty($search_filesize)) {
      $from = $search_filesize['filesize_from'];
      $to = $search_filesize['filesize_to'];
      $comparator = $search_filesize['comparator'];

      if ($comparator) {
        $data = $data->where('filesize', $comparator, $from);
      } else {
        $data = $data->whereBetween('filesize', [$from, $to]);
      }
    }

    if (!empty($search_episodes)) {
      $from = $search_episodes['count_from'];
      $to = $search_episodes['count_to'];
      $comparator = $search_episodes['comparator'];

      if ($comparator) {
        $data = $data->where('episodes', $comparator, $from);
      } else if (!$to) {
        $data = $data->where('episodes', $from);
      } else {
        $data = $data->whereBetween('episodes', [$from, $to]);
      }
    }

    if (!empty($search_ovas)) {
      $from = $search_ovas['count_from'];
      $to = $search_ovas['count_to'];
      $comparator = $search_ovas['comparator'];

      if ($comparator) {
        $data = $data->where('ovas', $comparator, $from);
      } else if (!$to) {
        $data = $data->where('ovas', $from);
      } else {
        $data = $data->whereBetween('ovas', [$from, $to]);
      }
    }

    if (!empty($search_specials)) {
      $from = $search_specials['count_from'];
      $to = $search_specials['count_to'];
      $comparator = $search_specials['comparator'];

      if ($comparator) {
        $data = $data->where('specials', $comparator, $from);
      } else if (!$to) {
        $data = $data->where('specials', $from);
      } else {
        $data = $data->whereBetween('specials', [$from, $to]);
      }
    }

    if (!empty($search_release)) {
      $release_from_year = $search_release['release_from_year'];
      $release_from_season = $search_release['release_from_season'];
      $release_to_year = $search_release['release_to_year'];
      $release_to_season = $search_release['release_to_season'];
      $comparator = $search_release['comparator'];
      $releases = $search_release['releases'];

      if ($comparator) {
        if (!$release_from_season) {
          if ($comparator === '>' || $comparator === '>=') {
            $release_from_season = 'winter';
          } else {
            $release_from_season = 'fall';
          }
        }

        $data = $data->where(function ($query) use ($comparator, $release_from_year) {
          $query->where('release_year', $comparator, $release_from_year)
            ->orWhere('release_year', $release_from_year);
        });

        $seasons = ['Winter', 'Spring', 'Summer', 'Fall'];
        $excluded_seasons = [];

        $capitalized_season_from = ucfirst($release_from_season);
        $season_index = array_search($capitalized_season_from, $seasons);

        foreach ($seasons as $key => $value) {
          if ($comparator === '>' && $key <= $season_index) {
            array_push($excluded_seasons, $value);
          } else if ($comparator === '>=' && $key < $season_index) {
            array_push($excluded_seasons, $value);
          } else if ($comparator === '<=' && $key > $season_index) {
            array_push($excluded_seasons, $value);
          } else if ($comparator === '<' && $key >= $season_index) {
            array_push($excluded_seasons, $value);
          }
        }

        if (count($excluded_seasons)) {
          $data = $data->whereNotIn('uuid', function ($query) use ($release_from_year, $excluded_seasons) {
            $query->select('uuid')
              ->where('release_year', $release_from_year)
              ->whereIn('release_season', $excluded_seasons);
          });
        }
      } else if ($release_to_year) {
        $data = $data->whereBetween('release_year', [$release_from_year, $release_to_year]);

        $seasons = ['Winter', 'Spring', 'Summer', 'Fall'];

        // Handle From
        $season_index_from = array_search(ucfirst($release_from_season), $seasons);
        $excluded_seasons_from = array_slice($seasons, 0, $season_index_from);

        if (count($excluded_seasons_from)) {
          $data = $data->whereNotIn('uuid', function ($query) use ($release_from_year, $excluded_seasons_from) {
            $query->select('uuid')
              ->where('release_year', $release_from_year)
              ->whereIn('release_season', $excluded_seasons_from);
          });
        }

        // Handle To
        $season_index_to = array_search(ucfirst($release_to_season), $seasons);
        $excluded_seasons_to = array_slice($seasons, $season_index_to + 1);

        if (count($excluded_seasons_to)) {
          $data = $data->whereNotIn('uuid', function ($query) use ($release_to_year, $excluded_seasons_to) {
            $query->select('uuid')
              ->where('release_year', $release_to_year)
              ->whereIn('release_season', $excluded_seasons_to);
          });
        }
      } else if (!$release_from_year && $release_from_season && !$release_to_season) {
        $data = $data->where('release_season', ucfirst($release_from_season));
      } else if (
        !$release_from_year
        && !$release_to_year
        && $release_from_season
        && $release_to_season
      ) {
        $seasons = ['Winter', 'Spring', 'Summer', 'Fall'];

        $capitalized_season_from = ucfirst($release_from_season);
        $capitalized_season_to = ucfirst($release_to_season);

        $season_index_from = array_search($capitalized_season_from, $seasons);
        $season_index_to = array_search($capitalized_season_to, $seasons);

        $actual_seasons = array_slice($seasons, $season_index_from, $season_index_to - $season_index_from + 1);

        $data = $data->whereIn('release_season', $actual_seasons);
      } else if ($releases && count($releases)) {
        foreach ($releases as $value) {
          $release_year = $value['year'] ?? null;
          $release_season = $value['season'] ? ucfirst($value['season']) : null;

          $data = $data->orWhere(function ($query) use ($release_year, $release_season) {
            if ($release_season && $release_year) {
              $query->where('release_season', $release_season)->where('release_year', $release_year);
            } else if (!$release_season && $release_year) {
              $query->where('release_year', $release_year);
            } else if ($release_season && !$release_year) {
              $query->where('release_season', $release_season);
            }
          });
        }
      } else {
        $data = $data->where('release_year', $release_from_year);

        if ($release_from_season) {
          $data = $data->where('release_season', ucfirst($release_from_season));
        }
      }
    }

    if ($search_has_remarks !== EntrySearchHasEnum::ANY) {
      if ($search_has_remarks === EntrySearchHasEnum::YES) {
        $data = $data->whereNotNull('remarks');
      } else {
        $data = $data->whereNull('remarks');
      }
    }

    if ($search_has_image !== EntrySearchHasEnum::ANY) {
      if ($search_has_image === EntrySearchHasEnum::YES) {
        $data = $data->whereNotNull('image');
      } else {
        $data = $data->whereNull('image');
      }
    }

    if ($search_is_hdr !== EntrySearchHasEnum::ANY) {
      if ($search_is_hdr === EntrySearchHasEnum::YES) {
        $data = $data->where('codec_hdr', true);
      } else {
        $data = $data->where('codec_hdr', false);
      }
    }

    if ($search_codec_audio && count($search_codec_audio)) {
      $data = $data->whereIn('id_codec_audio', $search_codec_audio);
    }

    if ($search_codec_video && count($search_codec_video)) {
      $data = $data->whereIn('id_codec_video', $search_codec_video);
    }

    if ($search_genres && count($search_genres)) {
      $data = $data->leftJoin('entries_genre', 'entries_genre.id_entries', '=', 'entries.id')
        ->whereIn('entries_genre.id_genres', $search_genres)
        ->groupBy('entries.id');
    }

    if ($search_watcher !== null) {
      // null value means any watcher
      $search_watcher = intval($search_watcher);

      if ($search_watcher === 0) {
        // 0 value means watcher column is null
        $data = $data->where('id_watcher', null);
      } else {
        // other values means watcher column is not null
        $id_watcher = EntryWatcher::where('id', $search_watcher)->first();

        if ($id_watcher) {
          $id_watcher = $id_watcher->id;
          $data = $data->where('id_watcher', $id_watcher);
        }
      }
    }

    // Adding Rewatch Count to column
    $subquery = Entry::select(DB::raw('entries.id'))
      ->addSelect(DB::raw('coalesce(count(entries_rewatch.id), 0) as total_rewatch_count'))
      ->leftJoin('entries_rewatch', 'entries.id', '=', 'entries_rewatch.id_entries')
      ->groupBy('entries.id');

    $data = $data->addSelect('derived_table_rewatches.total_rewatch_count')
      ->leftJoinSub($subquery, 'derived_table_rewatches', function ($query) {
        $query->on('derived_table_rewatches.id', '=', 'entries.id');
      })
      ->groupByRaw('entries.id, derived_table_rewatches.total_rewatch_count');

    if (!empty($search_rewatches)) {
      $from = $search_rewatches['count_from'];
      $to = $search_rewatches['count_to'];
      $comparator = $search_rewatches['comparator'];

      if ($comparator) {
        $data = $data->where('total_rewatch_count', $comparator, $from);
      } else if (!$to) {
        $data = $data->where('total_rewatch_count', $from);
      } else {
        $data = $data->whereBetween('total_rewatch_count', [$from, $to]);
      }
    }

    // Handling column and order
    if ($column === 'total_rewatch_count') {
      $column = 'derived_table_rewatches.total_rewatch_count';
    }

    $nulls = $order === 'asc' ? 'first' : 'last';
    $data = $data->orderByRaw($column . ' ' . $order . ' NULLS ' . $nulls)
      ->orderBy('title', 'asc');

    // ratings come last due to subquery + derived table
    if (!empty($search_rating)) {
      $from = $search_rating['rating_from'];
      $to = $search_rating['rating_to'];
      $comparator = $search_rating['comparator'];

      $rating_subquery = DB::table('entries as entry_sub')->select('entry_sub.id')
        ->addSelect(DB::raw('(
          select round(avg(x), 2)
          from unnest(array[
            entries_rating.audio,
            entries_rating.enjoyment,
            entries_rating.graphics,
            entries_rating.plot
          ]) as x
        ) as avg_rating'))
        ->leftJoin('entries_rating', 'entry_sub.id', '=', 'entries_rating.id_entries');

      $data = $data->leftJoinSub($rating_subquery, 'derived_table_rating', function ($query) {
        $query->on('derived_table_rating.id', '=', 'entries.id');
      });

      if ($comparator) {
        $data = $data->where('avg_rating', $comparator, $from);
      } else if (!$to) {
        $data = $data->where('avg_rating', $from);
      } else {
        $data = $data->whereBetween('avg_rating', [$from, $to]);
      }
    }

    $total_filtered = $data->count();
    $data = $data->get();

    $total_entries = Entry::count();

    $stats = [
      'totalFiltered' => $total_filtered,
      'totalEntries' => $total_entries,
    ];

    if (isset($search_date)) {
      $from = $search_date['date_from'];
      $to = $search_date['date_to'];
      $comparator = $search_date['comparator'];

      return [
        'data' => EntrySummaryResource::collectionWithDate($data, $from, $to, $comparator),
        'stats' => $stats,
      ];
    }


    return [
      'data' => EntrySummaryResource::collection($data),
      'stats' => $stats,
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

        if ($quality && !in_array($quality, $qualities)) {
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

  public static function search_parse_date_value(
    string $date_value,
    bool $end = null,
    string $on_error_message = null
  ) {

    $date = null;

    if (is_numeric($date_value)) { // yyyy format
      $date = intval($date_value);

      if ($date < 1900 && $date > 2999) {
        throw new SearchFilterParsingException('date', 'Year should start at 1900 and maxes out at 2999');
      }

      $date = Carbon::createFromFormat('Y', $date)->startOfYear();

      if ($end) {
        $date = $date->endOfYear();
      }
    } else {
      try {
        // $date_value = str_ireplace(',', '', $date_value);
        // $date_value = strtolower($date_value);
        $parts = [];

        if (str_contains($date_value, '-')) {
          $parts = explode('-', $date_value, 3);
        } else if (str_contains($date_value, '/')) {
          $parts = explode('/', $date_value, 3);
          // } else if (str_contains($date_value, ' ')) {
          //   $parts = explode(' ', $date_value, 3);
          // } else {
          //   throw new SearchFilterParsingException('date', 'Error in parsing date');
        }

        // if (count($parts) === 3) {

        // }

        if (count($parts) === 2) {
          if (preg_match('/^\d{4}[-\/]\d{1,2}$/', $date_value)) {
            // yyyy-mm or yyyy/mm format
            $date = Carbon::createFromDate($parts[0], $parts[1], 1);

            if ($end) $date = $date->endOfMonth();
          } else if (preg_match('/^\d{1,2}[-\/]\d{4}$/', $date_value)) {
            // mm-yyyy or mm/yyyy format
            $date = Carbon::createFromDate($parts[1], $parts[0], 1);

            if ($end) $date = $date->endOfMonth();
          } else {
            $date = Carbon::parse($date_value);
          }
        } else {
          $date = Carbon::parse($date_value);

          if ($end) $date = $date->lastOfMonth();
        }
      } catch (InvalidFormatException) {
        throw new SearchFilterParsingException('date', $on_error_message ?? 'Error in parsing date');
      }
    }

    return $date;
  }

  public static function search_parse_date($value) {
    if (!$value) return null;

    // Range ::
    // - {date} to {date}
    // - from {date} to {date}
    // Valid date formats: yyyy-mm-dd, yyyy-mm, yyyy or valid date formats
    if (str_contains($value, ' to ')) {
      $date_range = str_ireplace('from', '', $value);
      $date_range = str_replace(',', '', $date_range);
      $date_range = strtolower(trim($date_range));

      $parts = explode(' to ', $date_range);
      $date_from = trim($parts[0]);
      $date_to = trim(end($parts));

      // Process from
      $date_from = self::search_parse_date_value($date_from, false, 'Error in parsing from date');

      // Process to
      $date_to_parts = preg_split('/(-|\/|\ )/', $date_to, 3);

      if (count($date_to_parts) === 3) {
        $date_to = self::search_parse_date_value($date_to, false, 'Error in parsing to date');
      } else if (count($date_to_parts) === 2) {
        if (preg_match('/^(\d{4}[-\/]\d{1,2})$|^(\d{1,2}[-\/]\d{4})$/', $date_to)) {
          // yyyy-mm or mm-yyyy or yyyy/mm or mm/yyyy format
          $date_to = self::search_parse_date_value($date_to, true, 'Error in parsing from date');
        } else {
          // yyyy mmm or mmm yyyy format
          $m_y_regex = '((jan|january|feb|february|mar|march|apr|april|may|jun|june|jul|july|aug|august|sep|sept|september|oct|october|nov|november|dec|december) \d{4})';
          $y_m_regex = '(\d{4} (jan|january|feb|february|mar|march|apr|april|may|jun|june|jul|july|aug|august|sep|sept|september|oct|october|nov|november|dec|december))';
          $regex = '/^' . $m_y_regex . '$|^' . $y_m_regex . '$/';

          if (preg_match($regex, $date_to)) {
            $date_to = self::search_parse_date_value($date_to, true, 'Error in parsing to date');
          } else {
            throw new SearchFilterParsingException('date', 'Error in parsing date');
          }
        }
      } else if (count($date_to_parts) === 1) {
        // yyyy format
        if (is_numeric($date_to)) {
          $date_to = self::search_parse_date_value($date_to, true, 'Error in parsing to date');
        } else {
          throw new SearchFilterParsingException('date', 'Error in parsing date');
        }
      } else {
        throw new SearchFilterParsingException('date', 'Error in parsing date');
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

      $date = self::search_parse_date_value($date);

      return [
        'date_from' => $date->format('Y-m-d'),
        'date_to' => null,
        'comparator' => $comparator ?? null,
      ];
    }

    // Absolute Value (or semi-range) :: {date}
    $date_from = null;
    $date_to = null;

    $date = strtolower($value);
    $date = str_replace(',', '', $date);
    $date_parts = preg_split('/(-|\/|\ )/', $date, 3);

    if (count($date_parts) === 3) {
      $date_from = self::search_parse_date_value($value);
    } else if (count($date_parts) === 2) {
      // yyyy-mm or mm-yyyy or yyyy/mm or mm/yyyy format -> treat as range for the whole month
      if (preg_match('/^(\d{4}[-\/]\d{1,2})$|^(\d{1,2}[-\/]\d{4})$/', $date)) {
        $date_from = self::search_parse_date_value($date, false, 'Error in parsing from date');
        $date_to = self::search_parse_date_value($date, true, 'Error in parsing to date');
      } else {
        // yyyy mmm or mmm yyyy format
        $m_y_regex = '((jan|january|feb|february|mar|march|apr|april|may|jun|june|jul|july|aug|august|sep|sept|september|oct|october|nov|november|dec|december) \d{4})';
        $y_m_regex = '(\d{4} (jan|january|feb|february|mar|march|apr|april|may|jun|june|jul|july|aug|august|sep|sept|september|oct|october|nov|november|dec|december))';
        $regex = '/^' . $m_y_regex . '$|^' . $y_m_regex . '$/';

        if (preg_match($regex, $date)) {
          $date_from = self::search_parse_date_value($date, false, 'Error in parsing from date');
          $date_to = self::search_parse_date_value($date, true, 'Error in parsing to date');
        } else {
          throw new SearchFilterParsingException('date', 'Error in parsing date');
        }
      }
    } else if (count($date_parts) === 1) {
      // yyyy format -> treat as range for the whole year
      if (is_numeric($date)) {
        $date_from = self::search_parse_date_value($date, false, 'Error in parsing from date');
        $date_to = self::search_parse_date_value($date, true, 'Error in parsing to date');
      } else {
        throw new SearchFilterParsingException('date', 'Error in parsing date');
      }
    } else {
      throw new SearchFilterParsingException('date', 'Error in parsing date');
    }

    return [
      'date_from' => $date_from->format('Y-m-d'),
      'date_to' => $date_to ? $date_to->format('Y-m-d') : null,
      'comparator' => null,
    ];

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
        $is_valid_regex = '/^[\d]+(\.\d+)?\ ?(tb|gb|mb|kb)$/i';
        $is_valid = preg_match($is_valid_regex, $filesize);

        if (!$is_valid) {
          throw new SearchFilterParsingException('filesize', 'Error in parsing string');
        }

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
    if (!$value && $value !== '0') return null;

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

      $count_from = intval($count_from);
      $count_to = intval($count_to);

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
        throw new SearchFilterParsingException($field, 'Error in parsing string');
      }

      $count = intval($count);

      if ($comparator === '<' && !$count) {
        throw new SearchFilterParsingException($field, 'Number should be positive');
      }

      return [
        'count_from' => $count,
        'count_to' => null,
        'comparator' => $comparator ?? null,
      ];
    }

    // Absolute Value :: {value}
    if (!is_numeric($value)) {
      throw new SearchFilterParsingException($field, 'Error in parsing string');
    }

    $count = intval($value);

    return [
      'count_from' => $count,
      'count_to' => null,
      'comparator' => null,
    ];

    // Invalid
    throw new SearchFilterParsingException($field, 'Error in parsing string');
  }

  public static function search_parse_rating($value) {
    if (!$value) return null;

    // Range ::
    // - {value} to {value}
    // - from {value} to {value}
    if (str_contains($value, ' to ')) {
      $value = str_ireplace('from', '', $value);
      $value = trim($value);

      $parts = explode(' to ', $value);
      $rating_from = trim($parts[0]);
      $rating_to = trim(end($parts));

      if (!is_numeric($rating_from)) {
        throw new SearchFilterParsingException('rating', 'Rating from should be numeric and maxes out to 5');
      }

      if (!is_numeric($rating_to)) {
        throw new SearchFilterParsingException('rating', 'Rating to should be numeric and maxes out to 5');
      }

      $rating_from = floatval($rating_from);
      $rating_to = floatval($rating_to);

      if ($rating_from >= $rating_to) {
        throw new SearchFilterParsingException('rating', 'Rating to should be smaller than rating from');
      }

      if ($rating_from > 5 || $rating_to > 5) {
        throw new SearchFilterParsingException('rating', 'Max value of rating is 5');
      }

      if ($rating_from < 0 || $rating_to < 0) {
        throw new SearchFilterParsingException('rating', 'Min value of rating is 0');
      }

      if ($rating_from === $rating_to) {
        throw new SearchFilterParsingException('rating', 'Rating or from should not be the same');
      }

      return [
        'rating_from' => $rating_from,
        'rating_to' => $rating_to,
        'comparator' => null,
      ];
    }

    // Comparators :: {comparator} {value}
    $comparator = get_comparator($value);

    if ($comparator) {
      $rating = trim(str_replace($comparator, '', $value));

      try {
        $comparator = parse_comparator($comparator);
      } catch (Error) {
        throw new SearchFilterParsingException('rating', 'Error in parsing comparator');
      }

      if (!is_numeric($rating)) {
        throw new SearchFilterParsingException('rating', 'Error in parsing string');
      }

      $rating = floatval($rating);

      if ($rating >= 5 && $comparator === '>') {
        throw new SearchFilterParsingException('rating', 'Error in parsing string');
      }

      if ($rating > 5) {
        throw new SearchFilterParsingException('rating', 'Max value of rating is 5');
      }

      if ($rating < 0) {
        throw new SearchFilterParsingException('rating', 'Min value of rating is 0');
      }

      return [
        'rating_from' => $rating,
        'rating_to' => null,
        'comparator' => $comparator ?? null,
      ];
    }

    // Absolute Value :: {value}
    if (!is_numeric($value)) {
      throw new SearchFilterParsingException('rating', 'Error in parsing string');
    }

    $rating = floatval($value);

    if ($rating > 5) {
      throw new SearchFilterParsingException('rating', 'Max value of rating is 5');
    }

    if ($rating < 0) {
      throw new SearchFilterParsingException('rating', 'Min value of rating is 0');
    }

    return [
      'rating_from' => $rating,
      'rating_to' => null,
      'comparator' => null,
    ];

    // Invalid
    throw new SearchFilterParsingException($field, 'Error in parsing string');
  }

  public static function search_parse_release($value) {
    if (!$value) return null;

    // Range ::
    // - {season?} {year} to {season?} {year}
    // - {year} {season?} to {year} {season?}
    // - {season} to {season}
    // - from {season?} {year} to {season?} {year}
    // - from {year} {season?} to {year} {season?}
    // - from {year} to {year}
    // - from {season} to {season}
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

      // If {from?} {season} to {season}
      if ($formatted_release_from[0] === null && $formatted_release_to[0] === null) {
        $season_from = $formatted_release_from[1];
        $season_to = $formatted_release_to[1];

        $seasons = ['winter', 'spring', 'summer', 'fall'];
        $season_from_index = array_search($season_from, $seasons);
        $season_to_index = array_search($season_to, $seasons);

        if ($season_from_index >= $season_to_index) {
          throw new SearchFilterParsingException('release', 'Release season to should be earlier than release season from');
        }

        return [
          'release_from_year' => null,
          'release_from_season' => $formatted_release_from[1],
          'release_to_year' => null,
          'release_to_season' => $formatted_release_to[1],
          'comparator' => null,
          'releases' => [],
        ];
      }

      $date_from = Carbon::parse($formatted_release_from[0] . '-01-01');
      $date_to = Carbon::parse($formatted_release_to[0] . '-01-01');

      if (isset($formatted_release_from[1])) {
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

      if (isset($formatted_release_to[1])) {
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
        'releases' => [],
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

      if (!$release[0]) {
        throw new SearchFilterParsingException('release', 'Error in parsing string');
      }

      return [
        'release_from_year' => $release[0],
        'release_from_season' => $release[1] ?? null,
        'release_to_year' => null,
        'release_to_season' => null,
        'comparator' => $comparator,
        'releases' => [],
      ];
    }

    // Commma-separated Values ::
    // - {season?} {year?} {season?}, {season?} {year?} {season?}
    try {
      if (str_contains($value, ',')) {
        $parts = explode(',', $value);

        $releases = [];
        foreach ($parts as $value) {
          $release_value = trim($value);
          $release_value = parse_season($release_value);

          // has NO season and has NO year
          if (!$release_value[0] && !$release_value[1]) {
            throw new SearchFilterParsingException('release', 'Error in parsing string');
          }

          array_push($releases, [
            'year' => $release_value[0],
            'season' => $release_value[1],
          ]);
        }

        return [
          'release_from_year' => null,
          'release_from_season' => null,
          'release_to_year' => null,
          'release_to_season' => null,
          'comparator' => null,
          'releases' => $releases,
        ];
      }
    } catch (Error) {
      throw new SearchFilterParsingException('release', 'Error in parsing string');
    }


    // Absolute Value ::
    // - {season} {year}
    // - {year} {season}
    // - {year}
    // - {season}
    try {
      $release = parse_season($value);

      return [
        'release_from_year' => $release[0] ?? null,
        'release_from_season' => $release[1] ?? null,
        'release_to_year' => null,
        'release_to_season' => null,
        'comparator' => null,
        'releases' => [],
      ];
    } catch (Error) {
      throw new SearchFilterParsingException('release', 'Error in parsing string');
    }

    // Invalid
    throw new SearchFilterParsingException('release', 'Error in parsing string');
  }

  public static function search_parse_has_value($value) {
    if (empty($value) && $value !== false) return EntrySearchHasEnum::ANY;

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

  public static function search_parse_codec($value, $type) {
    if (!$value) return null;
    if ($type !== 'video' && $type !== 'audio') return null;

    $value = str_replace(' ', '', $value);
    $parts = explode(',', $value);

    foreach ($parts as $part) {
      if (!is_numeric($part)) {
        throw new SearchFilterParsingException('codec_' . $type, 'Error in parsing string');
      }
    }

    $valid_ids = [];

    if ($type === 'video') {
      $valid_ids = CodecVideo::select('id')->pluck('id')->toArray();
    } else if ($type === 'audio') {
      $valid_ids = CodecAudio::select('id')->pluck('id')->toArray();
    }

    $codec_ids = [];

    foreach ($parts as $part) {
      $part = intval($part);

      if (in_array($part, $valid_ids)) {
        array_push($codec_ids, $part);
      }
    }

    return $codec_ids;
  }

  public static function search_parse_genres($value) {
    if (!$value) return null;

    $value = str_replace(' ', '', $value);
    $parts = explode(',', $value);

    foreach ($parts as $part) {
      if (!is_numeric($part)) {
        throw new SearchFilterParsingException('genres', 'Error in parsing string');
      }
    }

    $valid_ids = Genre::select('id')->pluck('id')->toArray();

    $genre_ids = [];
    foreach ($parts as $part) {
      $part = intval($part);

      if (in_array($part, $valid_ids)) {
        array_push($genre_ids, $part);
      }
    }

    return $genre_ids;
  }
}
