<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Tests\BaseTestCase;

use App\Models\Entry;
use App\Models\EntryOffquel;
use App\Models\EntryRating;
use App\Models\EntryRewatch;
use App\Models\Quality;

class EntryByYearTest extends BaseTestCase {

  // Backup related variables
  private $entry_rewatch_backup = null;
  private $entry_rating_backup = null;
  private $entry_offquel_backup = null;
  private $entry_backup = null;

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
    $date_finished = Carbon::parse('2001-01-01')->format('Y-m-d');

    $test_entries = [
      [
        'uuid' => Str::uuid()->toString(),
        'id_quality' => $id_quality,
        'date_finished' => $date_finished,
        'title' => 'test title 1',
        'release_year' => 2000,
        'release_season' => 'Winter',
        'created_at' => '2020-01-01 13:00:00',
        'updated_at' => '2020-01-01 13:00:00',
      ],
      [
        'uuid' => Str::uuid()->toString(),
        'id_quality' => $id_quality,
        'date_finished' => $date_finished,
        'title' => 'test title 2',
        'release_year' => 2000,
        'release_season' => 'Winter',
        'created_at' => '2020-01-01 13:00:00',
        'updated_at' => '2020-01-01 13:00:00',
      ],
      [
        'uuid' => Str::uuid()->toString(),
        'id_quality' => $id_quality,
        'date_finished' => $date_finished,
        'title' => 'test title 3',
        'release_year' => 2000,
        'release_season' => 'Summer',
        'created_at' => '2020-01-01 13:00:00',
        'updated_at' => '2020-01-01 13:00:00',
      ],
      [
        'uuid' => Str::uuid()->toString(),
        'id_quality' => $id_quality,
        'date_finished' => $date_finished,
        'title' => 'test title 4',
        'release_year' => 2000,
        'release_season' => 'Spring',
        'created_at' => '2020-01-01 13:00:00',
        'updated_at' => '2020-01-01 13:00:00',
      ],
      [
        'uuid' => Str::uuid()->toString(),
        'id_quality' => $id_quality,
        'date_finished' => $date_finished,
        'title' => 'test title 5',
        'release_year' => 2000,
        'release_season' => 'Fall',
        'created_at' => '2020-01-01 13:00:00',
        'updated_at' => '2020-01-01 13:00:00',
      ],
      [
        'uuid' => Str::uuid()->toString(),
        'id_quality' => $id_quality,
        'date_finished' => $date_finished,
        'title' => 'test title 6',
        'release_year' => 2001,
        'release_season' => 'Winter',
        'created_at' => '2020-01-01 13:00:00',
        'updated_at' => '2020-01-01 13:00:00',
      ],
      [
        'uuid' => Str::uuid()->toString(),
        'id_quality' => $id_quality,
        'date_finished' => $date_finished,
        'title' => 'test title 7',
        'release_year' => 2020,
        'release_season' => null,
        'created_at' => '2020-01-01 13:00:00',
        'updated_at' => '2020-01-01 13:00:00',
      ],
      [
        'uuid' => Str::uuid()->toString(),
        'id_quality' => $id_quality,
        'date_finished' => $date_finished,
        'title' => 'test title 8',
        'release_year' => null,
        'release_season' => null,
        'created_at' => '2020-01-01 13:00:00',
        'updated_at' => '2020-01-01 13:00:00',
      ],
    ];

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
  public function test_should_get_all_entries_by_year() {
    $this->setup_config();

    $year = '2000';
    $response = $this->withoutMiddleware()->get('/api/entries/by-year/' . $year);

    $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
          'winter' => [[
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
          'spring' => [[
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
          'summer' => [[
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
          'fall' => [[
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
          'uncategorized',
        ],
      ])
      ->assertJsonCount(2, 'data.winter')
      ->assertJsonCount(1, 'data.spring')
      ->assertJsonCount(1, 'data.summer')
      ->assertJsonCount(1, 'data.fall')
      ->assertJsonCount(0, 'data.uncategorized');

    $year = '2001';
    $response = $this->withoutMiddleware()->get('/api/entries/by-year/' . $year);

    $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
          'winter' => [[
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
          'spring',
          'summer',
          'fall',
          'uncategorized',
        ],
      ])
      ->assertJsonCount(1, 'data.winter')
      ->assertJsonCount(0, 'data.spring')
      ->assertJsonCount(0, 'data.summer')
      ->assertJsonCount(0, 'data.fall')
      ->assertJsonCount(0, 'data.uncategorized');

    $year = '2020';
    $response = $this->withoutMiddleware()->get('/api/entries/by-year/' . $year);

    $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
          'winter',
          'spring',
          'summer',
          'fall',
          'uncategorized' => [[
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
        ],
      ])
      ->assertJsonCount(0, 'data.winter')
      ->assertJsonCount(0, 'data.spring')
      ->assertJsonCount(0, 'data.summer')
      ->assertJsonCount(0, 'data.fall')
      ->assertJsonCount(1, 'data.uncategorized');
  }

  public function test_should_return_blank_entries_if_year_has_no_entries() {
    $this->setup_config();

    $year = '2050';
    $response = $this->withoutMiddleware()->get('/api/entries/by-year/' . $year);

    $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
          'winter',
          'spring',
          'summer',
          'fall',
          'uncategorized',
        ],
      ])
      ->assertJsonCount(0, 'data.winter')
      ->assertJsonCount(0, 'data.spring')
      ->assertJsonCount(0, 'data.summer')
      ->assertJsonCount(0, 'data.fall')
      ->assertJsonCount(0, 'data.uncategorized');
  }

