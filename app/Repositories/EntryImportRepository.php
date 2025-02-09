<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

use App\Models\Entry;
use App\Models\EntryOffquel;
use App\Models\EntryRating;
use App\Models\EntryRewatch;

class EntryImportRepository {

  public function import(array $values) {
    $total_count = $this->initial_import($values);

    $import_updates = [];
    $import_updates_ids = [];

    $import_ratings = [];
    $import_rewatches = [];
    $import_offquels = [];

    $id_entries = DB::table('entries')
      ->select('id', 'title')
      ->get()
      ->toArray();

    // second run-through for all foreign keys
    foreach ($values as $item) {
      $title_id = null;

      /**
       * If it contains ratings
       */
      $has_ratings = !empty($item->rating->audio)
        || !empty($item->rating->enjoyment)
        || !empty($item->rating->graphics)
        || !empty($item->rating->plot);

      if ($has_ratings) {
        $title_id = $this->search_title_id($id_entries, $item->title);

        $ratings = [
          'audio' => $item->rating->audio ?? null,
          'enjoyment' => $item->rating->enjoyment ?? null,
          'graphics' => $item->rating->graphics ?? null,
          'plot' => $item->rating->plot ?? null,
        ];

        // Remove this block when import data is not from old setup
        foreach ($ratings as $key => $value) {
          $ratings[$key] = translate_rating_10_to_5($value, true);
        }
        // ===========================================================

        array_push($import_ratings, [
          'id_entries' => $title_id,
          ...$ratings,
        ]);
      }

      /**
       * Checking for local relationship keys
       */
      if (
        !empty($item->firstSeasonTitle)
        || !empty($item->prequel)
        || !empty($item->sequel)
      ) {
        if (!$title_id) {
          $title_id = $this->search_title_id($id_entries, $item->title);
        }

        $first_title_id = null;
        $prequel_id = null;
        $sequel_id = null;

        foreach ($id_entries as $entry) {
          if (
            $item->firstSeasonTitle
            && $entry->title === $item->firstSeasonTitle
          ) {
            $first_title_id = $entry->id;
          }

          if ($item->prequel && $entry->title === $item->prequel) {
            $prequel_id = $entry->id;
          }

          if ($item->sequel && $entry->title === $item->sequel) {
            $sequel_id = $entry->id;
          }
        }

        array_push($import_updates_ids, $title_id);
        array_push($import_updates, [
          'season_first_title_id' => $first_title_id,
          'prequel_id' => $prequel_id,
          'sequel_id' => $sequel_id,
        ]);
      }

      /**
       * If it contains rewatches
       */
      if (!empty($item->rewatch)) {
        if (!$title_id) {
          $title_id = $this->search_title_id($id_entries, $item->title);
        }

        $rewatch_list = explode(',', $item->rewatch);
        $rewatch_list = array_reverse($rewatch_list);

        foreach ($rewatch_list as $rewatch_item) {
          array_push($import_rewatches, [
            'uuid' => Str::uuid()->toString(),
            'id_entries' => $title_id,
            'date_rewatched' => Carbon::createFromTimestamp($rewatch_item, '+8:00')
              ->format('Y-m-d'),
          ]);
        }
      }

      /**
       * If it contains offquels
       */
      if (!empty($item->offquel)) {
        if (!$title_id) {
          $title_id = $this->search_title_id($id_entries, $item->title);
        }

        $offquel_list = explode(', ', $item->offquel);

        foreach ($offquel_list as $offquel_item) {
          $id_entries_offquel = $this->search_title_id($id_entries, $offquel_item);

          array_push($import_offquels, [
            'id_entries' => $title_id,                    // parent
            'id_entries_offquel' => $id_entries_offquel,  // child
          ]);
        }
      }
    }

    /**
     * Loops the 'for update' array
     */
    foreach ($import_updates as $key => $import_update) {
      Entry::where('id', $import_updates_ids[$key])
        ->update($import_update);
    }

    /**
     * Insert ratings
     */
    EntryRating::insert($import_ratings);
    EntryRewatch::insert($import_rewatches);
    EntryOffquel::insert($import_offquels);

    return $total_count;
  }

  private function initial_import(array $values) {
    $import = [];

    foreach ($values as $item) {
      // accepts only if it 'does not exist' or has a value of '-1'
      $acceptedPriority = !isset($item->downloadPriority)
        || $item->downloadPriority == -1;

      if (!empty($item) && $acceptedPriority) {
        $date_finished = null;

        if (isset($item->dateFinished) && !empty($item->dateFinished)) {
          $date_finished = Carbon::createFromTimestamp($item->dateFinished, '+8:00')->format('Y-m-d');
        }

        $release_season = null;

        if (isset($item->releaseSeason) && !empty($item->releaseSeason)) {
          $release_season = $this->parse_season($item->releaseSeason);
        }

        $release_year = null;

        if (
          isset($item->releaseSeason)
          && !empty($item->releaseSeason)
          && is_numeric($item->releaseYear)
          && $item->releaseYear >= 1900
          && $item->releaseYear < 3000
        ) {
          $release_year = $item->releaseYear;
        }

        $data = [
          'uuid' => Str::uuid()->toString(),

          'id_quality' => $this->parse_quality($item->quality),
          'title' => $item->title ?? null,

          'date_finished' => $date_finished,

          'duration' => $item->duration ?? 0,
          'filesize' => $item->filesize ?? 0,

          'episodes' => $item->episodes ?? 0,
          'ovas' => $item->ovas ?? 0,
          'specials' => $item->specials ?? 0,

          'release_season' => $release_season,
          'release_year' => $release_year,

          'remarks' => $item->remarks ?? null,
          'variants' => $item->variants ?? null,

          'season_number' => $item->seasonNumber ?? null,
          'encoder_video' => $item->encoder ?? null,

          'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
          'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ];

        array_push($import, $data);
      }
    }

    Entry::insert($import);

    return count($import);
  }

  private function parse_quality(string $quality): int {
    switch ($quality) {
      case '4K 2160p':
        return 1;
      case 'FHD 1080p':
        return 2;
      case 'HD 720p':
        return 3;
      case 'HQ 480p':
        return 4;
      case 'LQ 360p':
        return 5;
      default:
        return null;
    }
  }

  private function parse_season($season) {
    switch ($season) {
      case 'Winter':
      case 'Spring':
      case 'Summer':
      case 'Fall':
        return $season;
      default:
        return null;
    }
  }

  private function search_title_id(array $haystack, string $needle) {
    $id = array_column($haystack, 'title');
    $id = array_search($needle, $id);
    return $haystack[$id] ? $haystack[$id]->id ?? null : null;
  }
}
