<?php

namespace Tests\Feature;

use Tests\BaseTestCase;

use App\Models\Bucket;
use App\Models\BucketSim;
use App\Models\BucketSimInfo;

class BucketSimTest extends BaseTestCase {

  private $bucket_sim_info_id = 99999;
  private $bucket_sim_info_uuid = '7293c64a-8434-4aaf-855b-b6e5cefe24df';

  private $bucket_sim_id_1 = 99999;
  private $bucket_sim_id_2 = 99998;

  private function setup_config() {
    // Clearing possible duplicate data
    $this->setup_clear();

    BucketSimInfo::insert([
      'id' => $this->bucket_sim_info_id,
      'uuid' => $this->bucket_sim_info_uuid,
      'description' => 'Testing Bucket Sim',
      'created_at' => '2020-01-01 13:00:00',
      'updated_at' => '2020-01-01 13:00:00',
    ]);

    BucketSim::insert([[
      'id' => $this->bucket_sim_id_1,
      'id_sim_info' => $this->bucket_sim_info_id,
      'from' => 'a',
      'to' => 'm',
      'size' => 2_000_339_066_880,
      'created_at' => '2020-01-01 13:00:00',
      'updated_at' => '2020-01-01 13:00:00',
    ], [
      'id' => $this->bucket_sim_id_2,
      'id_sim_info' => $this->bucket_sim_info_id,
      'from' => 'n',
      'to' => 'z',
      'size' => 2_000_339_066_880,
      'created_at' => '2020-01-01 13:00:00',
      'updated_at' => '2020-01-01 13:00:00',
    ]]);
  }

  private function setup_clear() {
    BucketSimInfo::where('id', $this->bucket_sim_info_id)->forceDelete();
  }

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

    $this->setup_clear();
  }

  public function test_should_not_get_bucket_sims_when_not_authorized() {
    $response = $this->get('/api/bucket-sims');

    $response->assertStatus(401)
      ->assertJson(['message' => 'Unauthorized']);
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

    $data->delete();
  }

  public function test_should_not_add_bucket_sim_on_form_errors() {
    $response = $this->withoutMiddleware()->post('/api/bucket-sims');

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['description', 'buckets']]);

    $test_description = 'Test Sample Bucket Description ' . rand_str(256);
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

    $data->delete();

    $this->setup_clear();
  }

  public function test_should_not_edit_bucket_sim_on_form_errors() {
    $response = $this->withoutMiddleware()->put('/api/bucket-sims/' . $this->bucket_sim_info_uuid);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['description', 'buckets']]);

    $test_description = 'Test Sample Bucket Description ' . rand_str(256);
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

    $this->setup_clear();
  }

  public function test_should_delete_bucket_sim_successfully() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->delete('/api/bucket-sims/' . $this->bucket_sim_info_uuid);

    $response->assertStatus(200);

    $actual = BucketSimInfo::where('uuid', $this->bucket_sim_info_uuid)
      ->first();

    $actual_bucket_sims = BucketSim::where('id_sim_info', $this->bucket_sim_info_id)
      ->get()
      ->toArray();

    $this->assertNull($actual);
    $this->assertCount(0, $actual_bucket_sims);

    $this->setup_clear();
  }

  public function test_should_not_delete_non_existent_bucket_sim() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->delete('/api/bucket-sims/' . $this->bucket_sim_info_id);

    $response->assertStatus(404);

    $this->setup_clear();
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

    $this->setup_clear();
  }

  public function test_should_not_get_current_entry_stats_of_bucket_sim_when_using_id_instead_of_uuid() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->get('/api/bucket-sims/' . $this->bucket_sim_info_id);

    $response->assertStatus(404);

    $this->setup_clear();
  }
}
