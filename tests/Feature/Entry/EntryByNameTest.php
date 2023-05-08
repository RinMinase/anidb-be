<?php

namespace Tests\Feature\Entry;

use Tests\BaseTestCase;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Faker\Factory as Faker;

use App\Models\Entry;

class EntryByNameTest extends BaseTestCase {

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

  public function test_get_entries_by_name() {
    $this->setup_config();

    $test_data_start = 't';

    $response = $this->withoutMiddleware()
      ->get('/api/entries/by-name/' . $test_data_start);

    $response->assertStatus(200)
      ->assertJsonCount(40, 'data')
      ->assertJsonStructure([
        'data' => [[]],
      ]);

    $this->setup_clear();
  }

  public function test_get_entries_by_name_no_auth() {
    $test_data_start = 't';
    $response = $this->get('/api/entries/by-name/' . $test_data_start);

    $response->assertStatus(401)
      ->assertJson(['message' => 'Unauthorized']);
  }

  public function test_get_entries_by_name_stats() {
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
  }

  public function test_get_entries_by_name_stats_no_auth() {
    $response = $this->get('/api/entries/by-name/');

    $response->assertStatus(401)
      ->assertJson(['message' => 'Unauthorized']);
  }
}
