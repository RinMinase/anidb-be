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

class EntryByNameTest extends BaseTestCase {

  private $rewatch_backup = null;
  private $rating_backup = null;
  private $offquel_backup = null;
  private $entry_backup = null;

  private $filesize = 100;

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
    Entry::truncate();

    $id_quality = Quality::where('quality', 'FHD 1080p')->first()->id;
    $timestamp = Carbon::now();
    $date_finished = Carbon::parse('2001-01-01')->format('Y-m-d');

    $test_entries = [
      [
        'uuid' => Str::uuid()->toString(),
        'id_quality' => $id_quality,
        'date_finished' => $date_finished,
        'title' => '0 test title',
        'filesize' => $this->filesize,
        'created_at' => $timestamp,
        'updated_at' => $timestamp,
      ], [
        'uuid' => Str::uuid()->toString(),
        'id_quality' => $id_quality,
        'date_finished' => $date_finished,
        'title' => '1 test title',
        'filesize' => $this->filesize,
        'created_at' => $timestamp,
        'updated_at' => $timestamp,
      ], [
        'uuid' => Str::uuid()->toString(),
        'id_quality' => $id_quality,
        'date_finished' => $date_finished,
        'title' => 'a test title 1',
        'filesize' => $this->filesize,
        'created_at' => $timestamp,
        'updated_at' => $timestamp,
      ], [
        'uuid' => Str::uuid()->toString(),
        'id_quality' => $id_quality,
        'date_finished' => $date_finished,
        'title' => 'a test title 2',
        'filesize' => $this->filesize,
        'created_at' => $timestamp,
        'updated_at' => $timestamp,
      ], [
        'uuid' => Str::uuid()->toString(),
        'id_quality' => $id_quality,
        'date_finished' => $date_finished,
        'title' => 'a test title 3',
        'filesize' => $this->filesize,
        'created_at' => $timestamp,
        'updated_at' => $timestamp,
      ], [
        'uuid' => Str::uuid()->toString(),
        'id_quality' => $id_quality,
        'date_finished' => $date_finished,
        'title' => 'd test title',
        'filesize' => $this->filesize,
        'created_at' => $timestamp,
        'updated_at' => $timestamp,
      ], [
        'uuid' => Str::uuid()->toString(),
        'id_quality' => $id_quality,
        'date_finished' => $date_finished,
        'title' => 'g test title',
        'filesize' => $this->filesize,
        'created_at' => $timestamp,
        'updated_at' => $timestamp,
      ], [
        'uuid' => Str::uuid()->toString(),
        'id_quality' => $id_quality,
        'date_finished' => $date_finished,
        'title' => 'p test title 1',
        'filesize' => $this->filesize,
        'created_at' => $timestamp,
        'updated_at' => $timestamp,
      ], [
        'uuid' => Str::uuid()->toString(),
        'id_quality' => $id_quality,
        'date_finished' => $date_finished,
        'title' => 'p test title 2',
        'filesize' => $this->filesize,
        'created_at' => $timestamp,
        'updated_at' => $timestamp,
      ],
    ];

    Entry::insert($test_entries);
  }

  // Place this in a finally block
  private function setup_restore() {
    // Remove test data
    Entry::truncate();

    // Restore saved entries and relations
    Entry::insert($this->entry_backup);
    EntryOffquel::insert($this->offquel_backup);
    EntryRating::insert($this->rating_backup);
    EntryRewatch::insert($this->rewatch_backup);

    refresh_db_table_autoincrement((new Entry())->getTable());
    refresh_db_table_autoincrement((new EntryOffquel())->getTable());
    refresh_db_table_autoincrement((new EntryRating())->getTable());
    refresh_db_table_autoincrement((new EntryRewatch())->getTable());
  }

  public function test_should_get_all_entries_by_letter() {
    $this->setup_backup();

    try {
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
    } finally {
      $this->setup_restore();
    }
  }

  public function test_should_not_get_all_entries_by_letter_when_not_authorized() {
    $letter = 'a';
    $response = $this->get('/api/entries/by-name/' . $letter);

    $response->assertStatus(401)
      ->assertJson(['message' => 'Unauthorized']);
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
    $this->setup_backup();

    try {
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
    } finally {
      $this->setup_restore();
    }
  }

  public function test_should_not_get_stats_of_entries_by_name_when_not_authorized() {
    $response = $this->get('/api/entries/by-name/');

    $response->assertStatus(401)
      ->assertJson(['message' => 'Unauthorized']);
  }
}
