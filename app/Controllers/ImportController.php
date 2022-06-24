<?php

namespace App\Controllers;

use Exception;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

use App\Models\Entry;
use App\Models\EntryRating;

class ImportController extends Controller {

  public function index(Request $request) {
    try {
      $import = [];

      foreach ($request->all() as $item) {
        $data = [
          'uuid' => Str::uuid()->toString(),

          'id_quality' => $this->parse_quality($item['quality']),
          'title' => $item['title'] ?? null,

          'date_finished' => Carbon::parse('@' . $item['dateFinished'])->format('Y-m-d'),
          'duration' => $item['duration'] ?? 0,
          'filesize' => $item['filesize'] ?? 0,

          'episodes' => $item['episodes'] ?? 0,
          'ovas' => $item['ovas'] ?? 0,
          'specials' => $item['specials'] ?? 0,

          'release_season' => $this->parse_season($item['releaseSeason']) ?? 0,
          'release_year' => $item['releaseYear'],

          'remarks' => $item['remarks'] ?? null,
          'variants' => $item['variants'] ?? null,

          'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
          'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ];

        array_push($import, $data);
      }

      Entry::insert($import);

      $importRatings = [];

      // second run-through for all foreign keys
      foreach ($request->all() as $item) {
        if (
          $item['rating']['audio']
          || $item['rating']['enjoyment']
          || $item['rating']['graphics']
          || $item['rating']['plot']
        ) {
          $id_entry = Entry::where('title', $item['title'])->first()->id;

          array_push($importRatings, [
            'id_entries' => $id_entry,
            'audio' => $item['rating']['audio'],
            'enjoyment' => $item['rating']['enjoyment'],
            'graphics' => $item['rating']['graphics'],
            'plot' => $item['rating']['plot'],
          ]);
        }
      }

      EntryRating::insert($importRatings);

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

  private function parse_season(string $season): string {
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
}
