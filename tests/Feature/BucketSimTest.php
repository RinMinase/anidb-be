<?php

namespace Tests\Feature;

use Tests\BaseTestCase;

use Carbon\Carbon;

use App\Models\Bucket;
use App\Models\BucketSim;
use App\Models\BucketSimInfo;
use App\Models\Entry;
use App\Models\EntryOffquel;
use App\Models\EntryRating;
use App\Models\EntryRewatch;
use App\Models\Quality;

class BucketSimTest extends BaseTestCase {

  // Backup related variables
  private $bucket_sim_info_backup = null;
  private $bucket_sim_backup = null;
  private $bucket_backup = null;

  private $entry_backup = null;
  private $entry_rewatch_backup = null;
  private $entry_rating_backup = null;
  private $entry_offquel_backup = null;

  // Class variables
  private $bucket_sim_info_id = 99999;
  private $bucket_sim_info_uuid = '7293c64a-8434-4aaf-855b-b6e5cefe24df';
  private $bucket_sim_info_description = 'Testing Bucket Sim';

  private $bucket_sim_id_1 = 99999;
  private $bucket_sim_id_2 = 99998;

  private $bucket_sim_from_1 = 'a';
  private $bucket_sim_to_1 = 'm';
  private $bucket_sim_size_1 = 2_000_339_066_880;
  private $bucket_sim_from_2 = 'n';
  private $bucket_sim_to_2 = 'z';
  private $bucket_sim_size_2 = 2_000_339_066_880;

  private $bucket_id_1 = 99999;
  private $bucket_id_2 = 99998;
  private $bucket_id_3 = 99997;

  private $bucket_from_1 = 'a';
  private $bucket_to_1 = 'd';
  private $bucket_size_1 = 2_000_339_066_880;
  private $bucket_from_2 = 'e';
  private $bucket_to_2 = 'j';
  private $bucket_size_2 = 2_000_339_066_880;
  private $bucket_from_3 = 'k';
  private $bucket_to_3 = 'z';
  private $bucket_size_3 = 2_000_339_066_880;

  private $entry_id_1 = 99999;
  private $entry_id_2 = 99998;
  private $entry_id_3 = 99997;

  private $entry_uuid_1 = 'b354c456-fb16-4809-b4bb-e55f8c9ec900';
  private $entry_uuid_2 = 'a787f460-bc60-44cf-9224-3901fb5b08ca';
  private $entry_uuid_3 = '959d90bd-f1ed-4078-b374-4fd4dfedfbb6';

  private $entry_title_1 = 'a title';
  private $entry_title_2 = 'm title';
  private $entry_title_3 = 'n title';

