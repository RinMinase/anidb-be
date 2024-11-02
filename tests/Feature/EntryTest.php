<?php

namespace Tests\Feature;

use Error;
use Carbon\Carbon;
use Cloudinary\Api\Admin\AdminApi;
use Cloudinary\Api\Upload\UploadApi;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Tests\BaseTestCase;

use App\Enums\IntegerSizesEnum;
use App\Enums\IntegerTypesEnum;

use App\Models\CodecAudio;
use App\Models\CodecVideo;
use App\Models\Entry;
use App\Models\EntryOffquel;
use App\Models\EntryRating;
use App\Models\EntryRewatch;
use App\Models\Log;
use App\Models\Quality;

class EntryTest extends BaseTestCase {

  // Backup related variables
  private $entry_rewatch_backup = null;
  private $entry_rating_backup = null;
  private $entry_offquel_backup = null;
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

  private $entry_title_1 = 'testing series title season 1';

  private $entry_1_image = '__test_data__8fa9b149-0185-41b2-b6c2-7d2ac7512eb4';
  // cached static value throughout the whole test, make single call only to API
  private static $entry_1_image_url = null;

  private $entry_1_rating_audio = 6;
  private $entry_1_rating_enjoyment = 5;
  private $entry_1_rating_graphics = 4;
  private $entry_1_rating_plot = 3;

