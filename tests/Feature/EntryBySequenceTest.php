<?php

namespace Tests\Feature\Entry;

use Carbon\Carbon;
use Tests\BaseTestCase;

use App\Models\Entry;
use App\Models\Quality;
use App\Models\Sequence;

class EntryBySequenceTest extends BaseTestCase {

  private $sequence_id = 99999;

  private $date_from = '1980-01-01';
  private $date_to = '1980-02-01';

  private $entry_id_1 = 99999;
  private $entry_id_2 = 99998;
  private $entry_id_3 = 99997;

  private $entry_uuid_1 = '4385f9d3-3928-452c-917d-13b02fbe5687';
  private $entry_uuid_2 = '251c34f4-e281-4d7e-9ed9-6b7977a665ba';
  private $entry_uuid_3 = '9bea308b-a6a3-4fdd-add8-4c34a1051abc';

  private function setup_config() {
    // Clearing possible duplicate data
    $this->setup_clear();

    Sequence::insert([
      'id' => $this->sequence_id,
      'title' => 'Sample Testing Sequence',
      'date_from' => Carbon::parse($this->date_from)->format('Y-m-d'),
      'date_to' => Carbon::parse($this->date_to)->format('Y-m-d'),
      'created_at' => Carbon::now(),
      'updated_at' => Carbon::now(),
    ]);

    $id_quality = Quality::where('quality', 'FHD 1080p')->first()->id;
    $test_entries = [
      [
        'id' => $this->entry_id_1,
        'uuid' => $this->entry_uuid_1,
        'id_quality' => $id_quality,
        'title' => "a test data title",
        'date_finished' => Carbon::parse($this->date_from)->format('Y-m-d'),
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
      ], [
        'id' => $this->entry_id_2,
        'uuid' => $this->entry_uuid_2,
        'id_quality' => $id_quality,
        'title' => "x test data title",
        'date_finished' => Carbon::parse($this->date_to)->format('Y-m-d'),
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
      ], [
        'id' => $this->entry_id_3,
        'uuid' => $this->entry_uuid_3,
        'id_quality' => $id_quality,
        'title' => "z test data title",
        'date_finished' => Carbon::parse($this->date_to)->addDay()->format('Y-m-d'),
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
      ],
    ];

    Entry::insert($test_entries);
  }

  private function setup_clear() {
    Sequence::where('id', $this->sequence_id)->forceDelete();
    Entry::where('id', $this->entry_id_1)
      ->orWhere('id', $this->entry_id_2)
      ->orWhere('id', $this->entry_id_3)
      ->forceDelete();
  }

  public function test_should_get_all_entries_by_sequence_with_stats() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->get('/api/entries/by-sequence/' . $this->sequence_id);

    $response->assertStatus(200)
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

    $expected = 2;
    $this->assertCount($expected, $response['data']);

    $this->setup_clear();
  }

  public function test_should_not_get_all_entries_by_sequence_when_not_authorized() {
    $this->setup_config();

    $response = $this->get('/api/entries/by-sequence/' . $this->sequence_id);

    $response->assertStatus(401)
      ->assertJson(['message' => 'Unauthorized']);

    $this->setup_clear();
  }

  public function tests_should_not_get_all_entries_with_non_existent_sequence() {
    $invalid_id = -1;
    $response = $this->withoutMiddleware()->get('/api/entries/by-sequence/' . $invalid_id);

    $response->assertStatus(404);
  }
}