  // Backup related tables
  private function setup_backup() {
    $hidden_columns = ['id', 'created_at', 'updated_at'];
    $this->bucket_sim_info_backup = BucketSimInfo::all()->makeVisible($hidden_columns)->toArray();

    $hidden_columns = ['id', 'created_at', 'updated_at'];
    $this->bucket_sim_backup = BucketSim::all()->makeVisible($hidden_columns)->toArray();

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
    BucketSimInfo::truncate();
    BucketSimInfo::insert($this->bucket_sim_info_backup);
    BucketSimInfo::refreshAutoIncrements();

    BucketSim::truncate();
    BucketSim::insert($this->bucket_sim_backup);
    BucketSim::refreshAutoIncrements();

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
    BucketSim::truncate();
    BucketSimInfo::truncate();
    Bucket::truncate();
    Entry::truncate();

    BucketSimInfo::insert([
      'id' => $this->bucket_sim_info_id,
      'uuid' => $this->bucket_sim_info_uuid,
      'description' => $this->bucket_sim_info_description,
      'created_at' => '2020-01-01 13:00:00',
      'updated_at' => '2020-01-01 13:00:00',
    ]);

    BucketSim::insert([
      [
        'id' => $this->bucket_sim_id_1,
        'id_sim_info' => $this->bucket_sim_info_id,
        'from' => $this->bucket_sim_from_1,
        'to' => $this->bucket_sim_to_1,
        'size' => $this->bucket_sim_size_1,
        'created_at' => '2020-01-01 13:00:00',
        'updated_at' => '2020-01-01 13:00:00',
      ],
      [
        'id' => $this->bucket_sim_id_2,
        'id_sim_info' => $this->bucket_sim_info_id,
        'from' => $this->bucket_sim_from_2,
        'to' => $this->bucket_sim_to_2,
        'size' => $this->bucket_sim_size_2,
        'created_at' => '2020-01-01 13:00:00',
        'updated_at' => '2020-01-01 13:00:00',
      ]
    ]);

    Bucket::insert([
      [
        'id' => $this->bucket_id_1,
        'from' => $this->bucket_from_1,
        'to' => $this->bucket_to_1,
        'size' => $this->bucket_size_1,
        'created_at' => '2022-01-01 13:00:00',
        'updated_at' => '2022-01-01 13:00:00',
      ],
      [
        'id' => $this->bucket_id_2,
        'from' => $this->bucket_from_2,
        'to' => $this->bucket_to_2,
        'size' => $this->bucket_size_2,
        'created_at' => '2022-01-01 13:00:00',
        'updated_at' => '2022-01-01 13:00:00',
      ],
      [
        'id' => $this->bucket_id_3,
        'from' => $this->bucket_from_3,
        'to' => $this->bucket_to_3,
        'size' => $this->bucket_size_3,
        'created_at' => '2022-01-01 13:00:00',
        'updated_at' => '2022-01-01 13:00:00',
      ],
    ]);

    $id_quality = Quality::where('quality', 'FHD 1080p')->first()->id;

    Entry::insert([
      [
        'id' => $this->entry_id_1,
        'uuid' => $this->entry_uuid_1,
        'title' => $this->entry_title_1,
        'id_quality' => $id_quality,
        'filesize' => 100_000_000_000,
        'created_at' => '2020-01-01 13:00:00',
        'updated_at' => '2020-01-01 13:00:00',
      ],
      [
        'id' => $this->entry_id_2,
        'uuid' => $this->entry_uuid_2,
        'title' => $this->entry_title_2,
        'id_quality' => $id_quality,
        'filesize' => 100_000_000_000,
        'created_at' => '2020-01-01 13:00:00',
        'updated_at' => '2020-01-01 13:00:00',
      ],
      [
        'id' => $this->entry_id_3,
        'uuid' => $this->entry_uuid_3,
        'title' => $this->entry_title_3,
        'id_quality' => $id_quality,
        'filesize' => 100_000_000_000,
        'created_at' => '2020-01-01 13:00:00',
        'updated_at' => '2020-01-01 13:00:00',
      ]
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
  public function test_should_get_bucket_sims() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->get('/api/bucket-sims');

    $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [[
          'uuid',
          'description',
        ]],
      ]);

    $expected = [[
      'uuid' => $this->bucket_sim_info_uuid,
      'description' => 'Testing Bucket Sim',
    ]];

    $this->assertEquals($expected, $response['data']);
  }

  public function test_should_add_bucket_sim_successfully() {
    $test_description = 'Test Sample Bucket Description';
    $test_buckets = '[{"from":"a","to":"i","size":2000339066880},{"from":"j","to":"z","size":1000169533440}]';

    $response = $this->withoutMiddleware()->post('/api/bucket-sims', [
      'description' => $test_description,
      'buckets' => $test_buckets,
    ]);

    $response->assertStatus(200);

    $data = BucketSimInfo::where('description', $test_description)
      ->first();

    $bucket_sims = BucketSim::select('from', 'to', 'size')
      ->where('id_sim_info', $data->id)
      ->get()
      ->toArray();

    $bucket_sim_info = $data->toArray();

    $this->assertNotNull($bucket_sim_info);
    $this->assertNotNull($bucket_sim_info['uuid']);
    $this->assertEquals($test_description, $bucket_sim_info['description']);

    $expected_count = 2;
    $expected_bucket_sims = [[
      'from' => 'a',
      'to' => 'i',
      'size' => 2_000_339_066_880
    ], [
      'from' => 'j',
      'to' => 'z',
      'size' => 1_000_169_533_440
    ]];

    $this->assertCount($expected_count, $bucket_sims);
    $this->assertEquals($expected_bucket_sims, $bucket_sims);
  }

