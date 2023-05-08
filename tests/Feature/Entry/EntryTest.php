<?php

namespace Tests\Feature\Entry;

use Tests\BaseTestCase;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Faker\Factory as Faker;

use App\Models\Entry;

class EntryTest extends BaseTestCase {

  private $entry_uuid = "e9597119-8452-4f2b-96d8-f2b1b1d2f158";

  private function setup_config() {
    $faker = Faker::create();

    // Clearing possible duplicate data
    $this->setup_clear();

    Entry::create([
      'uuid' => $this->entry_uuid,
      'id_quality' => 1,
      'title' => "title",
      'date_finished' => "2001-01-01",
      'duration' => 10_000,
      'filesize' => 10_000_000,
      'episodes' => 30,
      'ovas' => 20,
      'specials' => 10,
      'encoder_video' => "encoder video",
      'encoder_audio' => "encoder audio",
      'encoder_subs' => "encoder subs",
      'codec_hdr' => 1,
      'codec_video' => 1,
      'codec_audio' => 1,
      'release_year' => 2000,
      'release_season' => "Winter",
      'variants' => "variant",
      'remarks' => "remark",
    ]);

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
    Entry::where('uuid', $this->entry_uuid)->forceDelete();
    Entry::where('title', 'LIKE', 'test data --- %')->forceDelete();
  }

  public function test_get_all_entries() {
    $this->setup_config();

    $response = $this->withoutMiddleware()
      ->get('/api/entries');

    $response->assertStatus(200)
      ->assertJsonCount(30, 'data')
      ->assertJsonStructure([
        'data' => [[
          'id',
          'quality',
          'title',
          'dateFinished',
          'rewatched',
          'filesize',
          'episodes',
          'ovas',
          'specials',
          'encoder',
          'release',
          'remarks',
          'rating',
        ]],
        'meta' => [
          'page',
          'limit',
          'total',
          'has_next',
        ],
      ]);

    // Clearing test data
    $this->setup_clear();
  }

  public function test_get_all_entries_pagination() {
    $this->setup_config();

    $response = $this->withoutMiddleware()
      ->get('/api/entries?page=2');

    $response->assertStatus(200)
      ->assertJson([
        'meta' => [
          'page' => 2,
          'limit' => 30,
        ],
      ]);

    // Clearing test data
    $this->setup_clear();
  }

