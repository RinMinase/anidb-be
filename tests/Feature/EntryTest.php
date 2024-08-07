<?php

namespace Tests\Feature;

use Error;
use Carbon\Carbon;
use Cloudinary\Api\Admin\AdminApi;
use Cloudinary\Api\Upload\UploadApi;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Tests\BaseTestCase;

use App\Models\CodecAudio;
use App\Models\CodecVideo;
use App\Models\Entry;
use App\Models\EntryOffquel;
use App\Models\EntryRating;
use App\Models\EntryRewatch;
use App\Models\Quality;

class EntryTest extends BaseTestCase {

  // Backup related variables
  private $rewatch_backup = null;
  private $rating_backup = null;
  private $offquel_backup = null;
  private $entry_backup = null;

  // Class variables
  private $total_entry_count = 5;

  private $entry_id_1 = 99999;
  private $entry_id_2 = 99998;
  private $entry_id_3 = 99997;
  private $entry_id_4 = 99996;
  private $entry_id_5 = 99995;

  private $entry_uuid_1 = 'b354c456-fb16-4809-b4bb-e55f8c9ec900';
  private $entry_uuid_2 = 'a787f460-bc60-44cf-9224-3901fb5b08ca';
  private $entry_uuid_3 = '959d90bd-f1ed-4078-b374-4fd4dfedfbb6';
  private $entry_uuid_4 = '64b3e54c-8280-4275-b5c2-5361065a5bf9';
  private $entry_uuid_5 = 'ddd65078-5d05-48a3-9604-a2ed9f4a679e';

  private $entry_1_image = '__test_data__8fa9b149-0185-41b2-b6c2-7d2ac7512eb4';
  // cached value throughout the whole test, make single call only to API
  private static $entry_1_image_url = null;

  private $entry_1_rating_audio = 6;
  private $entry_1_rating_enjoyment = 5;
  private $entry_1_rating_graphics = 4;
  private $entry_1_rating_plot = 3;

  private $entry_1_rewatch_id = 99999;
  private $entry_1_rewatch_uuid = 'e16593ad-ed01-4314-b4b1-0120ba734f90';

  // Place this outside the try-catch block
  private function setup_backup() {
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
    Entry::truncate();

    $id_quality = Quality::where('quality', 'FHD 1080p')->first()->id;
    $timestamp = Carbon::now();
    $date_finished_1 = Carbon::parse('2001-01-01')->format('Y-m-d');
    $date_finished_2 = Carbon::parse('2001-01-02')->format('Y-m-d');
    $date_finished_3 = Carbon::parse('2001-01-03')->format('Y-m-d');
    $date_finished_4 = Carbon::parse('2001-01-04')->format('Y-m-d');
    $date_finished_5 = Carbon::parse('2001-01-05')->format('Y-m-d');
    $date_finished_rewatch = Carbon::parse('2001-02-01')->format('Y-m-d');

    if (self::$entry_1_image_url === null) {
      echo "\nINFO: API call to Cloudinary:AdminAPI:asset\n";

      $image_url = (new AdminApi())->asset($this->entry_1_image)['url'];

      if (!$image_url) {
        throw new Error('Image URL was not acquired');
      }

      self::$entry_1_image_url = $image_url;
    }

    $test_entries = [
      [
        'id' => $this->entry_id_1,
        'uuid' => $this->entry_uuid_1,
        'id_quality' => $id_quality,
        'date_finished' => $date_finished_1,
        'title' => 'testing series title season 1',
        'season_number' => 1,
        'prequel_id' => null,
        'sequel_id' => $this->entry_id_4,
        'created_at' => $timestamp,
        'updated_at' => $timestamp,
        'image' => self::$entry_1_image_url,
      ], [
        'id' => $this->entry_id_2,
        'uuid' => $this->entry_uuid_2,
        'id_quality' => $id_quality,
        'date_finished' => $date_finished_2,
        'title' => 'testing another solo title',
        'season_number' => 1,
        'prequel_id' => null,
        'sequel_id' => null,
        'created_at' => $timestamp,
        'updated_at' => $timestamp,
        'image' => null,
      ], [
        'id' => $this->entry_id_3,
        'uuid' => $this->entry_uuid_3,
        'id_quality' => $id_quality,
        'date_finished' => $date_finished_3,
        'title' => 'test offquel',
        'season_number' => 1,
        'prequel_id' => null,
        'sequel_id' => null,
        'created_at' => $timestamp,
        'updated_at' => $timestamp,
        'image' => null,
      ], [
        'id' => $this->entry_id_4,
        'uuid' => $this->entry_uuid_4,
        'id_quality' => $id_quality,
        'date_finished' => $date_finished_4,
        'title' => 'testing series title season 2',
        'season_number' => 2,
        'prequel_id' => $this->entry_id_1,
        'sequel_id' => $this->entry_id_5,
        'created_at' => $timestamp,
        'updated_at' => $timestamp,
        'image' => null,
      ], [
        'id' => $this->entry_id_5,
        'uuid' => $this->entry_uuid_5,
        'id_quality' => $id_quality,
        'date_finished' => $date_finished_5,
        'title' => 'testing series title season 3',
        'season_number' => 3,
        'prequel_id' => $this->entry_id_4,
        'sequel_id' => null,
        'created_at' => $timestamp,
        'updated_at' => $timestamp,
        'image' => null,
      ],
    ];

    $test_entry_offquel = [
      'id_entries' => $this->entry_id_1,          // parent entry
      'id_entries_offquel' => $this->entry_id_3,  // offquel entry
    ];

    $test_entry_rating = [
      'id_entries' => $this->entry_id_1,
      'audio' => $this->entry_1_rating_audio,
      'enjoyment' => $this->entry_1_rating_enjoyment,
      'graphics' => $this->entry_1_rating_graphics,
      'plot' => $this->entry_1_rating_plot,
      'created_at' => $timestamp,
      'updated_at' => $timestamp,
    ];

    $test_entry_rewatch = [
      'id' => $this->entry_1_rewatch_id,
      'id_entries' => $this->entry_id_1,
      'uuid' => $this->entry_1_rewatch_uuid,
      'date_rewatched' => $date_finished_rewatch,
    ];

    Entry::insert($test_entries);
    EntryOffquel::insert($test_entry_offquel);
    EntryRating::insert($test_entry_rating);
    EntryRewatch::insert($test_entry_rewatch);
  }