  public function test_should_not_add_bucket_sim_on_form_errors() {
    $response = $this->withoutMiddleware()->post('/api/bucket-sims');

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['description', 'buckets']]);

    $test_description = rand_str(256 + 1);
    $test_buckets = '[{"from":"a","to":"i","size":2000339066880},{"from":"j","to":"z","size":1000169533440}';

    $response = $this->withoutMiddleware()->post('/api/bucket-sims', [
      'description' => $test_description,
      'buckets' => $test_buckets,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['description', 'buckets']]);

    $test_valid_description = 'Test Sample Bucket Description';

    $test_buckets = '[{"from":"a","to":"i","size":2000339066880},{from:"j","to":"z","size":1000169533440}]';

    $response = $this->withoutMiddleware()->post('/api/bucket-sims', [
      'description' => $test_valid_description,
      'buckets' => $test_buckets,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['buckets']]);

    $test_buckets = '';

    $response = $this->withoutMiddleware()->post('/api/bucket-sims', [
      'description' => $test_valid_description,
      'buckets' => $test_buckets,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['buckets']]);
  }

  public function test_should_edit_bucket_sim_successfully() {
    $this->setup_config();

    $test_description = 'New Test Sample Bucket Description';
    $test_buckets = '[{"from":"a","to":"i","size":2000339066880},{"from":"j","to":"z","size":1000169533440}]';

    $response = $this->withoutMiddleware()->put('/api/bucket-sims/' . $this->bucket_sim_info_uuid, [
      'description' => $test_description,
      'buckets' => $test_buckets,
    ]);

    $response->assertStatus(200);

    $data = BucketSimInfo::where('description', $test_description)
      ->first();

    $bucket_sims = BucketSim::select('from', 'to', 'size')
      ->where('id_sim_info', $data->id)
      ->get()
      ->toArray();

    $bucket_sim_info = $data->toArray();

    $this->assertNotNull($bucket_sim_info);
    $this->assertNotNull($bucket_sim_info['uuid']);
    $this->assertEquals($test_description, $bucket_sim_info['description']);

    $expected_count = 2;
    $expected_bucket_sims = [[
      'from' => 'a',
      'to' => 'i',
      'size' => 2_000_339_066_880
    ], [
      'from' => 'j',
      'to' => 'z',
      'size' => 1_000_169_533_440
    ]];

    $this->assertCount($expected_count, $bucket_sims);
    $this->assertEquals($expected_bucket_sims, $bucket_sims);
  }

  public function test_should_not_edit_bucket_sim_on_form_errors() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->put('/api/bucket-sims/' . $this->bucket_sim_info_uuid);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['description', 'buckets']]);

    $test_description = rand_str(256 + 1);
    $test_buckets = '[{"from":"a","to":"i","size":2000339066880},{"from":"j","to":"z","size":1000169533440}';

    $response = $this->withoutMiddleware()->put('/api/bucket-sims/' . $this->bucket_sim_info_uuid, [
      'description' => $test_description,
      'buckets' => $test_buckets,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['description', 'buckets']]);

    $test_valid_description = 'Test Sample Bucket Description';

    $test_buckets = '[{"from":"a","to":"i","size":2000339066880},{from:"j","to":"z","size":1000169533440}]';

    $response = $this->withoutMiddleware()->put('/api/bucket-sims/' . $this->bucket_sim_info_uuid, [
      'description' => $test_valid_description,
      'buckets' => $test_buckets,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['buckets']]);

    $test_buckets = '';

    $response = $this->withoutMiddleware()->put('/api/bucket-sims/' . $this->bucket_sim_info_uuid, [
      'description' => $test_valid_description,
      'buckets' => $test_buckets,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['buckets']]);
  }

  public function test_should_not_edit_bucket_sim_when_using_id_instead_of_uuid() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->put('/api/bucket-sims/' . $this->bucket_sim_info_id);

    $response->assertStatus(404);
  }

  public function test_should_delete_bucket_sim_successfully() {
    $this->setup_config();

    $actual = BucketSimInfo::where('uuid', $this->bucket_sim_info_uuid)
      ->first();

    $actual_bucket_sims = BucketSim::where('id_sim_info', $this->bucket_sim_info_id)
      ->get()
      ->toArray();

    $this->assertModelExists($actual);
    $this->assertGreaterThan(0, count($actual_bucket_sims));

    $response = $this->withoutMiddleware()->delete('/api/bucket-sims/' . $this->bucket_sim_info_uuid);

    $response->assertStatus(200);

    $actual = BucketSimInfo::where('uuid', $this->bucket_sim_info_uuid)
      ->first();

    $actual_bucket_sims = BucketSim::where('id_sim_info', $this->bucket_sim_info_id)
      ->get()
      ->toArray();

    $this->assertNull($actual);
    $this->assertCount(0, $actual_bucket_sims);
  }

  public function test_should_not_delete_bucket_sim_when_id_is_used_instead_of_uuid() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->delete('/api/bucket-sims/' . $this->bucket_sim_info_id);

    $response->assertStatus(404);
  }

  public function test_should_not_delete_non_existent_bucket_sim() {
    $invalid_id = 'aaaaaaaa-1234-1234-1234-aaaaaaaa1234';

    $response = $this->withoutMiddleware()->delete('/api/bucket-sims/' . $invalid_id);

    $response->assertStatus(404);

    $invalid_id = -1;

    $response = $this->withoutMiddleware()->delete('/api/bucket-sims/' . $invalid_id);

    $response->assertStatus(404);
  }

  public function test_should_get_current_entry_stats_of_bucket_sim() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->get('/api/bucket-sims/' . $this->bucket_sim_info_uuid);

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
        'stats' => [
          'uuid',
          'description',
        ],
      ]);

    $expected_data = [
      [
        'id' => null,
        'from' => null,
        'to' => null,
        'free' => '3.37 TB',
        'freeTB' => '3.37 TB',
        'used' => '279.4 GB',
        'percent' => 7,
        'total' => '3.64 TB',
        'rawTotal' => $this->bucket_sim_size_1 + $this->bucket_sim_size_2,
        'titles' => 3,
      ],
      [
        'id' => 1,
        'from' => $this->bucket_sim_from_1,
        'to' => $this->bucket_sim_to_1,
        'free' => '1.64 TB',
        'freeTB' => null,
        'used' => '186.26 GB',
        'percent' => 10,
        'total' => '1.82 TB',
        'rawTotal' => $this->bucket_sim_size_1,
        'titles' => 2,
      ],
      [
        'id' => 2,
        'from' => $this->bucket_sim_from_2,
        'to' => $this->bucket_sim_to_2,
        'free' => '1.73 TB',
        'freeTB' => null,
        'used' => '93.13 GB',
        'percent' => 5,
        'total' => '1.82 TB',
        'rawTotal' => $this->bucket_sim_size_2,
        'titles' => 1,
      ]
    ];

    $expected_stats = [
      'uuid' => $this->bucket_sim_info_uuid,
      'description' => $this->bucket_sim_info_description,
    ];

    $this->assertEqualsCanonicalizing($expected_data, $response['data']);

    $this->assertEquals($expected_stats, $response['stats']);
  }

  public function test_should_not_get_current_entry_stats_of_bucket_sim_when_using_id_instead_of_uuid() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->get('/api/bucket-sims/' . $this->bucket_sim_info_id);

    $response->assertStatus(404);
  }

  public function test_should_not_get_current_entry_stats_of_non_existent_bucket_sim() {
    $invalid_id = 'aaaaaaaa-1234-1234-1234-aaaaaaaa1234';

    $response = $this->withoutMiddleware()->get('/api/bucket-sims/' . $invalid_id);

    $response->assertStatus(404);

    $invalid_id = -1;

    $response = $this->withoutMiddleware()->get('/api/bucket-sims/' . $invalid_id);

    $response->assertStatus(404);
  }

  public function test_should_save_bucket_sim_as_bucket() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->post('/api/bucket-sims/save/' . $this->bucket_sim_info_uuid);

    $response->assertStatus(200);

    $data = Bucket::select('from', 'to', 'size')
      ->orderBy('from', 'asc')
      ->get();

    $actual = $data->toArray();

    $expected = [[
      'from' => $this->bucket_sim_from_1,
      'to' => $this->bucket_sim_to_1,
      'size' => $this->bucket_sim_size_1,
    ], [
      'from' => $this->bucket_sim_from_2,
      'to' => $this->bucket_sim_to_2,
      'size' => $this->bucket_sim_size_2,
    ]];

    $this->assertNotNull($actual);
    $this->assertEquals($expected, $actual);
  }

  public function test_should_not_save_bucket_sim_as_bucket_when_using_bucket_id_instead_of_uuid() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->post('/api/bucket-sims/save/' . $this->bucket_sim_info_id);

    $response->assertStatus(404);
  }

  public function test_should_not_save_non_existent_bucket_sim_as_bucket() {
    $invalid_id = 'aaaaaaaa-1234-1234-1234-aaaaaaaa1234';

    $response = $this->withoutMiddleware()->post('/api/bucket-sims/save/' . $invalid_id);

    $response->assertStatus(404);

    $invalid_id = -1;

    $response = $this->withoutMiddleware()->post('/api/bucket-sims/save/' . $invalid_id);

    $response->assertStatus(404);
  }

  public function test_should_clone_bucket_sim_successfully() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->post('/api/bucket-sims/clone/' . $this->bucket_sim_info_uuid);

    $response->assertStatus(200);

    $this->assertNotNull($response['data']);
    $this->assertNotNull($response['data']['newId']);
    $new_id = $response['data']['newId'];

    $new_bucket_sim = BucketSimInfo::where('uuid', $new_id)
      ->firstOrFail()
      ->makeVisible('id');

    $this->assertNotNull($new_bucket_sim);

    $expected_description = $this->bucket_sim_info_description . ' - cloned';
    $this->assertEquals($expected_description, $new_bucket_sim->description);

    $new_buckets = BucketSim::where('id_sim_info', $new_bucket_sim->id)->get()->toArray();
    $expected_total_buckets = 2;
    $this->assertEquals($expected_total_buckets, count($new_buckets));

    $expected_buckets = [[
      'from' => $this->bucket_sim_from_1,
      'to' => $this->bucket_sim_to_1,
      'size' => $this->bucket_sim_size_1,
    ], [
      'from' => $this->bucket_sim_from_2,
      'to' => $this->bucket_sim_to_2,
      'size' => $this->bucket_sim_size_2,
    ]];

    $this->assertArrayIsEqualToArrayOnlyConsideringListOfKeys(
      $expected_buckets,
      $new_buckets,
      ['from', 'to', 'size'],
    );
  }

  public function test_should_not_clone_bucket_sim_when_using_bucket_id_instead_of_uuid() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->post('/api/bucket-sims/clone/' . $this->bucket_sim_info_id);

    $response->assertStatus(404);
  }

  public function test_should_not_clone_non_existent_bucket_sim() {
    $invalid_id = 'aaaaaaaa-1234-1234-1234-aaaaaaaa1234';

    $response = $this->withoutMiddleware()->post('/api/bucket-sims/clone/' . $invalid_id);

    $response->assertStatus(404);

    $invalid_id = -1;

    $response = $this->withoutMiddleware()->post('/api/bucket-sims/clone/' . $invalid_id);

    $response->assertStatus(404);
  }

  public function test_should_preview_bucket_sim_successfully() {
    $this->setup_config();

    $test_buckets = '[{"from":"a","to":"i","size":2000339066880},{"from":"j","to":"z","size":1000169533440}]';

    $response = $this->withoutMiddleware()->post('/api/bucket-sims/preview', [
      'buckets' => $test_buckets,
    ]);

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

    $expected_data = [
      [
        'id' => null,
        'from' => null,
        'to' => null,
        'free' => '2.46 TB',
        'freeTB' => '2.46 TB',
        'used' => '279.4 GB',
        'percent' => 10,
        'total' => '2.73 TB',
        'rawTotal' => 2000339066880 + 1000169533440,
        'titles' => 3,
      ],
      [
        'id' => 1,
        'from' => 'a',
        'to' => 'i',
        'free' => '1.73 TB',
        'freeTB' => null,
        'used' => '93.13 GB',
        'percent' => 5,
        'total' => '1.82 TB',
        'rawTotal' => 2000339066880,
        'titles' => 1,
      ],
      [
        'id' => 2,
        'from' => 'j',
        'to' => 'z',
        'free' => '745.22 GB',
        'freeTB' => null,
        'used' => '186.26 GB',
        'percent' => 20,
        'total' => '931.48 GB',
        'rawTotal' => 1000169533440,
        'titles' => 2,
      ]
    ];

    $this->assertEqualsCanonicalizing($expected_data, $response['data']);
  }

  public function test_should_not_preview_bucket_sim_on_form_errors() {
    $response = $this->withoutMiddleware()->post('/api/bucket-sims/preview');

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['buckets']]);

    $test_buckets_401 = [
      '[{"":"a","to":"i","size":2000339066880},{"from":"j","to":"z","size":1000169533440}',
      '[{"from":"a","to":"i","size":2000339066880},{from:"j","to":"z","size":1000169533440}]',
      '',
    ];

    foreach ($test_buckets_401 as $key => $value) {
      $response = $this->withoutMiddleware()->post('/api/bucket-sims/preview', [
        'buckets' => $value,
      ]);

      $this->assertEquals(401, $response['status'], 'Error in $key=' . $key);
      $this->assertArrayHasKey('buckets', $response['data'], 'Error in $key=' . $key);
    }

    $test_buckets_400 = [
      '[{"from":"b","to":"a","size":2000339066880}]',
      '[{"from":"a","to":"a","size":0}]',
      '[{"from":"2","to":"a","size":0}]',
      '[{"from":"a","to":"2","size":0}]',
    ];

    foreach ($test_buckets_400 as $key => $value) {
      $response = $this->withoutMiddleware()->post('/api/bucket-sims/preview', [
        'buckets' => $value,
      ]);

      $this->assertEquals(400, $response['status'], 'Error in $key=' . $key);
    }
  }

  public function test_should_backup_current_bucket_to_bucket_sim_sucessfully() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->post('/api/bucket-sims/backup');

    $response->assertStatus(200);

    $date = Carbon::now()->format('Y-m-d');
    $actual_data_info = BucketSimInfo::where('description', 'Backup of bucket - ' . $date)
      ->get()
      ->makeVisible('id')
      ->toArray();

    // Check if created only one info
    $this->assertCount(1, $actual_data_info);

    $actual_data_info = $actual_data_info[0];

    $actual_data_bucket_sims = BucketSim::where('id_sim_info', $actual_data_info['id'])
      ->get()
      ->toArray();

    $expected_data_bucket_sims = [
      [
        'from' => $this->bucket_from_1,
        'to' => $this->bucket_to_1,
        'size' => $this->bucket_size_1,
        'created_at' => '2022-01-01 13:00:00',
        'updated_at' => '2022-01-01 13:00:00',
      ],
      [
        'from' => $this->bucket_from_2,
        'to' => $this->bucket_to_2,
        'size' => $this->bucket_size_2,
        'created_at' => '2022-01-01 13:00:00',
        'updated_at' => '2022-01-01 13:00:00',
      ],
      [
        'from' => $this->bucket_from_3,
        'to' => $this->bucket_to_3,
        'size' => $this->bucket_size_3,
        'created_at' => '2022-01-01 13:00:00',
        'updated_at' => '2022-01-01 13:00:00',
      ],
    ];

    $this->assertCount(count($expected_data_bucket_sims), $actual_data_bucket_sims);
    $this->assertArrayIsEqualToArrayOnlyConsideringListOfKeys(
      $expected_data_bucket_sims,
      $actual_data_bucket_sims,
      ['from', 'to', 'size', 'created_at', 'updated_at'],
    );
  }
}
