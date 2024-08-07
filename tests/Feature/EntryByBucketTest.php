<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\BaseTestCase;

use App\Models\Entry;
use App\Models\Bucket;
use App\Models\EntryOffquel;
use App\Models\EntryRating;
use App\Models\EntryRewatch;
use App\Models\Quality;

class EntryByBucketTest extends BaseTestCase {

  // Backup related variables
  private $bucket_backup = null;
  private $rewatch_backup = null;
  private $rating_backup = null;
  private $offquel_backup = null;
  private $entry_backup = null;

  // Class variables
  private $bucket_from_1 = 'a';
  private $bucket_from_2 = 'o';

  private $entry_id_1 = 99999;
  private $entry_id_2 = 99998;

  private $entry_uuid_1 = '4385f9d3-3928-452c-917d-13b02fbe5687';
  private $entry_uuid_2 = '251c34f4-e281-4d7e-9ed9-6b7977a665ba';

  // Place this outside the try-catch block
  private function setup_backup() {
    // Save current bucket list
    $this->bucket_backup = Bucket::all()->toArray();

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
    Bucket::truncate();
    Entry::truncate();

    $test_bucket = [
      [
        'from' => $this->bucket_from_1,
        'to' => 'n',
        'size' => 2_000_339_066_880,
      ], [
        'from' => $this->bucket_from_2,
        'to' => 'z',
        'size' => 2_000_339_066_880,
      ],
    ];

    foreach ($test_bucket as $item) {
      Bucket::create($item);
    }

    $id_quality = Quality::where('quality', 'FHD 1080p')->first()->id;

    $test_entries = [
      [
        'id' => $this->entry_id_1,
        'uuid' => $this->entry_uuid_1,
        'id_quality' => $id_quality,
        'title' => "a test data title",
        'date_finished' => Carbon::now()->format('Y-m-d'),
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
      ], [
        'id' => $this->entry_id_2,
        'uuid' => $this->entry_uuid_2,
        'id_quality' => $id_quality,
        'title' => "z test data title",
        'date_finished' => Carbon::now()->format('Y-m-d'),
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
      ],
    ];

    Entry::insert($test_entries);
  }

  // Place this in a finally block
  private function setup_restore() {
    Bucket::truncate();
    Bucket::insert($this->bucket_backup);

    // Remove test data
    Entry::truncate();

    // Restore saved entries and relations
    Entry::insert($this->entry_backup);
    EntryOffquel::insert($this->offquel_backup);
    EntryRating::insert($this->rating_backup);
    EntryRewatch::insert($this->rewatch_backup);
  }

  public function test_should_get_all_entries_by_bucket() {
    $this->setup_backup();

    try {
      $this->setup_config();

      $bucket_id = Bucket::where('from', $this->bucket_from_1)->first()->id;
      $response = $this->withoutMiddleware()->get('/api/entries/by-bucket/' . $bucket_id);

      $entry_id = $this->entry_uuid_1;
      $actual = collect($response['data'])->filter(function ($value) use ($entry_id) {
        return ($value['id'] === $entry_id);
      })->toArray();

      $this->assertNotNull($actual);
      $this->assertCount(1, $actual);

      $bucket_id = Bucket::where('from', $this->bucket_from_2)->first()->id;
      $response = $this->withoutMiddleware()->get('/api/entries/by-bucket/' . $bucket_id);

      $entry_id = $this->entry_uuid_2;
      $actual = collect($response['data'])->filter(function ($value) use ($entry_id) {
        return ($value['id'] === $entry_id);
      })->toArray();

      $this->assertNotNull($actual);
      $this->assertCount(1, $actual);
    } finally {
      $this->setup_restore();
    }
  }

  public function test_should_not_get_all_entries_by_bucket_when_not_authorized() {
    $response = $this->get('/api/entries/by-bucket/1');

    $response->assertStatus(401)
      ->assertJson(['message' => 'Unauthorized']);
  }

  public function tests_should_not_get_all_entries_with_non_existent_bucket() {
    $this->setup_backup();

    try {
      $this->setup_config();

      $invalid_id = -1;
      $response = $this->withoutMiddleware()->get('/api/entries/by-bucket/' . $invalid_id);

      $response->assertStatus(404);
    } finally {
      $this->setup_restore();
    }
  }

  public function test_should_get_all_bucket_stats() {
    $this->setup_backup();

    try {
      $this->setup_config();

      $response = $this->withoutMiddleware()
        ->get('/api/entries/by-bucket/');

      $response->assertStatus(200)
        ->assertJsonStructure([
          'data' => [[
            'id',
            'from',
            'to',
            'free',
            'freeTB',
            'used',
            'percent',
            'total',
            'rawTotal',
            'titles',
          ]],
        ]);
    } finally {
      $this->setup_restore();
    }
  }

  public function test_should_not_get_all_bucket_stats_when_not_authorized() {
    $response = $this->get('/api/entries/by-bucket/');

    $response->assertStatus(401)
      ->assertJson(['message' => 'Unauthorized']);
  }
}