  public function test_should_get_all_uncategorized_entries_on_blank_or_invalid_year() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->get('/api/entries/by-year/uncategorized');

    $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
          'winter',
          'spring',
          'summer',
          'fall',
          'uncategorized' => [[
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
        ],
      ])
      ->assertJsonCount(0, 'data.winter')
      ->assertJsonCount(0, 'data.spring')
      ->assertJsonCount(0, 'data.summer')
      ->assertJsonCount(0, 'data.fall')
      ->assertJsonCount(1, 'data.uncategorized');
  }

  public function test_should_not_get_all_entries_by_year_on_invalid_year() {
    $this->setup_config();

    $invalid_year = -1;
    $response = $this->withoutMiddleware()->get('/api/entries/by-year/' . $invalid_year);
    $response->assertStatus(404);

    $invalid_year = 0;
    $response = $this->withoutMiddleware()->get('/api/entries/by-year/' . $invalid_year);
    $response->assertStatus(404);

    $invalid_year = 'null';
    $response = $this->withoutMiddleware()->get('/api/entries/by-year/' . $invalid_year);
    $response->assertStatus(404);

    $invalid_year = 'invalid';
    $response = $this->withoutMiddleware()->get('/api/entries/by-year/' . $invalid_year);
    $response->assertStatus(404);
  }

  public function test_should_get_stats_of_entries_by_year() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->get('/api/entries/by-year');

    $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [[
          'year',
          'count',
          'seasons'
        ]],
      ]);

    $expected_2000 = [
      'winter' => 2,
      'spring' => 1,
      'summer' => 1,
      'fall' => 1,
      'uncategorized' => 0,
    ];

    $expected_2020 = [
      'winter' => 0,
      'spring' => 0,
      'summer' => 0,
      'fall' => 0,
      'uncategorized' => 1,
    ];

    $expected_uncategorized = 1;

    $actual_2000 = collect($response['data'])->first(function ($value) {
      return ($value['year'] === 2000);
    });


    $actual_2020 = collect($response['data'])->first(function ($value) {
      return ($value['year'] === 2020);
    });

    $actual_uncategorized = collect($response['data'])->first(function ($value) {
      return ($value['year'] === null);
    });

    $this->assertEquals($expected_2000, $actual_2000['seasons']);
    $this->assertEquals($expected_2020, $actual_2020['seasons']);
    $this->assertEquals($expected_uncategorized, $actual_uncategorized['count']);
  }
}
