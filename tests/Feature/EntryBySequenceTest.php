<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\BaseTestCase;

use App\Models\Entry;
use App\Models\EntryOffquel;
use App\Models\EntryRating;
use App\Models\EntryRewatch;
use App\Models\Quality;
use App\Models\Sequence;

class EntryBySequenceTest extends BaseTestCase {

  // Backup related variables
  private $sequence_backup = null;
  private $entry_rewatch_backup = null;
  private $entry_rating_backup = null;
  private $entry_offquel_backup = null;
  private $entry_backup = null;

  // Class variables
  private $sequence_id = 99999;

  private $date_from = '1980-01-01';
  private $date_to = '1980-02-01';

  private $entry_id_1 = 99999;
  private $entry_id_2 = 99998;
  private $entry_id_3 = 99997;

  private $entry_uuid_1 = '4385f9d3-3928-452c-917d-13b02fbe5687';
  private $entry_uuid_2 = '251c34f4-e281-4d7e-9ed9-6b7977a665ba';
  private $entry_uuid_3 = '9bea308b-a6a3-4fdd-add8-4c34a1051abc';

  // Backup related tables
  private function setup_backup() {
    $hidden_columns = ['created_at', 'updated_at'];
    $this->sequence_backup = Sequence::all()->makeVisible($hidden_columns)->toArray();

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
    Sequence::truncate();
    Sequence::insert($this->sequence_backup);
    Sequence::refreshAutoIncrements();

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
    Sequence::truncate();
    Entry::truncate();

    Sequence::insert([
      'id' => $this->sequence_id,
      'title' => 'Sample Testing Sequence',
      'date_from' => Carbon::parse($this->date_from)->format('Y-m-d'),
      'date_to' => Carbon::parse($this->date_to)->format('Y-m-d'),
      'created_at' => '2020-01-01 13:00:00',
      'updated_at' => '2020-01-01 13:00:00',
    ]);

    $id_quality = Quality::where('quality', 'FHD 1080p')->first()->id;

    $test_entries = [
      [
        'id' => $this->entry_id_1,
        'uuid' => $this->entry_uuid_1,
        'id_quality' => $id_quality,
        'title' => "a test data title",
        'date_finished' => Carbon::parse($this->date_from)->format('Y-m-d'),
        'created_at' => '2020-01-01 13:00:00',
        'updated_at' => '2020-01-01 13:00:00',
      ], [
        'id' => $this->entry_id_2,
        'uuid' => $this->entry_uuid_2,
        'id_quality' => $id_quality,
        'title' => "x test data title",
        'date_finished' => Carbon::parse($this->date_to)->format('Y-m-d'),
        'created_at' => '2020-01-01 13:00:00',
        'updated_at' => '2020-01-01 13:00:00',
      ], [
        'id' => $this->entry_id_3,
        'uuid' => $this->entry_uuid_3,
        'id_quality' => $id_quality,
        'title' => "z test data title",
        'date_finished' => Carbon::parse($this->date_to)->addDay()->format('Y-m-d'),
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
  public function test_should_get_all_entries_by_sequence_with_stats() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->get('/api/entries/by-sequence/' . $this->sequence_id);

    $response->assertStatus(200)
      ->assertJsonCount(2, 'data')
      ->assertJsonStructure([
        'data',
        'stats' => [
          'titlesPerDay',
          'epsPerDay',
          'quality2160',
          'quality1080',
          'quality720',
          'quality480',
          'quality360',
          'totalTitles',
          'totalEps',
          'totalSize',
          'totalDays',
          'startDate',
          'endDate',
        ],
      ]);

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $expected_ids = [
      $this->entry_uuid_1,
      $this->entry_uuid_2,
    ];

    $this->assertEqualsCanonicalizing($expected_ids, $actual_ids);
  }

  public function test_should_not_get_all_entries_with_non_existent_sequence() {
    $invalid_id = -1;

    $response = $this->withoutMiddleware()->get('/api/entries/by-sequence/' . $invalid_id);

    $response->assertStatus(404);
  }
}