  public function test_get_entry() {
    $this->setup_config();

    $response = $this->withoutMiddleware()
      ->get('/api/entries/' . $this->entry_uuid);

    $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
          'id',
          'quality',
          'id_quality',
          'title',
          'dateInitFinishedRaw',
          'dateInitFinished',
          'dateLastFinished',
          'durationRaw',
          'duration',
          'filesizeRaw',
          'filesize',
          'episodes',
          'ovas',
          'specials',
          'seasonNumber',
          'seasonFirstTitle',
          'prequelTitle',
          'prequel',
          'sequelTitle',
          'sequel',
          'encoder',
          'encoderVideo',
          'encoderAudio',
          'encoderSubs',
          'releaseSeason',
          'releaseYear',
          'release',
          'variants',
          'remarks',
          'codecHDR',
          'id_codec_video',
          'codecVideo',
          'id_codec_audio',
          'codecAudio',
          'offquels',
          'rewatches',
          'ratingAverage',
          'rating',
          'image',
        ]
      ]);

    // Clearing test data
    $this->setup_clear();
  }

  public function test_get_invalid_entry() {
    $response = $this->withoutMiddleware()
      ->get('/api/entries/' . $this->entry_uuid);

    $response->assertStatus(401)
      ->assertJson(['message' => 'The provided ID is invalid, or the item does not exist']);
  }

  public function test_get_entries_no_auth() {
    $this->setup_config();

    $response = $this->get('/api/entries/');

    $response->assertStatus(401)
      ->assertJson(['message' => 'Unauthorized']);

    // Clearing test data
    $this->setup_clear();
  }

  public function test_get_entry_no_auth() {
    $this->setup_config();

    $response = $this->get('/api/entries/' . $this->entry_uuid);

    $response->assertStatus(401)
      ->assertJson(['message' => 'Unauthorized']);

    // Clearing test data
    $this->setup_clear();
  }

  public function test_add_entry() {
    $this->setup_clear();

    $expected = [
      'id_quality' => 3,
      'title' => 'test data --- test-data-part-1',
      'date_finished' => '2020-10-21',
      'duration' => 100,
      'filesize' => 1000000,
      'episodes' => 12,
      'ovas' => 11,
      'specials' => 10,
      'encoder_video' => 'video',
      'encoder_audio' => 'audio',
      'encoder_subs' => 'subs',
      'release_year' => 2020,
      'release_season' => 'Spring',
      'variants' => 'variant',
      'remarks' => 'remark',
      'id_codec_audio' => 1,
      'id_codec_video' => 1,
      'codec_hdr' => 0,
    ];

    $response = $this->withoutMiddleware()
      ->post('/api/entries/', $expected);

    $actual = Entry::where('title', $expected['title'])->first();

    $response->assertStatus(200)
      ->assertJson(['message' => 'Success']);

    $this->assertModelExists($actual);

    $this->assertEquals($expected['id_quality'], $actual->quality->id);
    $this->assertEquals($expected['title'], $actual->title);
    $this->assertEquals($expected['date_finished'], $actual->date_finished);
    $this->assertEquals($expected['duration'], $actual->duration);
    $this->assertEquals($expected['filesize'], $actual->filesize);
    $this->assertEquals($expected['episodes'], $actual->episodes);
    $this->assertEquals($expected['ovas'], $actual->ovas);
    $this->assertEquals(1, $actual->season_number);
    $this->assertEquals($expected['title'], $actual->season_first_title->title);
    $this->assertEquals($expected['encoder_video'], $actual->encoder_video);
    $this->assertEquals($expected['encoder_audio'], $actual->encoder_audio);
    $this->assertEquals($expected['encoder_subs'], $actual->encoder_subs);
    $this->assertEquals($expected['codec_hdr'], $actual->codec_hdr);
    $this->assertEquals($expected['id_codec_audio'], $actual->id_codec_audio);
    $this->assertEquals($expected['id_codec_video'], $actual->id_codec_video);
    $this->assertEquals($expected['variants'], $actual->variants);
    $this->assertEquals($expected['remarks'], $actual->remarks);
    $this->assertEquals($expected['release_season'], $actual->release_season);
    $this->assertEquals($expected['release_year'], $actual->release_year);

    $actual->forceDelete();
  }

  public function test_add_entry_connected() {
    $this->setup_clear();

    $expected1 = [
      'id_quality' => 3,
      'title' => 'test data --- test-data-part-1',
      'date_finished' => '2020-10-21',
      'duration' => 100,
      'filesize' => 1000000,
      'episodes' => 12,
      'ovas' => 11,
      'specials' => 10,
      'encoder_video' => 'video',
      'encoder_audio' => 'audio',
      'encoder_subs' => 'subs',
      'release_year' => 2020,
      'release_season' => 'Spring',
      'variants' => 'variant',
      'remarks' => 'remark',
      'id_codec_audio' => 1,
      'id_codec_video' => 1,
      'codec_hdr' => 0,
    ];

    $expected2 = [
      'id_quality' => 3,
      'title' => 'test data --- test-data-part-2',
      'prequel_title' => 'test data --- test-data-part-1',
      'sequel_title' => 'test data --- test-data-part-3',
    ];

    $expected3 = [
      'id_quality' => 3,
      'title' => 'test data --- test-data-part-3',
    ];

    $response = $this->withoutMiddleware()
      ->post('/api/entries/', $expected1);

    // Part 3 is inputted prior to check sequel auto-connection
    $response2 = $this->withoutMiddleware()
      ->post('/api/entries/', $expected3);

    $response2 = $this->withoutMiddleware()
      ->post('/api/entries/', $expected2);

    $actual1 = Entry::where('title', $expected1['title'])->first();
    $actual2 = Entry::where('title', $expected2['title'])->first();
    $actual3 = Entry::where('title', $expected3['title'])->first();

    $response->assertStatus(200)
      ->assertJson(['message' => 'Success']);

    $response2->assertStatus(200)
      ->assertJson(['message' => 'Success']);

    $this->assertModelExists($actual1)
      ->assertModelExists($actual2)
      ->assertModelExists($actual3);

    $this->assertEquals($actual1->id, $actual2->prequel_id);
    $this->assertEquals($actual2->id, $actual1->sequel_id);
    $this->assertEquals($actual2->id, $actual3->prequel_id);
    $this->assertEquals($actual3->id, $actual2->sequel_id);

    $actual1->sequel_id = null;
    $actual2->prequel_id = null;
    $actual2->sequel_id = null;
    $actual3->prequel_id = null;

    $actual1->save();
    $actual2->save();
    $actual3->save();

    $actual1->forceDelete();
    $actual2->forceDelete();
    $actual3->forceDelete();
  }

  public function test_add_entry_no_auth() {
    $response = $this->post('/api/entries/');

    $response->assertStatus(401)
      ->assertJson(['message' => 'Unauthorized']);
  }

  // public function test_update_entry() {
  // }

  // public function test_update_entry_ratings() {
  // }

  public function test_update_entry_no_auth() {
    $response = $this->put('/api/entries/' . $this->entry_uuid);

    $response->assertStatus(401)
      ->assertJson(['message' => 'Unauthorized']);
  }

  public function test_delete_entry() {
    $this->setup_config();

    $response = $this->withoutMiddleware()
      ->delete('/api/entries/' . $this->entry_uuid);

    $response->assertStatus(200)
      ->assertJson(['message' => 'Success']);

    $entry = Entry::withTrashed()
      ->where('uuid', $this->entry_uuid)
      ->first();

    $this->assertSoftDeleted($entry);

    // Clearing test data
    $this->setup_clear();
  }

  public function test_delete_entry_no_auth() {
    $this->setup_config();

    $response = $this->delete('/api/entries/' . $this->entry_uuid);

    $response->assertStatus(401)
      ->assertJson(['message' => 'Unauthorized']);

    // Clearing test data
    $this->setup_clear();
  }
}