  private $entry_1_rewatch_id = 99999;
  private $entry_1_rewatch_uuid = 'e16593ad-ed01-4314-b4b1-0120ba734f90';

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
  }

  // Restore related tables
  private function setup_restore() {
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
    Entry::truncate();

    $id_quality = Quality::where('quality', 'FHD 1080p')->first()->id;

    $date_finished_1 = Carbon::parse('2001-01-01')->format('Y-m-d');
    $date_finished_2 = Carbon::parse('2001-01-02')->format('Y-m-d');
    $date_finished_3 = Carbon::parse('2001-01-03')->format('Y-m-d');
    $date_finished_4 = Carbon::parse('2001-01-04')->format('Y-m-d');
    $date_finished_5 = Carbon::parse('2001-01-05')->format('Y-m-d');

    $date_finished_rewatch = Carbon::parse('2001-02-01')->format('Y-m-d');

    if (self::$entry_1_image_url === null) {
      echo PHP_EOL . 'INFO: API call to Cloudinary:AdminAPI:asset' . PHP_EOL;

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
        'title' => $this->entry_title_1,
        'season_number' => 1,
        'prequel_id' => null,
        'sequel_id' => $this->entry_id_4,
        'created_at' => '2020-01-01 13:00:00',
        'updated_at' => '2020-01-01 13:00:00',
        'image' => self::$entry_1_image_url,
      ],
      [
        'id' => $this->entry_id_2,
        'uuid' => $this->entry_uuid_2,
        'id_quality' => $id_quality,
        'date_finished' => $date_finished_2,
        'title' => 'testing another solo title',
        'season_number' => 1,
        'prequel_id' => null,
        'sequel_id' => null,
        'created_at' => '2020-01-01 13:00:00',
        'updated_at' => '2020-01-01 13:00:00',
        'image' => null,
      ],
      [
        'id' => $this->entry_id_3,
        'uuid' => $this->entry_uuid_3,
        'id_quality' => $id_quality,
        'date_finished' => $date_finished_3,
        'title' => 'test offquel',
        'season_number' => 1,
        'prequel_id' => null,
        'sequel_id' => null,
        'created_at' => '2020-01-01 13:00:00',
        'updated_at' => '2020-01-01 13:00:00',
        'image' => null,
      ],
      [
        'id' => $this->entry_id_4,
        'uuid' => $this->entry_uuid_4,
        'id_quality' => $id_quality,
        'date_finished' => $date_finished_4,
        'title' => 'testing series title season 2',
        'season_number' => 2,
        'prequel_id' => $this->entry_id_1,
        'sequel_id' => $this->entry_id_5,
        'created_at' => '2020-01-01 13:00:00',
        'updated_at' => '2020-01-01 13:00:00',
        'image' => null,
      ],
      [
        'id' => $this->entry_id_5,
        'uuid' => $this->entry_uuid_5,
        'id_quality' => $id_quality,
        'date_finished' => $date_finished_5,
        'title' => 'testing series title season 3',
        'season_number' => 3,
        'prequel_id' => $this->entry_id_4,
        'sequel_id' => null,
        'created_at' => '2020-01-01 13:00:00',
        'updated_at' => '2020-01-01 13:00:00',
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
      'created_at' => '2020-01-01 13:00:00',
      'updated_at' => '2020-01-01 13:00:00',
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

  // Fixtures
  public function setUp(): void {
    parent::setUp();
    $this->setup_backup();
  }

  public function tearDown(): void {
    $this->setup_restore();
    parent::tearDown();
  }

  /**
   * Get All & Search Endpoint
   */
  public function test_should_get_all_data() {
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
  }

  public function test_should_get_and_verify_paginated_data() {
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
  }

  public function test_should_search_all_data_by_title() {
    $this->setup_config();

    $test_query = 'another solo';
    $response = $this->withoutMiddleware()->get('/api/entries?query=' . $test_query);

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
  }

  public function test_should_search_and_verify_paginated_data() {
    $this->setup_config();

    $test_page = 2;
    $test_limit = 1;
    $test_query = 'series title season';
    $response = $this->withoutMiddleware()->get(
      '/api/entries?page=' . $test_page .
        '&limit=' . $test_limit .
        '&query=' . $test_query
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
  }

  /**
   * Get Single Endpoint
   */
  public function test_should_get_single_data() {
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
  }

  public function test_should_not_get_single_data_when_id_is_used_instead_of_uuid() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->get('/api/entries/' . $this->entry_id_1);

    $response->assertStatus(404);
  }

  public function test_should_not_get_non_existent_entry() {
    $invalid_id = 'aaaaaaaa-1234-1234-1234-aaaaaaaa1234';

    $response = $this->withoutMiddleware()->get('/api/entries/' . $invalid_id);

    $response->assertStatus(404);

    $invalid_id = -1;

    $response = $this->withoutMiddleware()->get('/api/entries/' . $invalid_id);

    $response->assertStatus(404);
  }

  /**
   * Add Endpoint
   */
  public function test_should_add_data_successfully() {
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
  }

  public function test_should_create_logs_when_adding_data() {
    Entry::truncate();

    $test_id_quality = Quality::where('quality', 'FHD 1080p')->first()->id;
    $test_title = 'testing newly added title';

    $response = $this->withoutMiddleware()
      ->post('/api/entries/', [
        'id_quality' => $test_id_quality,
        'title' => $test_title,
      ]);

    $response->assertStatus(200)
      ->assertJson(['message' => 'Success']);

    $actual = Entry::where('title', $test_title)->first();

    $actual_log = Log::where('id_changed', $actual->uuid)->first();

    $expected_log = [
      'table_changed' => 'entry',
      'description' => null,
      'action' => 'add',
    ];

    $this->assertModelExists($actual_log);

    $this->assertEquals($expected_log['table_changed'], $actual_log->table_changed);
    $this->assertEquals($expected_log['description'], $actual_log->description);
    $this->assertEquals($expected_log['action'], $actual_log->action);
  }

  public function test_should_add_data_and_set_existing_entry_as_prequel() {
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
    $actual_prequel = Entry::where('uuid', $this->entry_uuid_2)->first();

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
  }

  public function test_should_add_data_and_set_existing_entry_as_sequel() {
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
  }

  // public function test_should_add_data_and_set_existing_entry_as_offquel() {
  // }

  public function test_should_not_add_data_on_form_errors() {
    $response = $this->withoutMiddleware()
      ->post('/api/entries/');

    $response->assertStatus(401)
      ->assertJsonStructure([
        'data' => [
          'id_quality',
          'title',
        ],
      ]);

    $test_id_quality = -1;
    $test_title = rand_str(256 + 1);
    $test_date_finished = Carbon::now()->addDay()->addHours(8)->format('Y-m-d');

    $test_duration = -1;
    $test_filesize = -1;

    $test_episodes = -1;
    $test_ovas = -1;
    $test_specials = -1;

    $test_season_number = -1;

    $test_encoder_video = rand_str(128 + 1);
    $test_encoder_audio = rand_str(128 + 1);
    $test_encoder_subs = rand_str(128 + 1);

    $test_release_year = '1899';
    $test_release_season = 'invalid';

    $test_variants = rand_str(256 + 1);
    $test_remarks = rand_str(256 + 1);

    $test_id_codec_audio = -1;
    $test_id_codec_video = -1;
    $test_codec_hdr = 'invalid';

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
        'season_number' => $test_season_number,
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

    $response->assertStatus(401)
      ->assertJsonStructure([
        'data' => [
          'id_quality',
          'title',
          'date_finished',
          'duration',
          'filesize',
          'episodes',
          'ovas',
          'specials',
          'season_number',
          'encoder_video',
          'encoder_audio',
          'encoder_subs',
          'release_year',
          'release_season',
          'variants',
          'remarks',
          'id_codec_audio',
          'id_codec_video',
          'codec_hdr',
        ],
      ]);

    $test_id_quality = 99999;
    $test_valid_title = 'testing newly added title';

    $test_duration = max_int(IntegerTypesEnum::SIGNED, IntegerSizesEnum::MEDIUM) + 1;
    $test_filesize = max_int(IntegerTypesEnum::SIGNED, IntegerSizesEnum::BIG) + 1;

    $test_episodes = max_int(IntegerTypesEnum::SIGNED, IntegerSizesEnum::SMALL) + 1;
    $test_ovas = max_int(IntegerTypesEnum::SIGNED, IntegerSizesEnum::SMALL) + 1;
    $test_specials = max_int(IntegerTypesEnum::SIGNED, IntegerSizesEnum::SMALL) + 1;

    $test_season_number = max_int(IntegerTypesEnum::SIGNED, IntegerSizesEnum::TINY) + 1;

    $test_release_year = '3000';

    $response = $this->withoutMiddleware()
      ->post('/api/entries/', [
        'id_quality' => $test_id_quality,
        'title' => $test_valid_title,
        'date_finished' => $test_date_finished,
        'duration' => $test_duration,
        'filesize' => $test_filesize,
        'episodes' => $test_episodes,
        'ovas' => $test_ovas,
        'specials' => $test_specials,
        'season_number' => $test_season_number,
        'release_year' => $test_release_year,
      ]);

    $response->assertStatus(401)
      ->assertJsonStructure([
        'data' => [
          'id_quality',
          'date_finished',
          'duration',
          'filesize',
          'episodes',
          'ovas',
          'specials',
          'season_number',
          'release_year',
        ],
      ]);
  }

  public function test_should_not_add_data_when_title_is_duplicate() {
    $this->setup_config();

    $test_id_quality = Quality::where('quality', 'FHD 1080p')->first()->id;

    $response = $this->withoutMiddleware()
      ->post('/api/entries/', [
        'id_quality' => $test_id_quality,
        'title' => $this->entry_title_1,
      ]);

    $response->assertStatus(401)
      ->assertJsonStructure([
        'data' => [
          'title',
        ],
      ]);
  }

  public function test_should_not_add_data_when_entry_id_is_used_instead_of_uuid_for_connections() {
    $this->setup_config();

    $test_id_quality = Quality::where('quality', 'FHD 1080p')->first()->id;
    $test_title = 'testing newly added title';

    $test_prequel_id = $this->entry_id_1;
    $test_sequel_id = $this->entry_id_2;
    $test_season_number = 2;

    // TODO: add offquel connection

    $response = $this->withoutMiddleware()
      ->post('/api/entries/', [
        'id_quality' => $test_id_quality,
        'title' => $test_title,
        'prequel_id' => $test_prequel_id,
        'sequel_id' => $test_sequel_id,
        'season_number' => $test_season_number,
        'season_first_title_id' => $test_prequel_id,
      ]);

    $response->assertStatus(401)
      ->assertJsonStructure([
        'data' => [
          'prequel_id',
          'sequel_id',
          'season_first_title_id',
        ],
      ]);
  }

  public function test_should_not_add_data_when_any_connection_id_is_non_existent() {
    $this->setup_config();

    $test_id_quality = Quality::where('quality', 'FHD 1080p')->first()->id;
    $test_title = 'testing newly added title';

    $test_prequel_id = -1;
    $test_sequel_id = -1;
    $test_season_number = 2;

    // TODO: add offquel connection

    $response = $this->withoutMiddleware()
      ->post('/api/entries/', [
        'id_quality' => $test_id_quality,
        'title' => $test_title,
        'prequel_id' => $test_prequel_id,
        'sequel_id' => $test_sequel_id,
        'season_number' => $test_season_number,
        'season_first_title_id' => $test_prequel_id,
      ]);

    $response->assertStatus(401)
      ->assertJsonStructure([
        'data' => [
          'prequel_id',
          'sequel_id',
          'season_first_title_id',
        ],
      ]);
  }

  /**
   * Edit Endpoint
   */
  public function test_should_edit_data_successfully() {
    $this->setup_config();

    $test_id_quality = Quality::where('quality', 'FHD 1080p')->first()->id;
    $test_title = 'testing newly edited title';
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
      ->put('/api/entries/' . $this->entry_uuid_1, [
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

    $actual = Entry::where('uuid', $this->entry_uuid_1)->first();

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
  }

  public function test_should_create_logs_when_editing_data() {
    $this->setup_config();

    $test_id_quality = Quality::where('quality', 'FHD 1080p')->first()->id;
    $test_title = 'testing newly edited title';

    $response = $this->withoutMiddleware()
      ->put('/api/entries/' . $this->entry_uuid_1, [
        'id_quality' => $test_id_quality,
        'title' => $test_title,
      ]);

    $response->assertStatus(200)
      ->assertJson(['message' => 'Success']);

    $actual_log = Log::where('id_changed', $this->entry_uuid_1)->first();

    $expected_log = [
      'table_changed' => 'entry',
      'description' => null,
      'action' => 'edit',
    ];

    $this->assertModelExists($actual_log);

    $this->assertEquals($expected_log['table_changed'], $actual_log->table_changed);
    $this->assertEquals($expected_log['description'], $actual_log->description);
    $this->assertEquals($expected_log['action'], $actual_log->action);
  }

  public function test_should_edit_data_and_set_existing_entry_as_prequel() {
    $this->setup_config();

    $quality = Quality::where('quality', 'FHD 1080p')->first();
    $test_id_quality = $quality->id;
    $test_title = 'testing newly added title';
    $test_date_finished = '2000-06-15';

    $test_prequel_id = Entry::where('uuid', $this->entry_uuid_2)->first()->uuid;
    $test_season_number = 2;

    $response = $this->withoutMiddleware()
      ->put('/api/entries/' . $this->entry_uuid_1, [
        'id_quality' => $test_id_quality,
        'title' => $test_title,
        'date_finished' => $test_date_finished,
        'prequel_id' => $test_prequel_id,
        'season_number' => $test_season_number,
        'season_first_title_id' => $test_prequel_id,
      ]);

    $response->assertStatus(200)
      ->assertJson(['message' => 'Success']);

    $actual = Entry::where('uuid', $this->entry_uuid_1)->first();
    $actual_prequel = Entry::where('uuid', $this->entry_uuid_2)->first();

    $this->assertModelExists($actual);

    $this->assertEquals($test_id_quality, $actual->quality->id);
    $this->assertEquals($test_title, $actual->title);
    $this->assertEquals($test_date_finished, $actual->date_finished);

    $this->assertEquals($test_season_number, $actual->season_number);
    $this->assertEquals($test_prequel_id, $actual->season_first_title->uuid);
    $this->assertEquals($test_prequel_id, $actual->prequel->uuid);

    // Check connection of prequel -> added title
    $this->assertModelExists($actual_prequel);
    $this->assertEquals($actual->uuid, $actual_prequel->sequel->uuid);
  }

  public function test_should_edit_data_and_set_existing_entry_as_sequel() {
    $this->setup_config();

    $quality = Quality::where('quality', 'FHD 1080p')->first();
    $test_id_quality = $quality->id;
    $test_title = 'testing newly added title';
    $test_date_finished = '2000-06-15';

    $test_sequel_id = Entry::where('uuid', $this->entry_uuid_2)->first()->uuid;

    $response = $this->withoutMiddleware()
      ->put('/api/entries/' . $this->entry_uuid_1, [
        'id_quality' => $test_id_quality,
        'title' => $test_title,
        'date_finished' => $test_date_finished,
        'sequel_id' => $test_sequel_id,
      ]);

    $response->assertStatus(200)
      ->assertJson(['message' => 'Success']);

    $actual = Entry::where('uuid', $this->entry_uuid_1)->first();
    $actual_sequel = Entry::where('uuid', $this->entry_uuid_2)->first();

    $this->assertModelExists($actual);

    $this->assertEquals($test_id_quality, $actual->quality->id);
    $this->assertEquals($test_title, $actual->title);
    $this->assertEquals($test_date_finished, $actual->date_finished);

    $this->assertEquals(1, $actual->season_number);
    $this->assertEquals($actual->uuid, $actual->season_first_title->uuid);
    $this->assertEquals($test_sequel_id, $actual->sequel->uuid);

    // Check connection of added title -> sequel
    $this->assertModelExists($actual_sequel);
    $this->assertEquals($actual->uuid, $actual_sequel->prequel->uuid);
  }

  // public function test_should_edit_data_as_offquel_to_existing_entry() {
  // }

  public function test_should_not_edit_data_on_form_errors() {
    $this->setup_config();

    $response = $this->withoutMiddleware()
      ->put('/api/entries/' . $this->entry_uuid_1);

    $response->assertStatus(401)
      ->assertJsonStructure([
        'data' => [
          'id_quality',
          'title',
        ],
      ]);

    $test_id_quality = -1;
    $test_title = rand_str(256 + 1);
    $test_date_finished = Carbon::now()->addDay()->addHours(8)->format('Y-m-d');

    $test_duration = -1;
    $test_filesize = -1;

    $test_episodes = -1;
    $test_ovas = -1;
    $test_specials = -1;

    $test_season_number = -1;

    $test_encoder_video = rand_str(128 + 1);
    $test_encoder_audio = rand_str(128 + 1);
    $test_encoder_subs = rand_str(128 + 1);

    $test_release_year = '1899';
    $test_release_season = 'invalid';

    $test_variants = rand_str(256 + 1);
    $test_remarks = rand_str(256 + 1);

    $test_id_codec_audio = -1;
    $test_id_codec_video = -1;
    $test_codec_hdr = 'invalid';

    $response = $this->withoutMiddleware()
      ->put('/api/entries/' . $this->entry_uuid_1, [
        'id_quality' => $test_id_quality,
        'title' => $test_title,
        'date_finished' => $test_date_finished,
        'duration' => $test_duration,
        'filesize' => $test_filesize,
        'episodes' => $test_episodes,
        'ovas' => $test_ovas,
        'specials' => $test_specials,
        'season_number' => $test_season_number,
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

    $response->assertStatus(401)
      ->assertJsonStructure([
        'data' => [
          'id_quality',
          'title',
          'date_finished',
          'duration',
          'filesize',
          'episodes',
          'ovas',
          'specials',
          'season_number',
          'encoder_video',
          'encoder_audio',
          'encoder_subs',
          'release_year',
          'release_season',
          'variants',
          'remarks',
          'id_codec_audio',
          'id_codec_video',
          'codec_hdr',
        ],
      ]);

    $test_id_quality = 99999;
    $test_valid_title = 'testing newly added title';

    $test_duration = max_int(IntegerTypesEnum::SIGNED, IntegerSizesEnum::MEDIUM) + 1;
    $test_filesize = max_int(IntegerTypesEnum::SIGNED, IntegerSizesEnum::BIG) + 1;

    $test_episodes = max_int(IntegerTypesEnum::SIGNED, IntegerSizesEnum::SMALL) + 1;
    $test_ovas = max_int(IntegerTypesEnum::SIGNED, IntegerSizesEnum::SMALL) + 1;
    $test_specials = max_int(IntegerTypesEnum::SIGNED, IntegerSizesEnum::SMALL) + 1;

    $test_season_number = max_int(IntegerTypesEnum::SIGNED, IntegerSizesEnum::TINY) + 1;

    $test_release_year = '3000';

    $response = $this->withoutMiddleware()
      ->put('/api/entries/' . $this->entry_uuid_1, [
        'id_quality' => $test_id_quality,
        'title' => $test_valid_title,
        'date_finished' => $test_date_finished,
        'duration' => $test_duration,
        'filesize' => $test_filesize,
        'episodes' => $test_episodes,
        'ovas' => $test_ovas,
        'specials' => $test_specials,
        'season_number' => $test_season_number,
        'release_year' => $test_release_year,
      ]);

    $response->assertStatus(401)
      ->assertJsonStructure([
        'data' => [
          'id_quality',
          'date_finished',
          'duration',
          'filesize',
          'episodes',
          'ovas',
          'specials',
          'season_number',
          'release_year',
        ],
      ]);
  }

  public function test_should_not_edit_data_when_id_is_used_instead_of_uuid() {
    $this->setup_config();

    $response = $this->withoutMiddleware()
      ->put('/api/entries/' . $this->entry_id_1);

    $response->assertStatus(404);
  }

  public function test_should_not_edit_data_on_non_existent_entry() {
    $invalid_id = 'aaaaaaaa-1234-1234-1234-aaaaaaaa1234';

    $response = $this->withoutMiddleware()->put('/api/entries/' . $invalid_id);

    $response->assertStatus(404);

    $invalid_id = -1;

    $response = $this->withoutMiddleware()->put('/api/entries/' . $invalid_id);

    $response->assertStatus(404);
  }

  public function test_should_not_edit_data_when_entry_id_is_used_instead_of_uuid_for_connections() {
    $this->setup_config();

    $test_prequel_id = $this->entry_id_1;
    $test_sequel_id = $this->entry_id_3;
    $test_season_number = 2;

    // TODO: add offquel connection

    $response = $this->withoutMiddleware()
      ->put('/api/entries/' . $this->entry_uuid_2, [
        'prequel_id' => $test_prequel_id,
        'sequel_id' => $test_sequel_id,
        'season_number' => $test_season_number,
        'season_first_title_id' => $test_prequel_id,
      ]);

    $response->assertStatus(401)
      ->assertJsonStructure([
        'data' => [
          'prequel_id',
          'sequel_id',
          'season_first_title_id',
        ],
      ]);
  }

  public function test_should_not_edit_data_when_any_connection_id_is_non_existent() {
    $this->setup_config();

    $test_prequel_id = -1;
    $test_sequel_id = -1;
    $test_season_number = 2;

    // TODO: add offquel connection

    $response = $this->withoutMiddleware()
      ->put('/api/entries/' . $this->entry_uuid_2, [
        'prequel_id' => $test_prequel_id,
        'sequel_id' => $test_sequel_id,
        'season_number' => $test_season_number,
        'season_first_title_id' => $test_prequel_id,
      ]);

    $response->assertStatus(401)
      ->assertJsonStructure([
        'data' => [
          'prequel_id',
          'sequel_id',
          'season_first_title_id',
        ],
      ]);
  }

  /**
   * Delete Endpoint
   */
  public function test_should_delete_data_successfully() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->delete('/api/entries/' . $this->entry_uuid_1);

    $response->assertStatus(200);

    $actual = Entry::withTrashed()
      ->where('id', $this->entry_id_1)
      ->first();

    $this->assertSoftDeleted($actual);
  }

  public function test_should_delete_data_with_prequel_and_remove_sequel_of_prequel_entry() {
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
  }

  public function test_should_delete_data_with_sequel_and_remove_prequel_of_sequel_entry() {
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
  }

  public function test_should_delete_data_with_offquel_and_keep_offquel_entry() {
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
  }

  public function test_should_not_delete_data_when_id_is_used_instead_of_uuid() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->delete('/api/entries/' . $this->entry_id_1);

    $response->assertStatus(404);

    $actual = Entry::withTrashed()
      ->where('id', $this->entry_id_1)
      ->first();

    $this->assertNotSoftDeleted($actual);
  }

  public function test_should_not_delete_non_existent_entry() {
    $invalid_id = 'aaaaaaaa-1234-1234-1234-aaaaaaaa1234';

    $response = $this->withoutMiddleware()->delete('/api/entries/' . $invalid_id);

    $response->assertStatus(404);

    $invalid_id = -1;

    $response = $this->withoutMiddleware()->delete('/api/entries/' . $invalid_id);

    $response->assertStatus(404);
  }

  /**
   * Image Upload Endpoint
   */
  public function test_should_return_a_valid_image_in_getting_single_data() {
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
  }

  public function test_should_upload_entry_image() {
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
        echo PHP_EOL . 'INFO: API call to Cloudinary:UploadAPI:destroy' . PHP_EOL;
      }
    }
  }

  public function test_should_not_upload_entry_image_on_invalid_image() {
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
  }

  public function test_should_not_upload_entry_image_on_non_existent_entry() {
    $invalid_id = 'aaaaaaaa-1234-1234-1234-aaaaaaaa1234';

    $response = $this->withoutMiddleware()->put('/api/entries/img-upload/' . $invalid_id);

    $response->assertStatus(404);

    $invalid_id = -1;

    $response = $this->withoutMiddleware()->put('/api/entries/img-upload/' . $invalid_id);

    $response->assertStatus(404);
  }

  public function test_should_not_upload_entry_image_when_entry_id_is_used_instead_of_uuid() {
    $response = $this->withoutMiddleware()->put('/api/entries/img-upload/' . $this->entry_id_1);

    $response->assertStatus(404);
  }

  public function test_should_delete_entry_image() {
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
  }

  public function test_should_not_delete_image_on_non_existent_entry() {
    $invalid_id = 'aaaaaaaa-1234-1234-1234-aaaaaaaa1234';

    $response = $this->withoutMiddleware()->delete('/api/entries/img-upload/' . $invalid_id);

    $response->assertStatus(404);

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
  }

  public function test_should_add_partial_ratings_to_entry() {
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
  }

  public function test_should_edit_partial_ratings_to_entry() {
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
  }

  public function test_should_not_add_or_edit_ratings_to_entry_on_form_errors() {
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
  }

  public function test_should_not_add_or_edit_ratings_to_non_existent_entry() {
    $invalid_id = 'aaaaaaaa-1234-1234-1234-aaaaaaaa1234';

    $response = $this->withoutMiddleware()->put('/api/entries/ratings/' . $invalid_id);

    $response->assertStatus(404);

    $invalid_id = -1;

    $response = $this->withoutMiddleware()->put('/api/entries/ratings/' . $invalid_id);

    $response->assertStatus(404);
  }

  public function test_should_not_add_or_edit_ratings_to_entry_when_entry_id_is_used_instead_of_uuid() {
    $response = $this->withoutMiddleware()->put('/api/entries/ratings/' . $this->entry_id_1);

    $response->assertStatus(404);
  }

  /**
   * Rewatch Endpoint
   */
  public function test_should_add_entry_rewatch() {
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
  }

  public function test_should_not_add_entry_rewatch_on_form_errors() {
    $this->setup_config();

    $params = ['date_rewatched' => '3000-01-01'];

    $response = $this->withoutMiddleware()
      ->post('/api/entries/rewatch/' . $this->entry_uuid_2, $params);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['date_rewatched']]);

    $params = ['date_rewatched' => Carbon::now()->addDay()->addHours(8)->format('Y-m-d')];

    $response = $this->withoutMiddleware()
      ->post('/api/entries/rewatch/' . $this->entry_uuid_2, $params);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['date_rewatched']]);
  }

  public function test_should_not_add_entry_rewatch_on_non_existent_entry() {
    $invalid_id = 'aaaaaaaa-1234-1234-1234-aaaaaaaa1234';

    $response = $this->withoutMiddleware()->post('/api/entries/rewatch/' . $invalid_id);

    $response->assertStatus(404);

    $invalid_id = -1;

    $response = $this->withoutMiddleware()->post('/api/entries/rewatch/' . $invalid_id);

    $response->assertStatus(404);
  }

  public function test_should_not_add_entry_rewatch_when_entry_id_is_used_instead_of_uuid() {
    $response = $this->withoutMiddleware()->post('/api/entries/rewatch/' . $this->entry_id_1);

    $response->assertStatus(404);
  }

  public function test_should_delete_entry_rewatch() {
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
  }

  public function test_should_not_not_delete_rewatch_on_non_existent_entry() {
    $invalid_id = 'aaaaaaaa-1234-1234-1234-aaaaaaaa1234';

    $response = $this->withoutMiddleware()->delete('/api/entries/rewatch/' . $invalid_id);

    $response->assertStatus(404);

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
    $this->setup_config();

    $response = $this->withoutMiddleware()->get('/api/entries/titles');

    $response->assertStatus(200)
      ->assertJsonCount($this->total_entry_count, 'data')
      ->assertJsonStructure(['data']);

    $needle = 'another solo';
    $response = $this->withoutMiddleware()->get('/api/entries/titles?needle=' . $needle);

    $response->assertStatus(200)
      ->assertJsonCount(1, 'data')
      ->assertJsonStructure(['data' => [['id', 'title']]]);
  }

  public function test_should_return_searched_titles_inluding_a_single_title() {
    $this->setup_config();

    $included_id = $this->entry_uuid_1;
    $response = $this->withoutMiddleware()->get('/api/entries/titles?id=' . $included_id);

    $response->assertStatus(200)
      ->assertJsonCount($this->total_entry_count, 'data')
      ->assertJsonStructure(['data' => [['id', 'title']]]);

    $actual = current(
      array_filter(
        $response['data'],
        fn($item) => $item['id'] === $included_id
      )
    );

    $this->assertNotNull($actual);
  }

  public function test_should_not_return_searched_titles_when_entry_id_is_used_instead_of_uuid() {
    $excluded_id = $this->entry_id_1;

    $response = $this->withoutMiddleware()->get('/api/entries/titles?id=' . $excluded_id);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['id']]);
  }
}
