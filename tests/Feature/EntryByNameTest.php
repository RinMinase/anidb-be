<?php

namespace Tests\Feature;

use Illuminate\Support\Str;
use Tests\BaseTestCase;

use App\Models\Entry;
use App\Models\EntryOffquel;
use App\Models\EntryRating;
use App\Models\EntryRewatch;
use App\Models\Quality;

class EntryByNameTest extends BaseTestCase {

  // Backup related variables
  private $entry_rewatch_backup = null;
  private $entry_rating_backup = null;
  private $entry_offquel_backup = null;
  private $entry_backup = null;

  // Class variables
  private $filesize = 100;

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

    $test_entries = [
      [
        'uuid' => Str::uuid()->toString(),
        'id_quality' => $id_quality,
        'date_finished' => '2001-01-01',
        'title' => '0 test title',
        'filesize' => $this->filesize,
        'created_at' => '2020-01-01 13:00:00',
        'updated_at' => '2020-01-01 13:00:00',
      ],
      [
        'uuid' => Str::uuid()->toString(),
        'id_quality' => $id_quality,
        'date_finished' => '2001-01-01',
        'title' => '1 test title',
        'filesize' => $this->filesize,
        'created_at' => '2020-01-01 13:00:00',
        'updated_at' => '2020-01-01 13:00:00',
      ],
      [
        'uuid' => Str::uuid()->toString(),
        'id_quality' => $id_quality,
        'date_finished' => '2001-01-01',
        'title' => 'a test title 1',
        'filesize' => $this->filesize,
        'created_at' => '2020-01-01 13:00:00',
        'updated_at' => '2020-01-01 13:00:00',
      ],
      [
        'uuid' => Str::uuid()->toString(),
        'id_quality' => $id_quality,
        'date_finished' => '2001-01-01',
        'title' => 'a test title 2',
        'filesize' => $this->filesize,
        'created_at' => '2020-01-01 13:00:00',
        'updated_at' => '2020-01-01 13:00:00',
      ],
      [
        'uuid' => Str::uuid()->toString(),
        'id_quality' => $id_quality,
        'date_finished' => '2001-01-01',
        'title' => 'a test title 3',
        'filesize' => $this->filesize,
        'created_at' => '2020-01-01 13:00:00',
        'updated_at' => '2020-01-01 13:00:00',
      ],
      [
        'uuid' => Str::uuid()->toString(),
        'id_quality' => $id_quality,
        'date_finished' => '2001-01-01',
        'title' => 'd test title',
        'filesize' => $this->filesize,
        'created_at' => '2020-01-01 13:00:00',
        'updated_at' => '2020-01-01 13:00:00',
      ],
      [
        'uuid' => Str::uuid()->toString(),
        'id_quality' => $id_quality,
        'date_finished' => '2001-01-01',
        'title' => 'g test title',
        'filesize' => $this->filesize,
        'created_at' => '2020-01-01 13:00:00',
        'updated_at' => '2020-01-01 13:00:00',
      ],
      [
        'uuid' => Str::uuid()->toString(),
        'id_quality' => $id_quality,
        'date_finished' => '2001-01-01',
        'title' => 'p test title 1',
        'filesize' => $this->filesize,
        'created_at' => '2020-01-01 13:00:00',
        'updated_at' => '2020-01-01 13:00:00',
      ],
      [
        'uuid' => Str::uuid()->toString(),
        'id_quality' => $id_quality,
        'date_finished' => '2001-01-01',
        'title' => 'p test title 2',
        'filesize' => $this->filesize,
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
  public function test_should_get_all_entries_by_letter() {
    $this->setup_config();

    $letter = 'a';
    $response = $this->withoutMiddleware()->get('/api/entries/by-name/' . $letter);

    $response->assertStatus(200)
      ->assertJsonCount(3, 'data')
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
      ]);

    $letter = '0';
    $response = $this->withoutMiddleware()->get('/api/entries/by-name/' . $letter);
    $response->assertStatus(200)->assertJsonCount(2, 'data');

    $letter = 'd';
    $response = $this->withoutMiddleware()->get('/api/entries/by-name/' . $letter);
    $response->assertStatus(200)->assertJsonCount(1, 'data');

    $letter = 'g';
    $response = $this->withoutMiddleware()->get('/api/entries/by-name/' . $letter);
    $response->assertStatus(200)->assertJsonCount(1, 'data');

    $letter = 'p';
    $response = $this->withoutMiddleware()->get('/api/entries/by-name/' . $letter);
    $response->assertStatus(200)->assertJsonCount(2, 'data');
  }

  public function test_should_not_get_all_entries_on_invalid_path_parameters() {
    $param = '1';
    $response = $this->withoutMiddleware()->get('/api/entries/by-name/' . $param);
    $response->assertStatus(404);

    $param = 100;
    $response = $this->withoutMiddleware()->get('/api/entries/by-name/' . $param);
    $response->assertStatus(404);
  }

  public function test_should_get_stats_of_entries_by_name() {
    $this->setup_config();

    $response = $this->withoutMiddleware()
      ->get('/api/entries/by-name/');

    $response->assertStatus(200)
      ->assertJsonCount(27, 'data')
      ->assertJsonStructure([
        'data' => [[
          'letter',
          'titles',
          'filesize',
        ]],
      ]);

    $actual_0 = collect($response['data'])->first(function ($value) {
      return ($value['letter'] === '#');
    });

    $actual_a = collect($response['data'])->first(function ($value) {
      return ($value['letter'] === 'A');
    });

    $actual_d = collect($response['data'])->first(function ($value) {
      return ($value['letter'] === 'D');
    });

    $actual_g = collect($response['data'])->first(function ($value) {
      return ($value['letter'] === 'G');
    });

    $actual_p = collect($response['data'])->first(function ($value) {
      return ($value['letter'] === 'P');
    });

    $actual_z = collect($response['data'])->first(function ($value) {
      return ($value['letter'] === 'Z');
    });

    $expected_0_count = 2;
    $expected_a_count = 3;
    $expected_d_count = 1;
    $expected_g_count = 1;
    $expected_p_count = 2;
    $expected_z_count = 0;

    $this->assertEquals($expected_0_count, $actual_0['titles']);
    $this->assertEquals($expected_a_count, $actual_a['titles']);
    $this->assertEquals($expected_d_count, $actual_d['titles']);
    $this->assertEquals($expected_g_count, $actual_g['titles']);
    $this->assertEquals($expected_p_count, $actual_p['titles']);
    $this->assertEquals($expected_z_count, $actual_z['titles']);

    $this->assertEquals($expected_0_count * $this->filesize || '', $actual_0['filesize']);
    $this->assertEquals($expected_a_count * $this->filesize || '', $actual_a['filesize']);
    $this->assertEquals($expected_d_count * $this->filesize || '', $actual_d['filesize']);
    $this->assertEquals($expected_g_count * $this->filesize || '', $actual_g['filesize']);
    $this->assertEquals($expected_p_count * $this->filesize || '', $actual_p['filesize']);
    $this->assertEquals($expected_z_count * $this->filesize || '', $actual_z['filesize']);
  }
}
