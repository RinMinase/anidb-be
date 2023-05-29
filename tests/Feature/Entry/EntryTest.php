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

    $entries = Entry::where('title', 'LIKE', 'test data --- %')->get();

    foreach ($entries as $entry) {
      if ($entry->season_first_title_id) $entry->season_first_title()->dissociate();
      if ($entry->prequel_id) $entry->prequel()->dissociate();
      if ($entry->sequel_id) $entry->sequel()->dissociate();

      $entry->save();
    }

    $entries->map(fn ($entry) => $entry->forceDelete());
  }

  public function test_get_all_entries() {
    $this->setup_config();

    $response = $this->withoutMiddleware()
      ->get('/api/entries');

    $response->assertStatus(200)
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

    $actual = Entry::where('title', 'LIKE', 'test data --- %')->get();
    $this->assertEquals(40, count($actual));

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

    $this->setup_clear();
  }

  public function test_get_last_30_entries() {
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

    $this->setup_clear();
  }

  public function test_get_invalid_entry() {
    $response = $this->withoutMiddleware()
      ->get('/api/entries/' . $this->entry_uuid);

    $response->assertStatus(404)
      ->assertJson(['message' => 'The provided ID is invalid, or the item does not exist']);
  }

  public function test_get_entries_no_auth() {
    $response = $this->get('/api/entries/');

    $response->assertStatus(401)
      ->assertJson(['message' => 'Unauthorized']);
  }

  public function test_get_entry_no_auth() {
    $response = $this->get('/api/entries/' . $this->entry_uuid);

    $response->assertStatus(401)
      ->assertJson(['message' => 'Unauthorized']);
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

    $this->setup_clear();
  }

  public function test_add_entry_autoconnections() {
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

    $expected3 = [
      'id_quality' => 3,
      'title' => 'test data --- test-data-part-3',
    ];

    $response = $this->withoutMiddleware()
      ->post('/api/entries/', $expected1);

    // Part 3 is inputted prior to check sequel auto-connection
    $response2 = $this->withoutMiddleware()
      ->post('/api/entries/', $expected3);

    $id1 = Entry::where('title', 'test data --- test-data-part-1')->first()->uuid;
    $id3 = Entry::where('title', 'test data --- test-data-part-3')->first()->uuid;

    $expected2 = [
      'id_quality' => 3,
      'title' => 'test data --- test-data-part-2',
      'prequel_id' => $id1,
      'sequel_id' => $id3,
    ];

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

    $this->setup_clear();
  }

  public function test_add_entry_with_invalid_keys_should_not_fail_request() {
    $this->setup_clear();

    $expected = [
      'id_quality' => 3,
      'title' => 'test data --- test-data-part-1',
      'an_invalid_key' => 'some value',
    ];

    $response = $this->withoutMiddleware()
      ->post('/api/entries/', $expected);

    $actual = Entry::where('title', $expected['title'])->first();

    $response->assertStatus(200)
      ->assertJson(['message' => 'Success']);

    $this->assertModelExists($actual);

    $this->assertEquals($expected['id_quality'], $actual->quality->id);
    $this->assertEquals($expected['title'], $actual->title);

    $this->setup_clear();
  }

  public function test_add_entry_no_auth() {
    $response = $this->post('/api/entries/');

    $response->assertStatus(401)
      ->assertJson(['message' => 'Unauthorized']);
  }

  public function test_update_entry() {
    $this->setup_config();

    $expected = [
      'id_quality' => 3,
      'title' => 'test data --- new title',
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
      ->put('/api/entries/' . $this->entry_uuid, $expected);

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

    $this->setup_clear();
  }

  public function test_update_entry_autoconnections() {
    $this->setup_clear();

    $expected1 = [
      'uuid' => '3330e0e0-0b4d-4ecd-88df-7554d0ab9f0d',
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
      'uuid' => '2d7e713b-e85a-4af0-8130-596aa8d6a45c',
      'id_quality' => 3,
      'title' => 'test data --- test-data-part-2',
    ];

    $expected3 = [
      'uuid' => '6f42e8ae-c127-4c28-821e-2952a3a58fb2',
      'id_quality' => 3,
      'title' => 'test data --- test-data-part-3',
    ];

    Entry::insert($expected1);
    Entry::insert($expected2);
    Entry::insert($expected3);

    // First data -> Second data
    $expected_connections1 = [
      'sequel_id' => $expected2['uuid'],
    ];

    // Third data <- Second Data
    $expected_connections3 = [
      'prequel_id' => $expected2['uuid'],
    ];

    $response = $this->withoutMiddleware()
      ->put('/api/entries/' . $expected1['uuid'], $expected_connections1);

    $response2 = $this->withoutMiddleware()
      ->put('/api/entries/' . $expected3['uuid'], $expected_connections3);

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

    $this->assertEquals($actual1->id, $actual2->prequel->id);
    $this->assertEquals($actual2->id, $actual1->sequel->id);
    $this->assertEquals($actual2->id, $actual3->prequel->id);
    $this->assertEquals($actual3->id, $actual2->sequel->id);

    $this->setup_clear();
  }

  public function test_update_entry_ratings() {
    $this->setup_config();

    $expected = [
      'audio' => 1,
      'enjoyment' => 2,
      'graphics' => 3,
      'plot' => 4,
    ];

    $response = $this->withoutMiddleware()
      ->put('/api/entries/ratings/' . $this->entry_uuid, $expected);

    $actual = Entry::where('uuid', $this->entry_uuid)->first();

    $response->assertStatus(200)
      ->assertJson(['message' => 'Success']);

    $this->assertModelExists($actual);

    $this->assertEquals($expected['audio'], $actual->rating->audio);
    $this->assertEquals($expected['enjoyment'], $actual->rating->enjoyment);
    $this->assertEquals($expected['graphics'], $actual->rating->graphics);
    $this->assertEquals($expected['plot'], $actual->rating->plot);

    $this->setup_clear();
  }

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

    $this->setup_clear();
  }

  public function test_delete_entry_no_auth() {
    $response = $this->delete('/api/entries/' . $this->entry_uuid);

    $response->assertStatus(401)
      ->assertJson(['message' => 'Unauthorized']);
  }
}
