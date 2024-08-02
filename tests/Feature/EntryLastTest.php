<?php

namespace Tests\Feature;

use Tests\BaseTestCase;
use Illuminate\Support\Str;
use Carbon\Carbon;

use App\Models\Entry;
use App\Models\EntryOffquel;
use App\Models\EntryRating;
use App\Models\EntryRewatch;
use App\Models\Quality;
use Exception;

class EntryLastTest extends BaseTestCase {

  private $rewatch_backup = null;
  private $rating_backup = null;
  private $offquel_backup = null;
  private $entry_backup = null;

  private $entry_ids = [];

  // Place this outside the try-catch block
  private function setup_backup() {
    // Save current entries and relations
    $this->rewatch_backup = EntryRewatch::all()
      ->makeVisible(['id', 'id_entries'])
      ->toArray();

    $this->rating_backup = EntryRating::all()
      ->makeVisible(['id', 'id_entries', 'created_at', 'updated_at', 'deleted_at'])
      ->toArray();

    $this->offquel_backup = EntryOffquel::all()
      ->makeVisible(['id_entries', 'created_at', 'updated_at', 'deleted_at'])
      ->toArray();

    $this->entry_backup = Entry::all()
      ->makeVisible(['id', 'id_quality', 'updated_at', 'deleted_at'])
      ->toArray();
  }

  // Place this in a try block
  private function setup_config() {
    EntryRewatch::truncate();
    EntryRating::truncate();
    EntryOffquel::truncate();
    Entry::truncate();

    $id_quality = Quality::where('quality', 'FHD 1080p')->first()->id;
    $timestamp = Carbon::now();
    $test_entries = [];

    for ($i = 0; $i < 30; $i++) {
      $id = Str::uuid()->toString();

      $values = [
        'uuid' => $id,
        'id_quality' => $id_quality,
        'date_finished' => Carbon::parse('2001-01-01')->addDays($i)->format('Y-m-d'),
        'title' => 'title ' . $i,
        'created_at' => $timestamp,
        'updated_at' => $timestamp,
      ];

      array_push($this->entry_ids, $id);
      array_push($test_entries, $values);
    }

    Entry::insert($test_entries);
  }

  // Place this in a finally block
  private function setup_restore() {
    // Remove test data
    EntryRewatch::truncate();
    EntryRating::truncate();
    EntryOffquel::truncate();
    Entry::truncate();

    // Restore saved entries and relations
    Entry::insert($this->entry_backup);
    EntryOffquel::insert($this->offquel_backup);
    EntryRating::insert($this->rating_backup);
    EntryRewatch::insert($this->rewatch_backup);
  }

  public function test_should_get_all_latest_entries() {
    $this->setup_backup();

    try {
      $this->setup_config();

      $response = $this->withoutMiddleware()->get('/api/entries/last');

      $expected_count = 20;
      $response->assertStatus(200)
        ->assertJsonCount($expected_count, 'data')
        ->assertJsonStructure([
          'data' => [[
            'id',
            'quality',
            'title',
            'dateFinished',
            'rewatched',
            'filesize',
            'episodes',
            'ovas',
            'specials',
            'encoder',
            'release',
            'remarks',
            'rating',
          ]],
          'stats' => [
            'dateLastEntry',
            'daysLastEntry',
            'dateOldestEntry',
            'daysOldestEntry',
            'totalEps',
            'totalTitles',
            'totalCours',
            'titlesPerWeek',
            'coursPerWeek',
            'epsPerWeek',
            'epsPerDay',
          ]
        ]);

      $expected_ids = collect($this->entry_ids)
        ->reverse()
        ->values()
        ->take($expected_count)
        ->toArray();

      $actual_ids = collect($response['data'])
        ->pluck('id')
        ->toArray();

      $this->assertEquals($expected_ids, $actual_ids);
    } catch (Exception $e) {
      throw $e;
    } finally {
      $this->setup_restore();
    }
  }

  public function test_should_not_get_all_latest_entries() {
    $response = $this->get('/api/entries/last');

    $response->assertStatus(401)
      ->assertJson(['message' => 'Unauthorized']);
  }
}
