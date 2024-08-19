<?php

namespace Tests\Feature;


use Tests\BaseTestCase;

use Database\Seeders\BucketSeeder;
use Database\Seeders\CatalogSeeder;
use Database\Seeders\EntrySeeder;
use Database\Seeders\PartialSeeder;

use App\Models\Bucket;
use App\Models\Catalog;
use App\Models\Entry;
use App\Models\EntryOffquel;
use App\Models\EntryRating;
use App\Models\EntryRewatch;
use App\Models\Partial;

class ManagementTest extends BaseTestCase {

  // Backup related variables
  private $entry_rewatch_backup = null;
  private $entry_rating_backup = null;
  private $entry_offquel_backup = null;
  private $entry_backup = null;

  private $buckets_backup = null;
  private $partials_backup = null;
  private $catalogs_backup = null;

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

    $this->buckets_backup = Bucket::all()->toArray();

    $hidden_columns = ['id', 'created_at', 'updated_at', 'deleted_at'];
    $this->partials_backup = Partial::all()->makeVisible($hidden_columns)->toArray();

    $hidden_columns = ['id', 'updated_at', 'deleted_at'];
    $this->catalogs_backup = Catalog::all()->makeVisible($hidden_columns)->toArray();
  }

  private function setup_restore() {
    Entry::truncate();
    Bucket::truncate();
    Partial::truncate();
    Catalog::truncate();

    Entry::insert($this->entry_backup);
    EntryOffquel::insert($this->entry_offquel_backup);
    EntryRating::insert($this->entry_rating_backup);
    EntryRewatch::insert($this->entry_rewatch_backup);
    Bucket::insert($this->buckets_backup);
    Catalog::insert($this->catalogs_backup);
    Partial::insert($this->partials_backup);

    Entry::refreshAutoIncrements();
    EntryOffquel::refreshAutoIncrements();
    EntryRating::refreshAutoIncrements();
    EntryRewatch::refreshAutoIncrements();
    Bucket::refreshAutoIncrements();
    Catalog::refreshAutoIncrements();
    Partial::refreshAutoIncrements();
  }

  // Setup data for testing
  private function setup_config() {
    Entry::truncate();
    Bucket::truncate();
    Partial::truncate();
    Catalog::truncate();

    Entry::refreshAutoIncrements();
    EntryOffquel::refreshAutoIncrements();
    EntryRating::refreshAutoIncrements();
    EntryRewatch::refreshAutoIncrements();
    Bucket::refreshAutoIncrements();
    Catalog::refreshAutoIncrements();
    Partial::refreshAutoIncrements();

    (new EntrySeeder())->run();
    (new BucketSeeder())->run();
    (new CatalogSeeder())->run();
    (new PartialSeeder())->run();
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
  public function test_should_get_all_managment_data() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->get('/api/management');

    $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
          'count' => [
            'entries',
            'buckets',
            'partials',
          ],
          'stats' => [
            'watchSeconds',
            'watch',
            'watchSubtext',
            'rewatchSeconds',
            'rewatch',
            'rewatchSubtext',
            'bucketSize',
            'entrySize',
            'episodes',
            'titles',
            'seasons',
          ],
          'graph' => [
            'quality' => [
              'quality2160',
              'quality1080',
              'quality720',
              'quality480',
              'quality360',
            ],
            'months' => [
              'jan',
              'feb',
              'mar',
              'apr',
              'may',
              'jun',
              'jul',
              'aug',
              'sep',
              'oct',
              'nov',
              'dec',
            ]
          ]
        ],
      ]);
  }

  public function test_should_validate_management_stats() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->get('/api/management');

    $expected_count = [
      'entries' => 9,
      'buckets' => 6,
      'partials' => 4,
    ];

    $expected_stats = [
      'watchSeconds' => 1080000,
      'watch' => '12 days',
      'watchSubtext' => '12 hours',
      'rewatchSeconds' => 2100000,
      'rewatch' => '24 days',
      'rewatchSubtext' => '7 hours 20 minutes',
      'bucketSize' => '10.92 TB',
      'entrySize' => '365.23 GB',
      'episodes' => 90,
      'titles' => 9,
      'seasons' => 7,
    ];

    $expected_graph_quality = [
      'quality2160' => 1,
      'quality1080' => 2,
      'quality720' => 2,
      'quality480' => 3,
      'quality360' => 1,
    ];

    $expected_graph_months = [
      'jan' => 4,
      'feb' => 0,
      'mar' => 0,
      'apr' => 1,
      'may' => 1,
      'jun' => 1,
      'jul' => 1,
      'aug' => 1,
      'sep' => 0,
      'oct' => 0,
      'nov' => 0,
      'dec' => 0,
    ];

    $response->assertStatus(200);

    $actual = $response['data'];

    $this->assertNotNull($actual['count']);
    $this->assertEquals($expected_count, $actual['count']);

    $this->assertNotNull($actual['stats']);
    $this->assertEquals($expected_stats, $actual['stats']);

    $this->assertNotNull($actual['graph']);
    $this->assertNotNull($actual['graph']['quality']);
    $this->assertEquals($expected_graph_quality, $actual['graph']['quality']);

    $this->assertNotNull($actual['graph']);
    $this->assertNotNull($actual['graph']['months']);
    $this->assertEquals($expected_graph_months, $actual['graph']['months']);
  }
}