  // Place this in a finally block
  private function setup_restore() {
    // Remove test data
    Entry::truncate();

    // Restore saved entries and relations
    Entry::insert($this->entry_backup);
    EntryOffquel::insert($this->offquel_backup);
    EntryRating::insert($this->rating_backup);
    EntryRewatch::insert($this->rewatch_backup);
  }

  /**
   * Get All & Search Endpoint
   */
  public function test_should_get_all_data() {
    $this->setup_backup();

    try {
      $this->setup_config();

      $response = $this->withoutMiddleware()->get('/api/entries');

      $response->assertStatus(200)
        ->assertJsonCount($this->total_entry_count, 'data')
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
            'results',
            'totalResults',
            'totalPages',
            'hasNext',
          ]
        ]);
    } finally {
      $this->setup_restore();
    }
  }

  public function test_should_get_and_verify_paginated_data() {
    $this->setup_backup();

    try {
      $this->setup_config();

      $test_page = 2;
      $test_limit = 2;
      $response = $this->withoutMiddleware()->get(
        '/api/entries?page=' . $test_page .
          '&limit=' . $test_limit
      );

      $response->assertStatus(200)
        ->assertJsonCount($test_limit, 'data')
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
            'results',
            'totalResults',
            'totalPages',
            'hasNext',
          ]
        ]);

      $actual_meta = $response['meta'];

      $expected_total_pages = ceil($this->total_entry_count / $test_limit);
      $expected_has_next = $test_page <= $expected_total_pages;
      $expected_meta = [
        'page' => $test_page,
        'limit' => $test_limit,
        'results' => $test_limit,
        'totalResults' => $this->total_entry_count,
        'totalPages' => $expected_total_pages,
        'hasNext' => $expected_has_next,
      ];

      $this->assertEqualsCanonicalizing($expected_meta, $actual_meta);
    } finally {
      $this->setup_restore();
    }
  }

  public function test_should_search_all_data_by_title() {
    $this->setup_backup();

    try {
      $this->setup_config();

      $test_needle = 'another solo';
      $response = $this->withoutMiddleware()->get('/api/entries?needle=' . $test_needle);

      $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
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
            'results',
            'totalResults',
            'totalPages',
            'hasNext',
          ]
        ]);

      $actual_data = $response['data'][0];
      $actual_meta = $response['meta'];

      $expected_meta = [
        'page' => 1,
        'limit' => 30,
        'results' => 1,
        'totalResults' => 1,
        'totalPages' => 1,
        'hasNext' => false,
      ];

      $this->assertEqualsCanonicalizing($expected_meta, $actual_meta);
      $this->assertEquals($this->entry_uuid_2, $actual_data['id']);
    } finally {
      $this->setup_restore();
    }
  }

  public function test_should_search_and_verify_paginated_data() {
    $this->setup_backup();

    try {
      $this->setup_config();

      $test_page = 2;
      $test_limit = 1;
      $test_needle = 'series title season';
      $response = $this->withoutMiddleware()->get(
        '/api/entries?page=' . $test_page .
          '&limit=' . $test_limit .
          '&needle=' . $test_needle
      );

      $response->assertStatus(200)
        ->assertJsonCount($test_limit, 'data')
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
            'results',
            'totalResults',
            'totalPages',
            'hasNext',
          ]
        ]);

      $expected_possible_titles = [
        $this->entry_uuid_1,
        $this->entry_uuid_4,
        $this->entry_uuid_5,
      ];

      $actual_data = $response['data'][0];
      $actual_meta = $response['meta'];

      $expected_total_results = count($expected_possible_titles);
      $expected_total_pages = ceil($expected_total_results / $test_limit);
      $expected_has_next = $test_page <= $expected_total_pages;
      $expected_meta = [
        'page' => $test_page,
        'limit' => $test_limit,
        'results' => $test_limit,
        'totalResults' => $expected_total_results,
        'totalPages' => $expected_total_pages,
        'hasNext' => $expected_has_next,
      ];

      $this->assertEqualsCanonicalizing($expected_meta, $actual_meta);
      $this->assertTrue(in_array($actual_data['id'], $expected_possible_titles));
    } finally {
      $this->setup_restore();
    }
  }

  public function test_should_not_get_all_data_when_not_authorized() {
    $response = $this->get('/api/entries');

    $response->assertStatus(401)
      ->assertJson(['message' => 'Unauthorized']);
  }

  /**
   * Get Single Endpoint
   */
  public function test_should_get_single_data() {
    $this->setup_backup();

    try {
      $this->setup_config();

      $response = $this->withoutMiddleware()->get('/api/entries/' . $this->entry_uuid_1);

      $response->assertStatus(200)
        ->assertJsonStructure([
          'data' => [
            'id',
            'quality',
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
            'prequel' => [
              'id',
              'title',
            ],
            'sequelTitle',
            'sequel' => [
              'id',
              'title',
            ],
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
            'idCodecVideo',
            'codecVideo',
            'idCodecAudio',
            'codecAudio',
            'offquels' => [[
              'id',
              'title',
            ]],
            'rewatches' => [[
              'id',
              'dateIso',
              'date',
            ]],
            'ratingAverage',
            'rating' => [
              'audio',
              'enjoyment',
              'graphics',
              'plot',
            ],
            'image',
          ],
        ]);

      $this->assertEquals($this->entry_uuid_1, $response['data']['id']);
    } finally {
      $this->setup_restore();
    }
  }

  public function test_should_not_get_single_data_when_id_is_used_instead_of_uuid() {
    $this->setup_backup();

    try {
      $this->setup_config();

      $response = $this->withoutMiddleware()->get('/api/entries/' . $this->entry_id_1);

      $response->assertStatus(404);
    } finally {
      $this->setup_restore();
    }
  }

  public function test_should_not_get_non_existent_entry() {
    $invalid_id = -1;

    $response = $this->withoutMiddleware()->get('/api/entries/' . $invalid_id);

    $response->assertStatus(404);
  }

  public function test_should_not_get_single_data_when_not_authorized() {

    $response = $this->get('/api/entries/' . $this->entry_uuid_1);

    $response->assertStatus(401)
      ->assertJson(['message' => 'Unauthorized']);
  }

  /**
   * Add Endpoint
   */
  public function test_should_add_data_successfully() {
    $this->setup_backup();

    try {
      Entry::truncate();

      $test_id_quality = Quality::where('quality', 'FHD 1080p')->first()->id;
      $test_title = 'testing newly added title';
      $test_date_finished = '2000-06-15';

      $test_duration = 100;
      $test_filesize = 1000;

      $test_episodes = 12;
      $test_ovas = 34;
      $test_specials = 56;

      $test_encoder_video = 'video encoder';
      $test_encoder_audio = 'audio encoder';
      $test_encoder_subs = 'subs encoder';

      $test_release_year = '2020';
      $test_release_season = 'Spring';

      $test_variants = 'variant';
      $test_remarks = 'remarks';

      $test_id_codec_audio = CodecAudio::first()->id;
      $test_id_codec_video = CodecVideo::first()->id;
      $test_codec_hdr = true;

      $response = $this->withoutMiddleware()
        ->post('/api/entries/', [
          'id_quality' => $test_id_quality,
          'title' => $test_title,
          'date_finished' => $test_date_finished,
          'duration' => $test_duration,
          'filesize' => $test_filesize,
          'episodes' => $test_episodes,
          'ovas' => $test_ovas,
          'specials' => $test_specials,
          'encoder_video' => $test_encoder_video,
          'encoder_audio' => $test_encoder_audio,
          'encoder_subs' => $test_encoder_subs,
          'release_year' => $test_release_year,
          'release_season' => $test_release_season,
          'variants' => $test_variants,
          'remarks' => $test_remarks,
          'id_codec_audio' => $test_id_codec_audio,
          'id_codec_video' => $test_id_codec_video,
          'codec_hdr' => $test_codec_hdr,
        ]);

      $response->assertStatus(200)
        ->assertJson(['message' => 'Success']);

      $actual = Entry::where('title', $test_title)->first();

      $this->assertModelExists($actual);

      $this->assertEquals($test_id_quality, $actual->quality->id);
      $this->assertEquals($test_title, $actual->title);
      $this->assertEquals($test_date_finished, $actual->date_finished);
      $this->assertEquals($test_duration, $actual->duration);

      $this->assertEquals($test_filesize, $actual->filesize);
      $this->assertEquals($test_episodes, $actual->episodes);
      $this->assertEquals($test_ovas, $actual->ovas);

      $this->assertEquals(1, $actual->season_number);
      $this->assertEquals($test_title, $actual->season_first_title->title);

      $this->assertEquals($test_encoder_video, $actual->encoder_video);
      $this->assertEquals($test_encoder_audio, $actual->encoder_audio);
      $this->assertEquals($test_encoder_subs, $actual->encoder_subs);

      $this->assertEquals($test_codec_hdr, $actual->codec_hdr);
      $this->assertEquals($test_id_codec_audio, $actual->id_codec_audio);
      $this->assertEquals($test_id_codec_video, $actual->id_codec_video);

      $this->assertEquals($test_variants, $actual->variants);
      $this->assertEquals($test_remarks, $actual->remarks);

      $this->assertEquals($test_release_season, $actual->release_season);
      $this->assertEquals($test_release_year, $actual->release_year);
    } finally {
      $this->setup_restore();
    }
  }

  public function test_should_add_data_and_set_existing_entry_as_prequel() {
    $this->setup_backup();

    try {
      $this->setup_config();

      $quality = Quality::where('quality', 'FHD 1080p')->first();
      $test_id_quality = $quality->id;
      $test_title = 'testing newly added title';
      $test_date_finished = '2000-06-15';

      $test_prequel_id = Entry::where('uuid', $this->entry_uuid_2)->first()->uuid;
      $test_season_number = 2;

      $response = $this->withoutMiddleware()
        ->post('/api/entries/', [
          'id_quality' => $test_id_quality,
          'title' => $test_title,
          'date_finished' => $test_date_finished,
          'prequel_id' => $test_prequel_id,
          'season_number' => $test_season_number,
          'season_first_title_id' => $test_prequel_id,
        ]);

      $response->assertStatus(200)
        ->assertJson(['message' => 'Success']);

      $actual = Entry::where('title', $test_title)->first();
      $actual_prequel = Entry::where('id', $this->entry_id_2)->first();

      $this->assertModelExists($actual);

      $this->assertEquals($test_id_quality, $actual->quality->id);
      $this->assertEquals($test_title, $actual->title);
      $this->assertEquals($test_date_finished, $actual->date_finished);

      $this->assertEquals($test_season_number, $actual->season_number);
      $this->assertEquals($test_prequel_id, $actual->season_first_title->uuid);
      $this->assertEquals($test_prequel_id, $actual->prequel->uuid);
      $this->assertNull($actual->sequel);

      // Check connection of prequel -> added title
      $this->assertModelExists($actual_prequel);
      $this->assertEquals($actual->uuid, $actual_prequel->sequel->uuid);
    } finally {
      $this->setup_restore();
    }
  }

  public function test_should_add_data_and_set_existing_entry_as_sequel() {
    $this->setup_backup();

    try {
      $this->setup_config();

      $quality = Quality::where('quality', 'FHD 1080p')->first();
      $test_id_quality = $quality->id;
      $test_title = 'testing newly added title';
      $test_date_finished = '2000-06-15';

      $test_sequel_id = Entry::where('uuid', $this->entry_uuid_2)->first()->uuid;

      $response = $this->withoutMiddleware()
        ->post('/api/entries/', [
          'id_quality' => $test_id_quality,
          'title' => $test_title,
          'date_finished' => $test_date_finished,
          'sequel_id' => $test_sequel_id,
        ]);

      $response->assertStatus(200)
        ->assertJson(['message' => 'Success']);

      $actual = Entry::where('title', $test_title)->first();
      $actual_sequel = Entry::where('id', $this->entry_id_2)->first();

      $this->assertModelExists($actual);

      $this->assertEquals($test_id_quality, $actual->quality->id);
      $this->assertEquals($test_title, $actual->title);
      $this->assertEquals($test_date_finished, $actual->date_finished);

      $this->assertEquals(1, $actual->season_number);
      $this->assertEquals($actual->uuid, $actual->season_first_title->uuid);
      $this->assertEquals($test_sequel_id, $actual->sequel->uuid);
      $this->assertNull($actual->prequel);

      // Check connection of added title -> sequel
      $this->assertModelExists($actual_sequel);
      $this->assertEquals($actual->uuid, $actual_sequel->prequel->uuid);
    } finally {
      $this->setup_restore();
    }
  }

  // public function test_should_add_data_and_set_existing_entry_as_offquel() {
  // }

  public function test_should_not_add_data_on_form_errors() {
  }

  public function test_should_not_add_data_when_entry_id_is_used_instead_of_uuid_for_connections() {
  }

  public function test_should_not_add_data_when_any_connection_id_is_non_existent() {
  }

  /**
   * Edit Endpoint
   */
  public function test_should_edit_data_successfully() {
  }

  public function test_should_not_edit_data_when_id_is_used_instead_of_uuid() {
  }

  public function test_should_edit_data_and_set_existing_entry_as_prequel() {
  }

  public function test_should_edit_data_and_set_existing_entry_as_sequel() {
  }

  // public function test_should_edit_data_as_offquel_to_existing_entry() {
  // }

  public function test_should_not_edit_data_on_form_errors() {
  }

  public function test_should_not_edit_data_on_non_existent_entry() {
    $invalid_id = -1;

    $response = $this->withoutMiddleware()->post('/api/entries/' . $invalid_id);

    $response->assertStatus(404);
  }

  public function test_should_not_edit_data_when_entry_id_is_used_instead_of_uuid_for_connections() {
  }

  public function test_should_not_edit_data_when_any_connection_id_is_non_existent() {
  }

  /**
   * Delete Endpoint
   */
  public function test_should_delete_data_successfully() {
    $this->setup_backup();

    try {
      $this->setup_config();

      $response = $this->withoutMiddleware()->delete('/api/entries/' . $this->entry_uuid_1);

      $response->assertStatus(200);

      $actual = Entry::withTrashed()
        ->where('id', $this->entry_id_1)
        ->first();

      $this->assertSoftDeleted($actual);
    } finally {
      $this->setup_restore();
    }
  }

  public function test_should_delete_data_with_prequel_and_remove_sequel_of_prequel_entry() {
    $this->setup_backup();

    try {
      $this->setup_config();

      $prequel_initial = Entry::where('id', $this->entry_id_1)->first();

      $this->assertNotNull($prequel_initial->sequel);

      $response = $this->withoutMiddleware()->delete('/api/entries/' . $this->entry_uuid_4);

      $response->assertStatus(200);

      $actual = Entry::withTrashed()
        ->where('id', $this->entry_id_4)
        ->first();

      $this->assertSoftDeleted($actual);

      $prequel_expected = Entry::where('id', $this->entry_id_1)->first();

      $this->assertNull($prequel_expected->sequel);
    } finally {
      $this->setup_restore();
    }
  }

  public function test_should_delete_data_with_sequel_and_remove_prequel_of_sequel_entry() {
    $this->setup_backup();

    try {
      $this->setup_config();

      $sequel_initial = Entry::where('id', $this->entry_id_5)->first();

      $this->assertNotNull($sequel_initial->prequel);

      $response = $this->withoutMiddleware()->delete('/api/entries/' . $this->entry_uuid_4);

      $response->assertStatus(200);

      $actual = Entry::withTrashed()
        ->where('id', $this->entry_id_4)
        ->first();

      $this->assertSoftDeleted($actual);

      $sequel_expected = Entry::where('id', $this->entry_id_5)->first();

      $this->assertNull($sequel_expected->prequel);
    } finally {
      $this->setup_restore();
    }
  }

  public function test_should_delete_data_with_offquel_and_keep_offquel_entry() {
    $this->setup_backup();

    try {
      $this->setup_config();

      $offquel_initial = Entry::where('id', $this->entry_id_3)->first();

      $this->assertNotNull($offquel_initial);

      $response = $this->withoutMiddleware()->delete('/api/entries/' . $this->entry_uuid_1);

      $response->assertStatus(200);

      $actual = Entry::withTrashed()
        ->where('id', $this->entry_id_1)
        ->first();

      $this->assertSoftDeleted($actual);

      $offquel_expected = Entry::where('id', $this->entry_id_3)->first();

      $this->assertNotNull($offquel_expected);
    } finally {
      $this->setup_restore();
    }
  }

  public function test_should_not_delete_data_when_id_is_used_instead_of_uuid() {
    $this->setup_backup();

    try {
      $this->setup_config();

      $response = $this->withoutMiddleware()->delete('/api/entries/' . $this->entry_id_1);

      $response->assertStatus(404);

      $actual = Entry::withTrashed()
        ->where('id', $this->entry_id_1)
        ->first();

      $this->assertNotSoftDeleted($actual);
    } finally {
      $this->setup_restore();
    }
  }

  public function test_should_not_delete_non_existent_entry() {
    $invalid_id = -1;

    $response = $this->withoutMiddleware()->delete('/api/entries/' . $invalid_id);

    $response->assertStatus(404);
  }

  /**
   * Image Upload Endpoint
   */
  public function test_should_return_a_valid_image_in_getting_single_data() {
    $this->setup_backup();

    try {
      $this->setup_config();

      $response = $this->withoutMiddleware()->get('/api/entries/' . $this->entry_uuid_1);

      $response->assertStatus(200)
        ->assertJsonStructure([
          'data' => [
            'image',
          ],
        ]);

      $image_url = $response['data']['image'];

      $this->assertTrue(Str::isUrl($image_url));

      file_get_contents($image_url);
      $headers = implode("\n", $http_response_header);

      if (preg_match_all("/^content-type\s*:\s*(.*)$/mi", $headers, $matches)) {
        $content_type = end($matches[1]);

        $this->assertTrue(str_contains($content_type, 'image'));
      }
    } finally {
      $this->setup_restore();
    }
  }

  public function test_should_upload_entry_image() {
    $this->setup_backup();

    $cloudinary_image = null;

    try {
      $this->setup_config();

      $file = UploadedFile::fake()->image('test_image.jpg')->size(4096);

      $response = $this->withoutMiddleware()->put('/api/entries/img-upload/' . $this->entry_uuid_2, [
        'image' => $file,
      ]);

      $response->assertStatus(200);

      $actual = Entry::select('image')
        ->where('uuid', $this->entry_uuid_2)
        ->first()
        ->image;

      $this->assertTrue(Str::isUrl($actual));
      $cloudinary_image = pathinfo($actual)['filename'];

      file_get_contents($actual);
      $headers = implode("\n", $http_response_header);

      if (preg_match_all("/^content-type\s*:\s*(.*)$/mi", $headers, $matches)) {
        $content_type = end($matches[1]);

        $this->assertTrue(str_contains($content_type, 'image'));
      }
    } finally {
      // Remove Cloudinary image¡
      if ($cloudinary_image) {
        (new UploadApi())->destroy('entries/' . $cloudinary_image);
        echo "\nINFO: API call to Cloudinary:UploadAPI:destroy\n";
      }

      $this->setup_restore();
    }
  }

  public function test_should_not_upload_entry_image_on_invalid_image() {
    $this->setup_backup();

    try {
      $this->setup_config();

      $file = UploadedFile::fake()->image('test_image.jpg')->size(4097);

      $response = $this->withoutMiddleware()->put('/api/entries/img-upload/' . $this->entry_uuid_2, [
        'image' => $file,
      ]);

      $response->assertStatus(401)
        ->assertJsonStructure(['data' => ['image']]);

      $file = UploadedFile::fake()->image('test_image.webp')->size(4096);

      $response = $this->withoutMiddleware()->put('/api/entries/img-upload/' . $this->entry_uuid_2, [
        'image' => $file,
      ]);

      $response->assertStatus(401)
        ->assertJsonStructure(['data' => ['image']]);
    } finally {
      $this->setup_restore();
    }
  }

  public function test_should_not_upload_entry_image_on_non_existent_entry() {
    $invalid_id = -1;

    $response = $this->withoutMiddleware()->post('/api/entries/img-upload/' . $invalid_id);

    $response->assertStatus(404);
  }

  public function test_should_not_upload_entry_image_when_entry_id_is_used_instead_of_uuid() {
    $response = $this->withoutMiddleware()->post('/api/entries/img-upload/' . $this->entry_id_1);

    $response->assertStatus(404);
  }

  public function test_should_delete_entry_image() {
    $this->setup_backup();

    try {
      $this->setup_config();

      $file = UploadedFile::fake()->image('test_image.jpg')->size(1);

      $upload_response = $this->withoutMiddleware()->put('/api/entries/img-upload/' . $this->entry_uuid_2, [
        'image' => $file,
      ]);

      $upload_response->assertStatus(200);

      $response = $this->withoutMiddleware()->delete('/api/entries/img-upload/' . $this->entry_uuid_2);

      $response->assertStatus(200);

      $actual = Entry::select('image')
        ->where('uuid', $this->entry_uuid_2)
        ->first();

      $this->assertNull($actual->image);
    } finally {
      $this->setup_restore();
    }
  }

  public function test_should_not_delete_image_on_non_existent_entry() {
    $invalid_id = -1;

    $response = $this->withoutMiddleware()->delete('/api/entries/img-upload/' . $invalid_id);

    $response->assertStatus(404);
  }

  public function test_should_not_delete_image_when_entry_id_is_used_instead_of_uuid() {
    $response = $this->withoutMiddleware()->delete('/api/entries/img-upload/' . $this->entry_id_1);

    $response->assertStatus(404);
  }

  /**
   * Ratings Endpoint
   */
  public function test_should_add_or_edit_ratings_to_entry() {
    $this->setup_backup();

    try {
      $this->setup_config();

      $params = [
        'audio' => 1,
        'enjoyment' => 2,
        'graphics' => 3,
        'plot' => 4,
      ];

      $response = $this->withoutMiddleware()
        ->put('/api/entries/ratings/' . $this->entry_uuid_2, $params);

      $response->assertStatus(200);

      $actual = Entry::with('rating')
        ->where('id', $this->entry_id_2)
        ->first()
        ->toArray();

      $this->assertEqualsCanonicalizing($params, $actual['rating']);
    } finally {
      $this->setup_restore();
    }
  }

  public function test_should_add_partial_ratings_to_entry() {
    $this->setup_backup();

    try {
      $this->setup_config();

      $params_audio = ['audio' => 1];

      $response = $this->withoutMiddleware()
        ->put('/api/entries/ratings/' . $this->entry_uuid_2, $params_audio);

      $response->assertStatus(200);

      $actual = Entry::with('rating')
        ->where('id', $this->entry_id_2)
        ->first()
        ->toArray();

      $this->assertEquals($params_audio['audio'], $actual['rating']['audio']);
      $this->assertEquals(null, $actual['rating']['enjoyment']);
      $this->assertEquals(null, $actual['rating']['graphics']);
      $this->assertEquals(null, $actual['rating']['plot']);

      $params_enjoyment = ['enjoyment' => 2];

      $response = $this->withoutMiddleware()
        ->put('/api/entries/ratings/' . $this->entry_uuid_2, $params_enjoyment);

      $response->assertStatus(200);

      $actual = Entry::with('rating')
        ->where('id', $this->entry_id_2)
        ->first()
        ->toArray();

      $this->assertEquals($params_audio['audio'], $actual['rating']['audio']);
      $this->assertEquals($params_enjoyment['enjoyment'], $actual['rating']['enjoyment']);
      $this->assertEquals(null, $actual['rating']['graphics']);
      $this->assertEquals(null, $actual['rating']['plot']);

      $params_graphics = ['graphics' => 3];

      $response = $this->withoutMiddleware()
        ->put('/api/entries/ratings/' . $this->entry_uuid_2, $params_graphics);

      $response->assertStatus(200);

      $actual = Entry::with('rating')
        ->where('id', $this->entry_id_2)
        ->first()
        ->toArray();

      $this->assertEquals($params_audio['audio'], $actual['rating']['audio']);
      $this->assertEquals($params_enjoyment['enjoyment'], $actual['rating']['enjoyment']);
      $this->assertEquals($params_graphics['graphics'], $actual['rating']['graphics']);
      $this->assertEquals(null, $actual['rating']['plot']);

      $params_plot = ['plot' => 4];

      $response = $this->withoutMiddleware()
        ->put('/api/entries/ratings/' . $this->entry_uuid_2, $params_plot);

      $response->assertStatus(200);

      $actual = Entry::with('rating')
        ->where('id', $this->entry_id_2)
        ->first()
        ->toArray();

      $this->assertEquals($params_audio['audio'], $actual['rating']['audio']);
      $this->assertEquals($params_enjoyment['enjoyment'], $actual['rating']['enjoyment']);
      $this->assertEquals($params_graphics['graphics'], $actual['rating']['graphics']);
      $this->assertEquals($params_plot['plot'], $actual['rating']['plot']);
    } finally {
      $this->setup_restore();
    }
  }

  public function test_should_edit_partial_ratings_to_entry() {
    $this->setup_backup();

    try {
      $this->setup_config();

      $existing_ratings = [
        'audio' => $this->entry_1_rating_audio,
        'enjoyment' => $this->entry_1_rating_enjoyment,
        'graphics' => $this->entry_1_rating_graphics,
        'plot' => $this->entry_1_rating_plot,
      ];

      $params_audio = ['audio' => 1];

      $response = $this->withoutMiddleware()
        ->put('/api/entries/ratings/' . $this->entry_uuid_1, $params_audio);

      $response->assertStatus(200);

      $actual = Entry::with('rating')
        ->where('id', $this->entry_id_1)
        ->first()
        ->toArray();

      $this->assertEquals($params_audio['audio'], $actual['rating']['audio']);
      $this->assertEquals($existing_ratings['enjoyment'], $actual['rating']['enjoyment']);
      $this->assertEquals($existing_ratings['graphics'], $actual['rating']['graphics']);
      $this->assertEquals($existing_ratings['plot'], $actual['rating']['plot']);

      $params_enjoyment = ['enjoyment' => 2];

      $response = $this->withoutMiddleware()
        ->put('/api/entries/ratings/' . $this->entry_uuid_1, $params_enjoyment);

      $response->assertStatus(200);

      $actual = Entry::with('rating')
        ->where('id', $this->entry_id_1)
        ->first()
        ->toArray();

      $this->assertEquals($params_audio['audio'], $actual['rating']['audio']);
      $this->assertEquals($params_enjoyment['enjoyment'], $actual['rating']['enjoyment']);
      $this->assertEquals($existing_ratings['graphics'], $actual['rating']['graphics']);
      $this->assertEquals($existing_ratings['plot'], $actual['rating']['plot']);

      $params_graphics = ['graphics' => 3];

      $response = $this->withoutMiddleware()
        ->put('/api/entries/ratings/' . $this->entry_uuid_1, $params_graphics);

      $response->assertStatus(200);

      $actual = Entry::with('rating')
        ->where('id', $this->entry_id_1)
        ->first()
        ->toArray();

      $this->assertEquals($params_audio['audio'], $actual['rating']['audio']);
      $this->assertEquals($params_enjoyment['enjoyment'], $actual['rating']['enjoyment']);
      $this->assertEquals($params_graphics['graphics'], $actual['rating']['graphics']);
      $this->assertEquals($existing_ratings['plot'], $actual['rating']['plot']);

      $params_plot = ['plot' => 4];

      $response = $this->withoutMiddleware()
        ->put('/api/entries/ratings/' . $this->entry_uuid_1, $params_plot);

      $response->assertStatus(200);

      $actual = Entry::with('rating')
        ->where('id', $this->entry_id_1)
        ->first()
        ->toArray();

      $this->assertEquals($params_audio['audio'], $actual['rating']['audio']);
      $this->assertEquals($params_enjoyment['enjoyment'], $actual['rating']['enjoyment']);
      $this->assertEquals($params_graphics['graphics'], $actual['rating']['graphics']);
      $this->assertEquals($params_plot['plot'], $actual['rating']['plot']);
    } finally {
      $this->setup_restore();
    }
  }

  public function test_should_not_add_or_edit_ratings_to_entry_on_form_errors() {
    $this->setup_backup();

    try {
      $this->setup_config();

      $params = [
        'audio' => 11,
        'enjoyment' => 11,
        'graphics' => 11,
        'plot' => 11,
      ];

      $response = $this->withoutMiddleware()
        ->put('/api/entries/ratings/' . $this->entry_uuid_1, $params);

      $response->assertStatus(401)
        ->assertJsonStructure([
          'data' => [
            'audio',
            'enjoyment',
            'graphics',
            'plot',
          ],
        ]);

      $params = [
        'audio' => -1,
        'enjoyment' => -1,
        'graphics' => -1,
        'plot' => -1,
      ];

      $response = $this->withoutMiddleware()
        ->put('/api/entries/ratings/' . $this->entry_uuid_1, $params);

      $response->assertStatus(401)
        ->assertJsonStructure([
          'data' => [
            'audio',
            'enjoyment',
            'graphics',
            'plot',
          ],
        ]);
    } finally {
      $this->setup_restore();
    }
  }

  public function test_should_not_add_or_edit_ratings_to_non_existent_entry() {
    $invalid_id = -1;

    $response = $this->withoutMiddleware()->patch('/api/entries/ratings/' . $invalid_id);

    $response->assertStatus(404);
  }

  public function test_should_not_add_or_edit_ratings_to_entry_when_entry_id_is_used_instead_of_uuid() {
    $response = $this->withoutMiddleware()->patch('/api/entries/ratings/' . $this->entry_id_1);

    $response->assertStatus(404);
  }

  /**
   * Rewatch Endpoint
   */
  public function test_should_add_entry_rewatch() {
    $this->setup_backup();

    try {
      $this->setup_config();

      $params_1 = ['date_rewatched' => '2020-10-20'];

      $response = $this->withoutMiddleware()
        ->post('/api/entries/rewatch/' . $this->entry_uuid_2, $params_1);

      $response->assertStatus(200);

      $actual = Entry::with('rewatches')
        ->where('id', $this->entry_id_2)
        ->first()
        ->rewatches
        ->last()
        ->date_rewatched;

      $this->assertEquals(
        Carbon::parse($params_1['date_rewatched'])->toString(),
        Carbon::parse($actual)->toString(),
      );

      $params_2 = ['date_rewatched' => '2020-11-22'];

      $response = $this->withoutMiddleware()
        ->post('/api/entries/rewatch/' . $this->entry_uuid_2, $params_2);

      $response->assertStatus(200);

      $actual = Entry::with('rewatches')
        ->where('id', $this->entry_id_2)
        ->first()
        ->rewatches
        ->pluck('date_rewatched')
        ->toArray();

      $expected_count = 2;
      $expected_rewatches = [
        $params_1['date_rewatched'],
        $params_2['date_rewatched'],
      ];

      $this->assertCount($expected_count, $actual);
      $this->assertEqualsCanonicalizing($expected_rewatches, $actual);
    } finally {
      $this->setup_restore();
    }
  }

  public function test_should_not_add_entry_rewatch_on_form_errors() {
    $this->setup_backup();

    try {
      $this->setup_config();

      $params = ['date_rewatched' => '3000-01-01'];

      $response = $this->withoutMiddleware()
        ->post('/api/entries/rewatch/' . $this->entry_uuid_2, $params);

      $response->assertStatus(401)
        ->assertJsonStructure(['data' => ['date_rewatched']]);

      $params = ['date_rewatched' => Carbon::now()->addDay()->format('Y-m-d')];

      $response = $this->withoutMiddleware()
        ->post('/api/entries/rewatch/' . $this->entry_uuid_2, $params);

      $response->assertStatus(401)
        ->assertJsonStructure(['data' => ['date_rewatched']]);
    } finally {
      $this->setup_restore();
    }
  }

  public function test_should_not_add_entry_rewatch_on_non_existent_entry() {
    $invalid_id = -1;

    $response = $this->withoutMiddleware()->post('/api/entries/rewatch/' . $invalid_id);

    $response->assertStatus(404);
  }

  public function test_should_not_add_entry_rewatch_when_entry_id_is_used_instead_of_uuid() {
    $response = $this->withoutMiddleware()->post('/api/entries/rewatch/' . $this->entry_id_1);

    $response->assertStatus(404);
  }

  public function test_should_delete_entry_rewatch() {
    $this->setup_backup();

    try {
      $this->setup_config();

      $rewatch_entry_init = Entry::with('rewatches')
        ->where('id', $this->entry_id_1)
        ->first()
        ->rewatches
        ->pluck('date_rewatched')
        ->toArray();

      $rewatch_init = EntryRewatch::where('uuid', $this->entry_1_rewatch_uuid)
        ->first()
        ->toArray();

      $this->assertNotCount(0, $rewatch_entry_init);
      $this->assertNotNull($rewatch_init);

      $response = $this->withoutMiddleware()->delete('/api/entries/rewatch/' . $this->entry_1_rewatch_uuid);

      $response->assertStatus(200);

      $actual_entry_rewatch = Entry::with('rewatches')
        ->where('id', $this->entry_id_2)
        ->first()
        ->rewatches
        ->toArray();

      $actual_rewatch = EntryRewatch::where('uuid', $this->entry_1_rewatch_uuid)
        ->first();

      $this->assertCount(0, $actual_entry_rewatch);
      $this->assertNull($actual_rewatch);
    } finally {
      $this->setup_restore();
    }
  }

  public function test_should_not_not_delete_rewatch_on_non_existent_entry() {
    $invalid_id = -1;

    $response = $this->withoutMiddleware()->delete('/api/entries/rewatch/' . $invalid_id);

    $response->assertStatus(404);
  }

  public function test_should_not_delete_rewatch_when_entry_id_is_used_instead_of_uuid() {
    $response = $this->withoutMiddleware()->delete('/api/entries/rewatch/' . $this->entry_id_1);

    $response->assertStatus(404);
  }

  /**
   * Title Search Endpoint
   */
  public function test_should_return_searched_titles() {
    $this->setup_backup();

    try {
      $this->setup_config();

      $response = $this->withoutMiddleware()->get('/api/entries/titles');

      $response->assertStatus(200)
        ->assertJsonCount($this->total_entry_count, 'data')
        ->assertJsonStructure(['data']);

      $needle = 'another solo';
      $response = $this->withoutMiddleware()->get('/api/entries/titles?needle=' . $needle);

      $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonStructure(['data']);
    } finally {
      $this->setup_restore();
    }
  }

  public function test_should_return_searched_titles_excluding_a_single_title() {
    $this->setup_backup();

    try {
      $this->setup_config();

      $excluded_id = $this->entry_uuid_1;
      $response = $this->withoutMiddleware()->get('/api/entries/titles?id=' . $excluded_id);

      $response->assertStatus(200)
        ->assertJsonCount($this->total_entry_count - 1, 'data')
        ->assertJsonStructure(['data']);

      $this->assertNotContains($excluded_id, $response['data']);
    } finally {
      $this->setup_restore();
    }
  }

  public function test_should_not_return_searched_titles_when_no_authorization() {
    $response = $this->get('/api/entries/titles');

    $response->assertStatus(401)
      ->assertJson(['message' => 'Unauthorized']);
  }

  public function test_should_not_return_searched_titles_when_entry_id_is_used_instead_of_uuid() {
    $excluded_id = $this->entry_id_1;
    $response = $this->withoutMiddleware()->get('/api/entries/titles?id=' . $excluded_id);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['id']]);
  }

  /**
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
      'id_quality' => 3,
      'title' => 'test data --- test-data-part-1',
      'sequel_id' => $expected2['uuid'],
    ];

    // Third data <- Second Data
    $expected_connections3 = [
      'id_quality' => 3,
      'title' => 'test data --- test-data-part-3',
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
   */
}
