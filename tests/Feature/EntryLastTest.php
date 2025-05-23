<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Tests\BaseTestCase;

use App\Enums\IntegerSizesEnum;
use App\Enums\IntegerTypesEnum;

use App\Models\Entry;
use App\Models\EntryOffquel;
use App\Models\EntryRating;
use App\Models\EntryRewatch;
use App\Models\Quality;

class EntryLastTest extends BaseTestCase {

  // Backup related variables
  private $entry_rewatch_backup = null;
  private $entry_rating_backup = null;
  private $entry_offquel_backup = null;
  private $entry_backup = null;

  // Class variables
  private $entry_ids = [];
  private $entry_count = 50;

  // Backup related tables
  private function setup_backup() {
    $hidden_columns = ['id', 'id_entries'];
    $this->entry_rewatch_backup = EntryRewatch::all()->makeVisible($hidden_columns)->toArray();

    $hidden_columns = ['id', 'id_entries', 'created_at', 'updated_at', 'deleted_at'];
    $this->entry_rating_backup = EntryRating::all()->makeVisible($hidden_columns)->toArray();

    $hidden_columns = ['id_entries', 'created_at', 'updated_at', 'deleted_at'];
    $this->entry_offquel_backup = EntryOffquel::all()->makeVisible($hidden_columns)->toArray();

    $hidden_columns = ['id', 'id_quality', 'updated_at', 'deleted_at'];
    $this->entry_backup = Entry::all()->makeVisible($hidden_columns)->toArray();
  }

  // Restore related tables
  private function setup_restore() {
    Entry::truncate(); // cascade deletes

    Entry::insert($this->entry_backup);
    EntryOffquel::insert($this->entry_offquel_backup);
    EntryRating::insert($this->entry_rating_backup);
    EntryRewatch::insert($this->entry_rewatch_backup);

    Entry::refreshAutoIncrements();
    EntryOffquel::refreshAutoIncrements();
    EntryRating::refreshAutoIncrements();
    EntryRewatch::refreshAutoIncrements();
  }

  // Setup data for testing
  private function setup_config() {
    Entry::truncate();

    $id_quality = Quality::where('quality', 'FHD 1080p')->first()->id;
    $test_entries = [];

    for ($i = 0; $i < $this->entry_count; $i++) {
      $id = Str::uuid()->toString();

      $values = [
        'uuid' => $id,
        'id_quality' => $id_quality,
        'date_finished' => Carbon::parse('2001-01-01')->addDays($i)->format('Y-m-d'),
        'title' => 'title ' . $i,
        'created_at' => '2020-01-01 13:00:00',
        'updated_at' => '2020-01-01 13:00:00',
      ];

      array_push($this->entry_ids, $id);
      array_push($test_entries, $values);
    }

    Entry::insert($test_entries);
  }

  // Fixtures
  public function setUp(): void {
    parent::setUp();
    $this->setup_backup();
  }

  public function tearDown(): void {
    $this->setup_restore();
    parent::tearDown();
  }

  // Test Cases
  public function test_should_get_all_latest_entries() {
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
          'filesize',
          'rewatched',
          'rewatchCount',
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
  }

  public function test_should_get_all_latest_20_entries_when_limit_is_lower_than_20() {
    $this->setup_config();

    $expected_count = 20;

    $this->withoutMiddleware()
      ->get('/api/entries/last?' . http_build_query(['items' => 19]))
      ->assertStatus(200)
      ->assertJsonCount($expected_count, 'data');

    $this->withoutMiddleware()
      ->get('/api/entries/last?' . http_build_query(['items' => 10]))
      ->assertStatus(200)
      ->assertJsonCount($expected_count, 'data');

    $this->withoutMiddleware()
      ->get('/api/entries/last?' . http_build_query(['items' => 5]))
      ->assertStatus(200)
      ->assertJsonCount($expected_count, 'data');

    $this->withoutMiddleware()
      ->get('/api/entries/last?' . http_build_query(['items' => 1]))
      ->assertStatus(200)
      ->assertJsonCount($expected_count, 'data');
  }

  public function test_should_get_all_latest_entries_with_limit() {
    $this->setup_config();

    $items = 25;

    $this->withoutMiddleware()
      ->get('/api/entries/last?' . http_build_query(['items' => 25]))
      ->assertStatus(200)
      ->assertJsonCount($items, 'data');

    $items = 30;

    $this->withoutMiddleware()
      ->get('/api/entries/last?' . http_build_query(['items' => 30]))
      ->assertStatus(200)
      ->assertJsonCount($items, 'data');

    $items = 40;

    $this->withoutMiddleware()
      ->get('/api/entries/last?' . http_build_query(['items' => 40]))
      ->assertStatus(200)
      ->assertJsonCount($items, 'data');

    $expected_count = $this->entry_count;

    $this->withoutMiddleware()
      ->get('/api/entries/last?' . http_build_query([
        'items' => max_int(IntegerTypesEnum::SIGNED, IntegerSizesEnum::TINY)
      ]))
      ->assertStatus(200)
      ->assertJsonCount($expected_count, 'data');
  }

  public function test_should_not_get_all_latest_entries_on_form_errors() {
    $this->setup_config();

    $this->withoutMiddleware()
      ->get('/api/entries/last?' . http_build_query([
        'items' => max_int(IntegerTypesEnum::SIGNED, IntegerSizesEnum::TINY) + 1
      ]))
      ->assertStatus(401)
      ->assertJsonStructure(['data' => ['items']]);

    $this->withoutMiddleware()
      ->get('/api/entries/last?' . http_build_query(['items' => -1]))
      ->assertStatus(401)
      ->assertJsonStructure(['data' => ['items']]);

    $this->withoutMiddleware()
      ->get('/api/entries/last?' . http_build_query(['items' => 0]))
      ->assertStatus(401)
      ->assertJsonStructure(['data' => ['items']]);

    $this->withoutMiddleware()
      ->get('/api/entries/last?' . http_build_query(['items' => 'string']))
      ->assertStatus(401)
      ->assertJsonStructure(['data' => ['items']]);
  }
}
