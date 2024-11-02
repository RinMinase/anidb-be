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
  private $entry_rewatch_backup = null;
  private $entry_rating_backup = null;
  private $entry_offquel_backup = null;
  private $entry_backup = null;

  // Class variables
  private $bucket_id_1 = 99999;
  private $bucket_from_1 = 'a';
  private $bucket_to_1 = 'n';
  private $bucket_size_1 = 2_000_339_066_880;

  private $bucket_id_2 = 99998;
  private $bucket_from_2 = 'o';
  private $bucket_to_2 = 'z';
  private $bucket_size_2 = 2_000_339_066_880;

  private $entry_id_1 = 99999;
  private $entry_id_2 = 99998;

  private $entry_uuid_1 = '4385f9d3-3928-452c-917d-13b02fbe5687';
  private $entry_uuid_2 = '251c34f4-e281-4d7e-9ed9-6b7977a665ba';

  // Backup related tables
  private function setup_backup() {
    $this->bucket_backup = Bucket::all()->toArray();

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
    Bucket::truncate();
    Bucket::insert($this->bucket_backup);
    Bucket::refreshAutoIncrements();

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
    Bucket::truncate();
    Entry::truncate();

    Bucket::insert([
      [
        'id' => $this->bucket_id_1,
        'from' => $this->bucket_from_1,
        'to' => $this->bucket_to_1,
        'size' => $this->bucket_size_1,
      ],
      [
        'id' => $this->bucket_id_2,
        'from' => $this->bucket_from_2,
        'to' => $this->bucket_to_2,
        'size' => $this->bucket_size_2,
      ],
    ]);

    $id_quality = Quality::where('quality', 'FHD 1080p')->first()->id;

    Entry::insert([
      [
        'id' => $this->entry_id_1,
        'uuid' => $this->entry_uuid_1,
        'id_quality' => $id_quality,
        'title' => "a test data title",
        'filesize' => 500_000_000_000,
        'created_at' => '2020-01-01 13:00:00',
        'updated_at' => '2020-01-01 13:00:00',
      ],
      [
        'id' => $this->entry_id_2,
        'uuid' => $this->entry_uuid_2,
        'id_quality' => $id_quality,
        'title' => "z test data title",
        'filesize' => 400_000_000_000,
        'created_at' => '2020-01-01 13:00:00',
        'updated_at' => '2020-01-01 13:00:00',
      ],
    ]);
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
  public function test_should_get_all_entries_by_bucket() {
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
  }

  public function test_should_not_get_all_entries_with_non_existent_bucket() {
    $invalid_id = -1;

    $response = $this->withoutMiddleware()->get('/api/entries/by-bucket/' . $invalid_id);

    $response->assertStatus(404);
  }

  public function test_should_get_all_bucket_stats() {
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

    $expected = [
      [
        'id' => null,
        'from' => null,
        'to' => null,
        'free' => '2.82 TB',
        'freeTB' => '2.82 TB',
        'used' => '838.19 GB',
        'percent' => 22,
        'total' => '3.64 TB',
        'rawTotal' => $this->bucket_size_1 + $this->bucket_size_2,
        'titles' => 2,
      ],
      [
        'id' => $this->bucket_id_1,
        'from' => $this->bucket_from_1,
        'to' => $this->bucket_to_1,
        'free' => '1.36 TB',
        'freeTB' => null,
        'used' => '465.66 GB',
        'percent' => 25,
        'total' => '1.82 TB',
        'rawTotal' => $this->bucket_size_1,
        'titles' => 1,
      ],
      [
        'id' => $this->bucket_id_2,
        'from' => $this->bucket_from_2,
        'to' => $this->bucket_to_2,
        'free' => '1.46 TB',
        'freeTB' => null,
        'used' => '372.53 GB',
        'percent' => 20,
        'total' => '1.82 TB',
        'rawTotal' => $this->bucket_size_2,
        'titles' => 1,
      ]
    ];

    $this->assertEquals($expected, $response['data']);
  }
}
