<?php

namespace Tests\Feature;

use App\Models\Bucket;
use App\Models\Catalog;
use App\Models\Entry;
use App\Models\EntryOffquel;
use App\Models\EntryRating;
use App\Models\EntryRewatch;
use App\Models\Partial;
use Database\Seeders\BucketSeeder;
use Database\Seeders\CatalogSeeder;
use Database\Seeders\EntrySeeder;
use Database\Seeders\PartialSeeder;
use Tests\BaseTestCase;

class ManagementTest extends BaseTestCase {
  public function test_should_get_all_managment_data() {
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
    // Backup related variables
    $entry_rewatch_backup = null;
    $entry_rating_backup = null;
    $entry_offquel_backup = null;
    $entry_backup = null;

    $buckets_backup = null;
    $partials_backup = null;
    $catalogs_backup = null;

    // Save current data and relations
    $entry_rewatch_backup = EntryRewatch::all()
      ->makeVisible(['id', 'id_entries'])
      ->toArray();

    $entry_rating_backup = EntryRating::all()
      ->makeVisible(['id', 'id_entries', 'created_at', 'updated_at', 'deleted_at'])
      ->toArray();

    $entry_offquel_backup = EntryOffquel::all()
      ->makeVisible(['id_entries', 'created_at', 'updated_at', 'deleted_at'])
      ->toArray();

    $entry_backup = Entry::all()
      ->makeVisible(['id', 'id_quality', 'updated_at', 'deleted_at'])
      ->toArray();

    $buckets_backup = Bucket::all()->toArray();

    $partials_backup = Partial::all()
      ->makeVisible(['id', 'created_at', 'updated_at', 'deleted_at'])
      ->toArray();

    $catalogs_backup = Catalog::all()
      ->makeVisible(['id', 'updated_at', 'deleted_at'])
      ->toArray();

    try {
      // Remove existing values
      Entry::truncate();
      Bucket::truncate();
      Partial::truncate();
      Catalog::truncate();

      // Add testing values -- using the seeders
      (new EntrySeeder())->run();
      (new BucketSeeder())->run();
      (new CatalogSeeder())->run();
      (new PartialSeeder())->run();

      // Test the calculations with the seeded data
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
    } finally {
      // Remove test data values
      Entry::truncate();
      Bucket::truncate();
      Partial::truncate();
      Catalog::truncate();

      // Restore initially saved data
      Entry::insert($entry_backup);
      EntryOffquel::insert($entry_offquel_backup);
      EntryRating::insert($entry_rating_backup);
      EntryRewatch::insert($entry_rewatch_backup);
      Bucket::insert($buckets_backup);
      Catalog::insert($catalogs_backup);
      Partial::insert($partials_backup);

      refresh_db_table_autoincrement((new Entry())->getTable());
      refresh_db_table_autoincrement((new EntryOffquel())->getTable());
      refresh_db_table_autoincrement((new EntryRating())->getTable());
      refresh_db_table_autoincrement((new EntryRewatch())->getTable());
      refresh_db_table_autoincrement((new Bucket())->getTable());
      refresh_db_table_autoincrement((new Catalog())->getTable());
      refresh_db_table_autoincrement((new Partial())->getTable());
    }
  }

  public function test_should_not_get_all_data_when_not_authorized() {
    $response = $this->get('/api/management');

    $response->assertStatus(401)
      ->assertJson(['message' => 'Unauthorized']);
  }
}
