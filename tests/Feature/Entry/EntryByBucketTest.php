<?php

namespace Tests\Feature\Entry;

use Tests\BaseTestCase;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Faker\Factory as Faker;

use App\Models\Entry;
use App\Models\Bucket;

class EntryByBucketTest extends BaseTestCase {

  private function setup_config() {
    $faker = Faker::create();

    // Clearing possible duplicate data
    $this->setup_clear();

    $uuid_list = [];
    $additional_test_data = [];

    for ($i = 0; $i < 40; $i++) {
      $new_uuid = Str::uuid()->toString();

      while (in_array($new_uuid, $uuid_list)) {
        $new_uuid = Str::uuid()->toString();
      }

      $uuid_list[] = $new_uuid;
      $additional_test_data[] = [
        'uuid' => $new_uuid,
        'id_quality' => 1,
        'title' => 'test data --- ' . $faker->text(20),
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
      ];
    }

    Entry::insert($additional_test_data);
  }

  private function setup_clear() {
    Entry::where('title', 'LIKE', 'test data --- %')->forceDelete();
  }

  public function test_get_buckets_with_entry_stats() {
    $this->setup_config();

    $buckets = Bucket::select('id', 'from', 'to')->get();

    $test_data_start = 't';
    $bucket_id = null;

    foreach ($buckets as $bucket) {
      if ($test_data_start >= $bucket->from && $test_data_start <= $bucket->to) {
        $bucket_id = $bucket->id;
      }
    }

    $this->assertNotNull($bucket_id, 'No Bucket was found to match the letter "T"');

    if ($bucket_id) {
      $response = $this->withoutMiddleware()
        ->get('/api/entries/by-bucket/' . $bucket_id);

      $response->assertStatus(200)
        ->assertJsonCount(40, 'data.data')
        ->assertJsonStructure([
          'data' => [
            'data' => [[]],
            'stats' => [
              'from',
              'to',
            ],
          ],
        ]);
    }

    $this->setup_clear();
  }

  public function test_get_buckets_with_entry_stats_no_auth() {
    $response = $this->get('/api/entries/by-bucket/');

    $response->assertStatus(401)
      ->assertJson(['message' => 'Unauthorized']);
  }

  public function test_get_entries_by_bucket() {
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
  }

  public function test_get_entries_by_bucket_no_auth() {
    $response = $this->get('/api/entries/by-bucket/1');

    $response->assertStatus(401)
      ->assertJson(['message' => 'Unauthorized']);
  }
}
