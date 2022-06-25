<?php

namespace App\Controllers;

use Exception;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

use App\Models\Entry;
use App\Models\EntryRating;
use App\Models\EntryRewatch;

class ImportController extends Controller {

  public function index(Request $request) {
    try {
      $this->initial_import($request);

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
      foreach ($request->all() as $item) {
        $title_id = null;

        /**
         * If it contains ratings
         */
        if (
          $item['rating']['audio']
          || $item['rating']['enjoyment']
          || $item['rating']['graphics']
          || $item['rating']['plot']
        ) {
          $title_id = $this->search_title_id($id_entries, $item['title']);

          array_push($import_ratings, [
            'id_entries' => $title_id,
            'audio' => $item['rating']['audio'],
            'enjoyment' => $item['rating']['enjoyment'],
            'graphics' => $item['rating']['graphics'],
            'plot' => $item['rating']['plot'],
          ]);
        }

        /**
         * Checking for local relationship keys
         */
        if (
          $item['firstSeasonTitle']
          || $item['prequel']
          || $item['sequel']
        ) {
          if (!$title_id) {
            $title_id = $this->search_title_id($id_entries, $item['title']);
          }

          $first_title_id = null;
          $prequel_id = null;
          $sequel_id = null;

          foreach ($id_entries as $entry) {
            if (
              $item['firstSeasonTitle']
              && $entry->title === $item['firstSeasonTitle']
            ) {
              $first_title_id = $entry->id;
            }

            if ($item['prequel'] && $entry->title === $item['prequel']) {
              $prequel_id = $entry->id;
            }

            if ($item['sequel'] && $entry->title === $item['sequel']) {
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
        if (isset($item['rewatch']) && $item['rewatch']) {
          if (!$title_id) {
            $title_id = $this->search_title_id($id_entries, $item['title']);
          }

          $rewatch_list = explode(',', $item['rewatch']);
          $rewatch_list = array_reverse($rewatch_list);

          foreach ($rewatch_list as $rewatch_item) {
            array_push($import_rewatches, [
              'id_entries' => $title_id,
              'date_rewatched' => Carbon::createFromTimestamp($rewatch_item)
                ->format('Y-m-d'),
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

      return response()->json([
        'status' => 200,
        'message' => 'Success',
      ]);
    } catch (Exception $e) {
      throw $e;
      // return response()->json([
      //   'status' => 401,
      //   'message' => 'Failed to import JSON file',
      // ]);
    }
  }

  private function initial_import($request) {
    $import = [];

    foreach ($request->all() as $item) {
      $data = [
        'uuid' => Str::uuid()->toString(),

        'id_quality' => $this->parse_quality($item['quality']),
        'title' => $item['title'] ?? null,

        'date_finished' => Carbon::createFromTimestamp($item['dateFinished'])
          ->format('Y-m-d'),

        'duration' => $item['duration'] ?? 0,
        'filesize' => $item['filesize'] ?? 0,

        'episodes' => $item['episodes'] ?? 0,
        'ovas' => $item['ovas'] ?? 0,
        'specials' => $item['specials'] ?? 0,

        'release_season' => $this->parse_season($item['releaseSeason']) ?? null,
        'release_year' => $item['releaseYear'] ?? null,

        'remarks' => $item['remarks'] ?? null,
        'variants' => $item['variants'] ?? null,

        'season_number' => $item['seasonNumber'] ?? null,

        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
      ];

      array_push($import, $data);
    }

    Entry::insert($import);
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
    return $haystack[$id]->id;
  }
}
