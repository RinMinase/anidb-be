<?php

namespace Tests\Feature;

use Exception;
use Carbon\Carbon;
use Tests\BaseTestCase;

use App\Enums\EntrySearchHasEnum;
use App\Exceptions\Entry\SearchFilterParsingException;

use App\Models\CodecAudio;
use App\Models\CodecVideo;
use App\Models\Entry;
use App\Models\EntryOffquel;
use App\Models\EntryRating;
use App\Models\EntryRewatch;
use App\Models\Quality;

use App\Repositories\EntrySearchRepository;

class EntrySearchTest extends BaseTestCase {

  // Backup related variables
  private $entry_rewatch_backup = null;
  private $entry_rating_backup = null;
  private $entry_offquel_backup = null;
  private $entry_backup = null;

  // Class variables
  private $entry_id_1 = 99999;
  private $entry_id_2 = 99998;
  private $entry_id_3 = 99997;
  private $entry_id_4 = 99996;
  private $entry_id_5 = 99995;

  private $entry_uuid_1 = 'b354c456-fb16-4809-b4bb-e55f8c9ec900';
  private $entry_uuid_2 = 'a787f460-bc60-44cf-9224-3901fb5b08ca';
  private $entry_uuid_3 = '959d90bd-f1ed-4078-b374-4fd4dfedfbb6';
  private $entry_uuid_4 = '64b3e54c-8280-4275-b5c2-5361065a5bf9';
  private $entry_uuid_5 = 'a9493744-8b97-458e-9152-f561eddf67bc';

  private $entry_title_1 = 'testing title 1';
  private $entry_title_2 = 'testing title 2';
  private $entry_title_3 = 'testing title 3';
  private $entry_title_4 = 'testing title 4';
  private $entry_title_5 = 'testing title 5';

  private $entry_date_finished_1 = '2001-01-01';
  private $entry_date_finished_2 = '2001-10-01';
  private $entry_date_finished_3 = '2002-01-01';
  private $entry_date_finished_4 = '2005-01-01';
  private $entry_date_finished_5 = '2005-02-01';

  // Average of 4.5
  private $entry_1_rating_audio = 6;
  private $entry_1_rating_enjoyment = 5;
  private $entry_1_rating_graphics = 4;
  private $entry_1_rating_plot = 3;

  // Average of 2.0
  private $entry_2_rating_audio = 2;
  private $entry_2_rating_enjoyment = 2;
  private $entry_2_rating_graphics = 2;
  private $entry_2_rating_plot = 2;

  private $entry_1_rewatch_id = 99999;
  private $entry_2_rewatch_id = 99998;

  private $entry_1_rewatch_uuid = 'e16593ad-ed01-4314-b4b1-0120ba734f90';
  private $entry_2_rewatch_uuid = '1c8cb214-a87b-4d91-8ba0-4d956612e1e1';

  private $entry_date_rewatch_1 = '2001-03-01';
  private $entry_date_rewatch_2 = '2001-05-01';

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

    $id_quality_2160 = Quality::where('quality', '4K 2160p')->first()->id;
    $id_quality_1080 = Quality::where('quality', 'FHD 1080p')->first()->id;
    $id_quality_720 = Quality::where('quality', 'HD 720p')->first()->id;

    $id_codec_video_1 = CodecVideo::where('codec', 'x264 8bit')->first()->id;
    $id_codec_video_2 = CodecVideo::where('codec', 'x264 10bit')->first()->id;
    $id_codec_video_3 = CodecVideo::where('codec', 'x265 10bit')->first()->id;

    $id_codec_audio_1 = CodecAudio::where('codec', 'FLAC 2.0')->first()->id;
    $id_codec_audio_2 = CodecAudio::where('codec', 'DTS-HD MA 2.0')->first()->id;
    $id_codec_audio_3 = CodecAudio::where('codec', 'TrueHD 2.0')->first()->id;

    $test_entries = [
      [
        'id' => $this->entry_id_1,
        'uuid' => $this->entry_uuid_1,
        'id_quality' => $id_quality_2160,
        'date_finished' => $this->entry_date_finished_1,
        'title' => $this->entry_title_1,
        'filesize' => 3_221_225_472,
        'episodes' => 10,
        'ovas' => 20,
        'specials' => 30,
        'encoder_video' => 'AlphaVideo',
        'encoder_audio' => 'AlphaAudio',
        'encoder_subs' => 'AlphaSubs',
        'codec_hdr' => true,
        'id_codec_video' => $id_codec_video_1,
        'id_codec_audio' => $id_codec_audio_1,
        'release_year' => 2020,
        'release_season' => 'Spring',
        'remarks' => 'sample remarks',
        'variants' => 'variant sample',
        'created_at' => '2020-01-01 13:00:00',
        'updated_at' => '2020-01-01 13:00:00',
        'image' => 'https://example.com',
      ],
      [
        'id' => $this->entry_id_2,
        'uuid' => $this->entry_uuid_2,
        'id_quality' => $id_quality_1080,
        'date_finished' => $this->entry_date_finished_2,
        'title' => $this->entry_title_2,
        'filesize' => 2_147_483_648,
        'episodes' => 5,
        'ovas' => 5,
        'specials' => 5,
        'encoder_video' => 'BetaVideo',
        'encoder_audio' => 'BetaAudio',
        'encoder_subs' => 'BetaSubs',
        'codec_hdr' => false,
        'id_codec_video' => $id_codec_video_2,
        'id_codec_audio' => $id_codec_audio_2,
        'release_year' => 2020,
        'release_season' => 'Spring',
        'remarks' => null,
        'variants' => 'alternate',
        'created_at' => '2020-01-01 13:00:00',
        'updated_at' => '2020-01-01 13:00:00',
        'image' => null,
      ],
      [
        'id' => $this->entry_id_3,
        'uuid' => $this->entry_uuid_3,
        'id_quality' => $id_quality_720,
        'date_finished' => $this->entry_date_finished_3,
        'title' => $this->entry_title_3,
        'filesize' => 1_073_741_824,
        'episodes' => 0,
        'ovas' => 0,
        'specials' => 0,
        'encoder_video' => null,
        'encoder_audio' => null,
        'encoder_subs' => null,
        'codec_hdr' => false,
        'id_codec_video' => $id_codec_video_3,
        'id_codec_audio' => $id_codec_audio_3,
        'release_year' => 2021,
        'release_season' => 'Winter',
        'remarks' => null,
        'variants' => 'test value',
        'created_at' => '2020-01-01 13:00:00',
        'updated_at' => '2020-01-01 13:00:00',
        'image' => null,
      ],
      [
        'id' => $this->entry_id_4,
        'uuid' => $this->entry_uuid_4,
        'id_quality' => $id_quality_720,
        'date_finished' => $this->entry_date_finished_4,
        'title' => $this->entry_title_4,
        'filesize' => 0,
        'episodes' => 0,
        'ovas' => 0,
        'specials' => 0,
        'encoder_video' => null,
        'encoder_audio' => null,
        'encoder_subs' => null,
        'codec_hdr' => false,
        'id_codec_video' => $id_codec_video_3,
        'id_codec_audio' => $id_codec_audio_3,
        'release_year' => 2021,
        'release_season' => 'Fall',
        'remarks' => null,
        'variants' => 'another value',
        'created_at' => '2020-01-01 13:00:00',
        'updated_at' => '2020-01-01 13:00:00',
        'image' => null,
      ],
      [
        'id' => $this->entry_id_5,
        'uuid' => $this->entry_uuid_5,
        'id_quality' => $id_quality_720,
        'date_finished' => $this->entry_date_finished_5,
        'title' => $this->entry_title_5,
        'filesize' => 0,
        'episodes' => 0,
        'ovas' => 0,
        'specials' => 0,
        'encoder_video' => null,
        'encoder_audio' => null,
        'encoder_subs' => null,
        'codec_hdr' => false,
        'id_codec_video' => $id_codec_video_3,
        'id_codec_audio' => $id_codec_audio_3,
        'release_year' => 2022,
        'release_season' => 'Spring',
        'remarks' => null,
        'variants' => null,
        'created_at' => '2020-01-01 13:00:00',
        'updated_at' => '2020-01-01 13:00:00',
        'image' => null,
      ],
    ];

    $test_entry_rating = [[
      'id_entries' => $this->entry_id_1,
      'audio' => $this->entry_1_rating_audio,
      'enjoyment' => $this->entry_1_rating_enjoyment,
      'graphics' => $this->entry_1_rating_graphics,
      'plot' => $this->entry_1_rating_plot,
      'created_at' => '2020-01-01 13:00:00',
      'updated_at' => '2020-01-01 13:00:00',
    ], [
      'id_entries' => $this->entry_id_2,
      'audio' => $this->entry_2_rating_audio,
      'enjoyment' => $this->entry_2_rating_enjoyment,
      'graphics' => $this->entry_2_rating_graphics,
      'plot' => $this->entry_2_rating_plot,
      'created_at' => '2020-01-01 13:00:00',
      'updated_at' => '2020-01-01 13:00:00',
    ]];

    $test_entry_rewatch = [[
      'id' => $this->entry_1_rewatch_id,
      'id_entries' => $this->entry_id_1,
      'uuid' => $this->entry_1_rewatch_uuid,
      'date_rewatched' => $this->entry_date_rewatch_1,
    ], [
      'id' => $this->entry_2_rewatch_id,
      'id_entries' => $this->entry_id_1,
      'uuid' => $this->entry_2_rewatch_uuid,
      'date_rewatched' => $this->entry_date_rewatch_2,
    ]];

    Entry::insert($test_entries);
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

  // Test Cases
  public function test_should_search_all_data_successfully() {
    $this->setup_config();

    $id_codec_video_1 = CodecVideo::where('codec', 'x264 8bit')->first()->id;
    $id_codec_audio_1 = CodecAudio::where('codec', 'FLAC 2.0')->first()->id;

    $test_params = [
      'quality' => '2160p, 1080p',
      'title' => $this->entry_title_1,
      'date' => 'from ' . $this->entry_date_finished_1 . ' to ' . $this->entry_date_rewatch_2,
      'filesize' => '>= 3 GB',
      'episodes' => '>= 10',
      'ovas' => '20',
      'specials' => '<= 30',
      'encoder' => 'Alpha',
      'is_hdr' => 'yes',
      'codec_audio' => $id_codec_audio_1,
      'codec_video' => $id_codec_video_1,
      'release' => 'Spring 2020',
      'has_remarks' => 'yes',
      'remarks' => 'sample',
      'has_image' => 'yes',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(1, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_1, $actual_ids);
  }

  public function test_should_search_quality_data_successfully() {
    $this->setup_config();

    $test_params = [
      'quality' => '2160p, 1080p',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(2, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_1, $actual_ids);
    $this->assertContains($this->entry_uuid_2, $actual_ids);

    $test_params = [
      'quality' => '>= 1080p',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(2, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_1, $actual_ids);
    $this->assertContains($this->entry_uuid_2, $actual_ids);

    $test_params = [
      'quality' => '< 1080p',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(3, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_3, $actual_ids);
    $this->assertContains($this->entry_uuid_4, $actual_ids);
    $this->assertContains($this->entry_uuid_5, $actual_ids);
  }

  public function test_should_search_title_data_successfully() {
    $this->setup_config();

    $test_params = [
      'quality' => '>= 1080p',
      'title' => $this->entry_title_1,
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(1, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_1, $actual_ids);

    $test_params = [
      'title' => 'testing title',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(5, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_1, $actual_ids);
    $this->assertContains($this->entry_uuid_2, $actual_ids);
    $this->assertContains($this->entry_uuid_3, $actual_ids);
    $this->assertContains($this->entry_uuid_4, $actual_ids);
    $this->assertContains($this->entry_uuid_5, $actual_ids);
  }

  public function test_should_search_variant_data_successfully() {
    $this->setup_config();

    $test_params = [
      'title' => 'variant',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(1, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_1, $actual_ids);

    $test_params = [
      'title' => 'alternate',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(1, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_2, $actual_ids);
  }

  public function test_should_search_episode_data_successfully() {
    $this->setup_config();

    $test_params = [
      'quality' => '>= 1080p',
      'title' => $this->entry_title_1,
      'episodes' => '>= 10',
      'ovas' => '>= 20',
      'specials' => '>= 30',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(1, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_1, $actual_ids);

    $test_params = [
      'episodes' => '10',
      'ovas' => '20',
      'specials' => '30',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(1, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_1, $actual_ids);

    $test_params = [
      'episodes' => '< 10',
      'ovas' => '< 20',
      'specials' => '< 30',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(4, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_2, $actual_ids);
    $this->assertContains($this->entry_uuid_3, $actual_ids);
    $this->assertContains($this->entry_uuid_4, $actual_ids);
    $this->assertContains($this->entry_uuid_5, $actual_ids);

    $test_params = [
      'episodes' => '5 to 10',
      'ovas' => 'from 5 to 20',
      'specials' => 'from 5 to 30',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(2, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_1, $actual_ids);
    $this->assertContains($this->entry_uuid_2, $actual_ids);
  }

  public function test_should_search_general_encoder_data_successfully() {
    $this->setup_config();

    $test_params = [
      'encoder' => 'Video',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(2, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_1, $actual_ids);
    $this->assertContains($this->entry_uuid_2, $actual_ids);

    $test_params = [
      'encoder' => 'Alpha',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(1, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_1, $actual_ids);

    $test_params = [
      'encoder' => 'BetaAudio',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(1, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_2, $actual_ids);
  }

  public function test_should_search_specific_encoder_data_successfully() {
    $this->setup_config();

    $test_params = [
      'encoder_video' => 'Video',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(2, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_1, $actual_ids);
    $this->assertContains($this->entry_uuid_2, $actual_ids);

    $test_params = [
      'encoder_video' => 'Video',
      'encoder_audio' => 'BetaAudio',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(1, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_2, $actual_ids);

    $test_params = [
      'encoder_video' => 'Video',
      'encoder_subs' => 'AlphaSubs',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(1, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_1, $actual_ids);
  }

  public function test_should_search_image_flag_data_successfully() {
    $this->setup_config();

    $test_params = [
      'quality' => '>= 1080p',
      'has_image' => 'yes',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(1, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_1, $actual_ids);

    $test_params = [
      'has_image' => 'no',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(4, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_2, $actual_ids);
    $this->assertContains($this->entry_uuid_3, $actual_ids);
    $this->assertContains($this->entry_uuid_4, $actual_ids);
    $this->assertContains($this->entry_uuid_5, $actual_ids);

    $test_params = [
      'has_image' => 'any',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(5, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_1, $actual_ids);
    $this->assertContains($this->entry_uuid_2, $actual_ids);
    $this->assertContains($this->entry_uuid_3, $actual_ids);
    $this->assertContains($this->entry_uuid_4, $actual_ids);
    $this->assertContains($this->entry_uuid_5, $actual_ids);
  }

  public function test_should_search_hdr_flag_data_successfully() {
    $this->setup_config();

    $test_params = [
      'quality' => '>= 1080p',
      'is_hdr' => 'yes',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(1, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_1, $actual_ids);

    $test_params = [
      'is_hdr' => 'no',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(4, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_2, $actual_ids);
    $this->assertContains($this->entry_uuid_3, $actual_ids);
    $this->assertContains($this->entry_uuid_4, $actual_ids);
    $this->assertContains($this->entry_uuid_5, $actual_ids);

    $test_params = [
      'is_hdr' => 'any',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(5, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_1, $actual_ids);
    $this->assertContains($this->entry_uuid_2, $actual_ids);
    $this->assertContains($this->entry_uuid_3, $actual_ids);
    $this->assertContains($this->entry_uuid_4, $actual_ids);
    $this->assertContains($this->entry_uuid_5, $actual_ids);
  }

  public function test_should_search_codec_data_successfully() {
    $this->setup_config();

    $id_codec_video_1 = CodecVideo::where('codec', 'x264 8bit')->first()->id;
    $id_codec_video_2 = CodecVideo::where('codec', 'x264 10bit')->first()->id;

    $test_params = [
      'codec_video' => $id_codec_video_1 . ',' . $id_codec_video_2,
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(2, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_1, $actual_ids);
    $this->assertContains($this->entry_uuid_2, $actual_ids);

    $id_codec_audio_1 = CodecAudio::where('codec', 'FLAC 2.0')->first()->id;
    $id_codec_audio_2 = CodecAudio::where('codec', 'DTS-HD MA 2.0')->first()->id;

    $test_params = [
      'codec_audio' => $id_codec_audio_1 . ',' . $id_codec_audio_2,
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(2, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_1, $actual_ids);
    $this->assertContains($this->entry_uuid_2, $actual_ids);

    $id_codec_video_3 = CodecVideo::where('codec', 'x265 10bit')->first()->id;
    $id_codec_audio_3 = CodecAudio::where('codec', 'TrueHD 2.0')->first()->id;

    $test_params = [
      'codec_video' => $id_codec_video_3,
      'codec_audio' => $id_codec_audio_3,
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(3, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_3, $actual_ids);
    $this->assertContains($this->entry_uuid_4, $actual_ids);
    $this->assertContains($this->entry_uuid_5, $actual_ids);
  }

  public function test_should_search_remarks_flag_data_successfully() {
    $this->setup_config();

    $test_params = [
      'has_remarks' => 'yes',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(1, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_1, $actual_ids);

    $test_params = [
      'has_remarks' => 'no',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(4, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_2, $actual_ids);
    $this->assertContains($this->entry_uuid_3, $actual_ids);
    $this->assertContains($this->entry_uuid_4, $actual_ids);
    $this->assertContains($this->entry_uuid_5, $actual_ids);

    $test_params = [
      'has_remarks' => 'any',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(5, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_1, $actual_ids);
    $this->assertContains($this->entry_uuid_2, $actual_ids);
    $this->assertContains($this->entry_uuid_3, $actual_ids);
    $this->assertContains($this->entry_uuid_4, $actual_ids);
    $this->assertContains($this->entry_uuid_5, $actual_ids);
  }

  public function test_should_search_remarks_data_successfully() {
    $this->setup_config();

    $test_params = [
      'remarks' => 'non existing remarks',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(0, 'data');

    $test_params = [
      'remarks' => 'sample remarks',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(1, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_1, $actual_ids);

    $test_params = [
      'remarks' => 'remarks',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(1, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_1, $actual_ids);
  }

  public function test_should_search_release_data_by_range_successfully() {
    $this->setup_config();

    $test_params = [
      'release' => '2020 spring to 2021 spring',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(3, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_1, $actual_ids);
    $this->assertContains($this->entry_uuid_2, $actual_ids);
    $this->assertContains($this->entry_uuid_3, $actual_ids);

    $test_params = [
      'release' => 'spring 2020 to spring 2021',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(3, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_1, $actual_ids);
    $this->assertContains($this->entry_uuid_2, $actual_ids);
    $this->assertContains($this->entry_uuid_3, $actual_ids);

    $test_params = [
      'release' => '2020 to 2021',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(4, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_1, $actual_ids);
    $this->assertContains($this->entry_uuid_2, $actual_ids);
    $this->assertContains($this->entry_uuid_3, $actual_ids);
    $this->assertContains($this->entry_uuid_4, $actual_ids);

    $test_params = [
      'release' => '2021 to 2022',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(3, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_3, $actual_ids);
    $this->assertContains($this->entry_uuid_4, $actual_ids);
    $this->assertContains($this->entry_uuid_5, $actual_ids);
  }

  public function test_should_search_release_data_by_comparators_successfully() {
    $this->setup_config();

    $test_params = [
      'release' => '> 2021 winter',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(2, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_4, $actual_ids);
    $this->assertContains($this->entry_uuid_5, $actual_ids);

    $test_params = [
      'release' => '>= 2021 winter',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(3, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_3, $actual_ids);
    $this->assertContains($this->entry_uuid_4, $actual_ids);
    $this->assertContains($this->entry_uuid_5, $actual_ids);

    $test_params = [
      'release' => '< 2021 winter',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(2, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_1, $actual_ids);
    $this->assertContains($this->entry_uuid_2, $actual_ids);

    $test_params = [
      'release' => '<= 2021',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(4, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_1, $actual_ids);
    $this->assertContains($this->entry_uuid_2, $actual_ids);
    $this->assertContains($this->entry_uuid_3, $actual_ids);
    $this->assertContains($this->entry_uuid_4, $actual_ids);

    $test_params = [
      'release' => 'gte 2022',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(1, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_5, $actual_ids);
  }

  public function test_should_search_release_data_by_absolute_value_successfully() {
    $this->setup_config();

    $test_params = [
      'release' => 'spring 2022',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(1, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_5, $actual_ids);

    $test_params = [
      'release' => '2022',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(1, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_5, $actual_ids);

    $test_params = [
      'release' => '2020',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(2, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_1, $actual_ids);
    $this->assertContains($this->entry_uuid_2, $actual_ids);

    $test_params = [
      'release' => 'spring 2020',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(2, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_1, $actual_ids);
    $this->assertContains($this->entry_uuid_2, $actual_ids);
  }

  public function test_should_search_release_data_by_season_range_successfully() {
    $this->setup_config();

    $test_params = [
      'release' => 'spring to summer',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(3, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_1, $actual_ids);
    $this->assertContains($this->entry_uuid_2, $actual_ids);
    $this->assertContains($this->entry_uuid_5, $actual_ids);

    $test_params = [
      'release' => 'winter to spring',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(4, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_1, $actual_ids);
    $this->assertContains($this->entry_uuid_2, $actual_ids);
    $this->assertContains($this->entry_uuid_3, $actual_ids);
    $this->assertContains($this->entry_uuid_5, $actual_ids);

    $test_params = [
      'release' => 'winter to fall',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(5, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_1, $actual_ids);
    $this->assertContains($this->entry_uuid_2, $actual_ids);
    $this->assertContains($this->entry_uuid_3, $actual_ids);
    $this->assertContains($this->entry_uuid_4, $actual_ids);
    $this->assertContains($this->entry_uuid_5, $actual_ids);
  }

  public function test_should_search_release_data_by_season_absolute_value_successfully() {
    $this->setup_config();

    $test_params = [
      'release' => 'spring',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(3, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_1, $actual_ids);
    $this->assertContains($this->entry_uuid_2, $actual_ids);
    $this->assertContains($this->entry_uuid_5, $actual_ids);

    $test_params = [
      'release' => 'fall',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(1, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_4, $actual_ids);

    $test_params = [
      'release' => 'WINTER',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(1, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_3, $actual_ids);
  }

  public function test_should_search_release_data_by_comma_separated_values_successfully() {
    $this->setup_config();

    $test_params = [
      'release' => '2020, 2021',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(4, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_1, $actual_ids);
    $this->assertContains($this->entry_uuid_2, $actual_ids);
    $this->assertContains($this->entry_uuid_3, $actual_ids);
    $this->assertContains($this->entry_uuid_4, $actual_ids);

    $test_params = [
      'release' => '2020 spring, fall',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(3, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_1, $actual_ids);
    $this->assertContains($this->entry_uuid_2, $actual_ids);
    $this->assertContains($this->entry_uuid_4, $actual_ids);

    $test_params = [
      'release' => 'spring 2020, 2021',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(4, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_1, $actual_ids);
    $this->assertContains($this->entry_uuid_2, $actual_ids);
    $this->assertContains($this->entry_uuid_3, $actual_ids);
    $this->assertContains($this->entry_uuid_4, $actual_ids);
  }

  public function test_should_search_date_data_by_range_successfully() {
    $this->setup_config();

    $test_params = [
      'date' => $this->entry_date_finished_1 . ' to ' . $this->entry_date_finished_3,
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(3, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_1, $actual_ids);
    $this->assertContains($this->entry_uuid_2, $actual_ids);
    $this->assertContains($this->entry_uuid_3, $actual_ids);

    $actual_dates = collect($response['data'])->pluck('dateFinished')->toArray();

    foreach ($actual_dates as $value) {
      $date = Carbon::parse($value);

      $this->assertTrue(
        $date->between($this->entry_date_finished_1, $this->entry_date_finished_3),
        'Error in $value=' . $value,
      );
    }

    $test_params = [
      'date' => '2001-01 to 2001-10',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(2, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_1, $actual_ids);
    $this->assertContains($this->entry_uuid_2, $actual_ids);

    $actual_dates = collect($response['data'])->pluck('dateFinished')->toArray();
    $date_from = Carbon::parse('2001-01-01');
    $date_to = Carbon::parse('2001-10-31');

    foreach ($actual_dates as $value) {
      $date = Carbon::parse($value);

      $this->assertTrue(
        $date->between($date_from, $date_to),
        'Error in $value=' . $value,
      );
    }

    $test_params = [
      'date' => $this->entry_date_rewatch_1 . ' to ' . $this->entry_date_finished_2,
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(2, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_1, $actual_ids);
    $this->assertContains($this->entry_uuid_2, $actual_ids);

    $actual_dates = collect($response['data'])->pluck('dateFinished')->toArray();

    foreach ($actual_dates as $value) {
      $date = Carbon::parse($value);

      $this->assertTrue(
        $date->between($this->entry_date_rewatch_1, $this->entry_date_finished_3),
        'Error in $value=' . $value,
      );
    }

    $entry_rewatch_id = $this->entry_uuid_1;
    $actual_rewatch_date = collect($response['data'])
      ->filter(fn($item) => $item['id'] === $entry_rewatch_id)
      ->first();

    $this->assertEquals(
      Carbon::parse($this->entry_date_rewatch_2)->toString(),
      Carbon::parse($actual_rewatch_date['dateFinished'])->toString(),
    );


    $test_params = [
      'date' => $this->entry_date_rewatch_2 . ' to ' . $this->entry_date_finished_2,
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(2, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_1, $actual_ids);
    $this->assertContains($this->entry_uuid_2, $actual_ids);

    $actual_dates = collect($response['data'])->pluck('dateFinished')->toArray();

    foreach ($actual_dates as $value) {
      $date = Carbon::parse($value);

      $this->assertTrue(
        $date->between($this->entry_date_rewatch_2, $this->entry_date_finished_3),
        'Error in $value=' . $value,
      );
    }

    $entry_rewatch_id = $this->entry_uuid_1;
    $actual_rewatch_date = collect($response['data'])
      ->filter(fn($item) => $item['id'] === $entry_rewatch_id)
      ->first();

    $this->assertEquals(
      Carbon::parse($this->entry_date_rewatch_2)->toString(),
      Carbon::parse($actual_rewatch_date['dateFinished'])->toString(),
    );
  }

  public function test_should_search_date_data_by_comparators_successfully() {
    $this->setup_config();

    $test_params = [
      'date' => '> ' . $this->entry_date_finished_4,
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(1, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_5, $actual_ids);

    $actual_dates = collect($response['data'])->pluck('dateFinished')->toArray();
    $date_target = Carbon::parse($this->entry_date_finished_4);

    foreach ($actual_dates as $value) {
      $date = Carbon::parse($value);
      $this->assertTrue($date->gt($date_target), 'Error in $value=' . $value);
    }

    $test_params = [
      'date' => 'gte ' . $this->entry_date_finished_4,
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(2, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_4, $actual_ids);
    $this->assertContains($this->entry_uuid_5, $actual_ids);

    $actual_dates = collect($response['data'])->pluck('dateFinished')->toArray();
    $date_target = Carbon::parse($this->entry_date_finished_4);

    foreach ($actual_dates as $value) {
      $date = Carbon::parse($value);
      $this->assertTrue($date->gte($date_target), 'Error in $value=' . $value);
    }

    $test_params = [
      'date' => 'less than ' . $this->entry_date_rewatch_2,
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(1, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_1, $actual_ids);

    $actual_dates = collect($response['data'])->pluck('dateFinished')->toArray();
    $date_target = Carbon::parse($this->entry_date_rewatch_2);

    foreach ($actual_dates as $value) {
      $date = Carbon::parse($value);
      $this->assertTrue($date->lt($date_target), 'Error in $value=' . $value);
    }

    $actual_date = $response['data'][0]['dateFinished'];
    $expected_date = $this->entry_date_rewatch_1;
    $this->assertEquals(
      Carbon::parse($expected_date)->toString(),
      Carbon::parse($actual_date)->toString(),
    );

    $test_params = [
      'date' => '<= ' . $this->entry_date_rewatch_2,
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(1, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_1, $actual_ids);

    $actual_dates = collect($response['data'])->pluck('dateFinished')->toArray();
    $date_target = Carbon::parse($this->entry_date_rewatch_2);

    foreach ($actual_dates as $value) {
      $date = Carbon::parse($value);
      $this->assertTrue($date->lte($date_target), 'Error in $value=' . $value);
    }

    $actual_date = $response['data'][0]['dateFinished'];
    $expected_date = $this->entry_date_rewatch_2;
    $this->assertEquals(
      Carbon::parse($expected_date)->toString(),
      Carbon::parse($actual_date)->toString(),
    );
  }

  public function test_should_search_filesize_data_by_range_successfully() {
    $this->setup_config();

    $test_params = [
      'filesize' => '2 GB to 3 GB',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(2, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_1, $actual_ids);
    $this->assertContains($this->entry_uuid_2, $actual_ids);

    $test_params = [
      'filesize' => '1 GB to 2 GB',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(2, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_2, $actual_ids);
    $this->assertContains($this->entry_uuid_3, $actual_ids);
  }

  public function test_should_search_filesize_data_by_comparators_successfully() {
    $this->setup_config();

    $test_params = [
      'filesize' => '> 2 GB',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(1, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_1, $actual_ids);

    $test_params = [
      'filesize' => 'gte 2 GB',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(2, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_1, $actual_ids);
    $this->assertContains($this->entry_uuid_2, $actual_ids);

    $test_params = [
      'filesize' => 'less than 2 GB',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(3, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_3, $actual_ids);
    $this->assertContains($this->entry_uuid_4, $actual_ids);
    $this->assertContains($this->entry_uuid_5, $actual_ids);
  }

  public function test_should_search_rating_data_by_range_successfully() {
    $this->setup_config();

    $test_params = [
      'rating' => '2 to 4',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(1, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_2, $actual_ids);

    $test_params = [
      'rating' => 'from 2 to 4.5',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(2, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_1, $actual_ids);
    $this->assertContains($this->entry_uuid_2, $actual_ids);

    $test_params = [
      'rating' => 'from 2 to 5',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(2, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_1, $actual_ids);
    $this->assertContains($this->entry_uuid_2, $actual_ids);
  }

  public function test_should_search_rating_data_by_comparators_successfully() {
    $this->setup_config();

    $test_params = [
      'rating' => 'gt 2',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(1, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_1, $actual_ids);

    $test_params = [
      'rating' => 'greater than or equal 2',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(2, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_1, $actual_ids);
    $this->assertContains($this->entry_uuid_2, $actual_ids);
  }

  public function test_should_search_rating_data_by_absolute_value_successfully() {
    $this->setup_config();

    $test_params = [
      'rating' => '2',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(1, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_2, $actual_ids);

    $test_params = [
      'rating' => '4.5',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(1, 'data');

    $actual_ids = collect($response['data'])->pluck('id')->toArray();
    $this->assertContains($this->entry_uuid_1, $actual_ids);
  }

  public function test_should_not_search_all_data_when_any_filter_is_invalid() {
    $test_params = [
      'is_hdr' => 'invalid',
      'has_image' => 'invalid',
      'has_remarks' => 'invalid',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(401)
      ->assertJsonStructure([
        'data' => [
          'is_hdr',
          'has_image',
          'has_remarks',
        ],
      ]);

    $test_params = [
      // Valid
      'is_hdr' => 'any',
      'has_image' => 'any',
      'has_remarks' => 'any',

      // Invalid
      'quality' => 'invalid',
      'date' => 'invalid',
      'filesize' => 'invalid',
      'episodes' => 'invalid',
      'ovas' => 'invalid',
      'specials' => 'invalid',
      'release' => 'invalid',
      'codec_video' => 'invalid',
      'codec_audio' => 'invalid',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['quality']]);

    $test_params = [
      // Valid
      'is_hdr' => 'any',
      'has_image' => 'any',
      'has_remarks' => 'any',
      'quality' => '1080p',

      // Invalid
      'date' => 'invalid',
      'filesize' => 'invalid',
      'episodes' => 'invalid',
      'ovas' => 'invalid',
      'specials' => 'invalid',
      'release' => 'invalid',
      'codec_video' => 'invalid',
      'codec_audio' => 'invalid',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['date']]);

    $test_params = [
      // Valid
      'is_hdr' => 'any',
      'has_image' => 'any',
      'has_remarks' => 'any',
      'quality' => '1080p',
      'date' => '> 2020-10-10',

      // Invalid
      'filesize' => 'invalid',
      'episodes' => 'invalid',
      'ovas' => 'invalid',
      'specials' => 'invalid',
      'release' => 'invalid',
      'codec_video' => 'invalid',
      'codec_audio' => 'invalid',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['filesize']]);

    $test_params = [
      // Valid
      'is_hdr' => 'any',
      'has_image' => 'any',
      'has_remarks' => 'any',
      'quality' => '1080p',
      'date' => '> 2020-10-10',
      'filesize' => '> 1 GB',

      // Invalid
      'episodes' => 'invalid',
      'ovas' => 'invalid',
      'specials' => 'invalid',
      'release' => 'invalid',
      'codec_video' => 'invalid',
      'codec_audio' => 'invalid',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['episodes']]);

    $test_params = [
      // Valid
      'is_hdr' => 'any',
      'has_image' => 'any',
      'has_remarks' => 'any',
      'quality' => '1080p',
      'date' => '> 2020-10-10',
      'filesize' => '> 1 GB',
      'episodes' => '1',

      // Invalid
      'ovas' => 'invalid',
      'specials' => 'invalid',
      'release' => 'invalid',
      'codec_video' => 'invalid',
      'codec_audio' => 'invalid',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['ovas']]);

    $test_params = [
      // Valid
      'is_hdr' => 'any',
      'has_image' => 'any',
      'has_remarks' => 'any',
      'quality' => '1080p',
      'date' => '> 2020-10-10',
      'filesize' => '> 1 GB',
      'episodes' => '1',
      'ovas' => '1',

      // Invalid
      'specials' => 'invalid',
      'release' => 'invalid',
      'codec_video' => 'invalid',
      'codec_audio' => 'invalid',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['specials']]);

    $test_params = [
      // Valid
      'is_hdr' => 'any',
      'has_image' => 'any',
      'has_remarks' => 'any',
      'quality' => '1080p',
      'date' => '> 2020-10-10',
      'filesize' => '> 1 GB',
      'episodes' => '1',
      'ovas' => '1',
      'specials' => '1',

      // Invalid
      'release' => 'invalid',
      'codec_video' => 'invalid',
      'codec_audio' => 'invalid',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['release']]);

    $test_params = [
      // Valid
      'is_hdr' => 'any',
      'has_image' => 'any',
      'has_remarks' => 'any',
      'quality' => '1080p',
      'date' => '> 2020-10-10',
      'filesize' => '> 1 GB',
      'episodes' => '1',
      'ovas' => '1',
      'specials' => '1',
      'release' => 'spring 2020',

      // Invalid
      'codec_video' => 'invalid',
      'codec_audio' => 'invalid',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['codec_video']]);

    $id_codec_video = CodecVideo::where('codec', 'x264 8bit')->first()->id;

    $test_params = [
      // Valid
      'is_hdr' => 'any',
      'has_image' => 'any',
      'has_remarks' => 'any',
      'quality' => '1080p',
      'date' => '> 2020-10-10',
      'filesize' => '> 1 GB',
      'episodes' => '1',
      'ovas' => '1',
      'specials' => '1',
      'release' => 'spring 2020',
      'codec_video' => $id_codec_video,

      // Invalid
      'codec_audio' => 'invalid',
    ];

    $response = $this->withoutMiddleware()->get('/api/entries/search?' . http_build_query($test_params));

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['codec_audio']]);
  }

  // Entry Search Functions
  public function test_should_parse_quality_value_with_multiple_values() {
    $expected = ['4K 2160p', 'FHD 1080p', 'HD 720p', 'HQ 480p', 'LQ 360p'];

    $values = [
      '4k, 1080p, 720p, 480p, 360p',
      '4k, uhd, 1080p, fhd, 720p, 480p, 360p',
      '4K, 1080P, 720P, 480P, 360P',
      '4K, 1080P, 720P, hd, 480P, 360P',
      '2160p, 1080p, 720p, 480p, 360p',
      '2160P, 1080P, 720P, 480P, 360P',
      '2160P, 1080P, 720P, 480P, hq, 360P',
      '2160, 1080, 720, 480, 360',
      'uhd, fhd, hd, hq, lq',
      'uhd,fhd,hd,hq,lq',
    ];

    foreach ($values as $key => $value) {
      $actual = EntrySearchRepository::search_parse_quality($value);
      $this->assertEquals($expected, $actual, 'Error in $key=' . $key);
    }
  }

  public function test_should_parse_quality_value_with_absolute_value() {
    $expected = ['4K 2160p'];
    $values = ['4K', '4k', 'UHD', 'uhd', '2160P', '2160p', '2160'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_quality($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = ['FHD 1080p'];
    $values = ['FHD', 'fhd', '1080P', '1080p', '1080'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_quality($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = ['HD 720p'];
    $values = ['HD', 'hd', '720P', '720p', '720'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_quality($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = ['HQ 480p'];
    $values = ['HQ', 'hq', '480P', '480p', '480'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_quality($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = ['LQ 360p'];
    $values = ['LQ', 'lq', '360P', '360p', '360'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_quality($value);
      $this->assertEquals($expected, $actual);
    }
  }

  public function test_should_parse_quality_value_with_comparators() {
    $expected = ['4K 2160p'];
    $values = [
      '>= uhd',
      '>= 4k',
      'gte uhd',
      'gte 4k',
      'greater than equal uhd',
      'greater than equal 4k',
      'greater than or equal uhd',
      'greater than or equal 4k',
    ];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_quality($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = ['4K 2160p', 'FHD 1080p', 'HD 720p', 'HQ 480p', 'LQ 360p'];
    $values = ['<= uhd', 'lte uhd', 'less than equal uhd', 'less than or equal uhd'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_quality($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = ['FHD 1080p', 'HD 720p', 'HQ 480p', 'LQ 360p'];
    $values = ['< uhd', 'lt uhd', 'less than uhd'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_quality($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = ['4K 2160p'];
    $values = ['> fhd', 'gt fhd', 'greater than fhd'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_quality($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = ['4K 2160p', 'FHD 1080p'];
    $values = ['>= fhd', 'gte fhd', 'greater than equal fhd', 'greater than or equal fhd'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_quality($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = ['FHD 1080p', 'HD 720p', 'HQ 480p', 'LQ 360p'];
    $values = ['<= fhd', 'lte fhd', 'less than equal fhd', 'less than or equal fhd'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_quality($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = ['HD 720p', 'HQ 480p', 'LQ 360p'];
    $values = ['< fhd', 'lt fhd', 'less than fhd'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_quality($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = ['4K 2160p', 'FHD 1080p'];
    $values = ['> hd', 'gt hd', 'greater than hd'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_quality($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = ['4K 2160p', 'FHD 1080p', 'HD 720p'];
    $values = ['>= hd', 'gte hd', 'greater than equal hd', 'greater than or equal hd'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_quality($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = ['HD 720p', 'HQ 480p', 'LQ 360p'];
    $values = ['<= hd', 'lte hd', 'less than equal hd', 'less than or equal hd'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_quality($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = ['HQ 480p', 'LQ 360p'];
    $values = ['< hd', 'lt hd', 'less than hd'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_quality($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = ['4K 2160p', 'FHD 1080p', 'HD 720p'];
    $values = ['> hq', 'gt hq', 'greater than hq'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_quality($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = ['4K 2160p', 'FHD 1080p', 'HD 720p', 'HQ 480p'];
    $values = ['>= hq', 'gte hq', 'greater than equal hq', 'greater than or equal hq'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_quality($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = ['HQ 480p', 'LQ 360p'];
    $values = ['<= hq', 'lte hq', 'less than equal hq', 'less than or equal hq'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_quality($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = ['LQ 360p'];
    $values = ['< hq', 'lt hq', 'less than hq'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_quality($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = ['4K 2160p', 'FHD 1080p', 'HD 720p', 'HQ 480p'];
    $values = ['> lq', 'gt lq', 'greater than lq'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_quality($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = ['4K 2160p', 'FHD 1080p', 'HD 720p', 'HQ 480p', 'LQ 360p'];
    $values = ['>= lq', 'gte lq', 'greater than equal lq', 'greater than or equal lq'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_quality($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = ['LQ 360p'];
    $values = ['<= lq', 'lte lq', 'less than equal lq', 'less than or equal lq'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_quality($value);
      $this->assertEquals($expected, $actual);
    }
  }

  public function test_should_return_valid_filters_when_parsing_partial_invalid_quality() {
    $expected = ['FHD 1080p', 'HD 720p', 'HQ 480p', 'LQ 360p'];
    $value = 'invalid, fhd, hd, hq, lq';
    $actual = EntrySearchRepository::search_parse_quality($value);
    $this->assertEquals($expected, $actual);

    $expected = ['4K 2160p', 'HD 720p', 'HQ 480p', 'LQ 360p'];
    $value = 'uhd, invalid, hd, hq, lq';
    $actual = EntrySearchRepository::search_parse_quality($value);
    $this->assertEquals($expected, $actual);

    $expected = ['4K 2160p', 'FHD 1080p', 'HQ 480p', 'LQ 360p'];
    $value = 'uhd, fhd, invalid, hq, lq';
    $actual = EntrySearchRepository::search_parse_quality($value);
    $this->assertEquals($expected, $actual);

    $expected = ['4K 2160p', 'FHD 1080p', 'HD 720p', 'LQ 360p'];
    $value = 'uhd, fhd, hd, invalid, lq';
    $actual = EntrySearchRepository::search_parse_quality($value);
    $this->assertEquals($expected, $actual);

    $expected = ['4K 2160p', 'FHD 1080p', 'HD 720p', 'HQ 480p'];
    $value = 'uhd, fhd, hd, hq, invalid';
    $actual = EntrySearchRepository::search_parse_quality($value);
    $this->assertEquals($expected, $actual);
  }

  public function test_should_return_null_on_parsing_empty_quality() {
    $value = '';
    $actual = EntrySearchRepository::search_parse_quality($value);
    $this->assertNull($actual);

    $value = null;
    $actual = EntrySearchRepository::search_parse_quality($value);
    $this->assertNull($actual);
  }

  public function test_should_throw_error_on_parsing_completely_invalid_quality() {
    $value = 'greater than uhd';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_quality($value),
      Exception::class
    );

    $value = 'greater than 4k';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_quality($value),
      Exception::class
    );

    $value = 'less than 360p';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_quality($value),
      Exception::class
    );

    $value = '< lq';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_quality($value),
      Exception::class
    );

    $value = 'greater than invalid';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_quality($value),
      Exception::class
    );

    $value = '> invalid';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_quality($value),
      SearchFilterParsingException::class
    );

    $value = 'invalid value';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_quality($value),
      SearchFilterParsingException::class
    );

    $value = 'invalid, value';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_quality($value),
      SearchFilterParsingException::class
    );

    $value = 'invalid,value';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_quality($value),
      SearchFilterParsingException::class
    );
  }

  public function test_should_parse_date_value_with_range() {
    $expected = [
      'date_from' => '2020-10-12',
      'date_to' => '2020-11-12',
      'comparator' => null,
    ];

    $values = [
      'from 2020-10-12 to 2020-11-12',
      'from 12-10-2020 to 12-11-2020',
      'from 10/12/2020 to 11/12/2020',
      'from oct 12 2020 to nov 12 2020',
      'from Oct 12 2020 to Nov 12 2020',
      'from Oct 12, 2020 to Nov 12, 2020',
      'from october 12 2020 to november 12 2020',
      'from October 12 2020 to November 12 2020',
      'from October 12, 2020 to November 12, 2020',
      '2020-10-12 to 2020-11-12',
      '12-10-2020 to 12-11-2020',
      '10/12/2020 to 11/12/2020',
      'oct 12 2020 to nov 12 2020',
      'Oct 12 2020 to Nov 12 2020',
      'Oct 12, 2020 to Nov 12, 2020',
      'october 12 2020 to november 12 2020',
      'October 12 2020 to November 12 2020',
      'October 12, 2020 to November 12, 2020',
    ];

    foreach ($values as $key => $value) {
      $actual = EntrySearchRepository::search_parse_date($value);
      $this->assertEquals($expected, $actual, 'Error on $key = ' . $key);
    }

    $expected = [
      'date_from' => '2020-01-01',
      'date_to' => '2022-12-31',
      'comparator' => null,
    ];

    $value = '2020 to 2022';
    $actual = EntrySearchRepository::search_parse_date($value);
    $this->assertEquals($expected, $actual);

    $value = 'from 2020 to 2022';
    $actual = EntrySearchRepository::search_parse_date($value);
    $this->assertEquals($expected, $actual);

    $expected = [
      'date_from' => '2020-03-01',
      'date_to' => '2020-06-30',
      'comparator' => null,
    ];

    $values = [
      '2020-03 to 2020-06',
      '2020-3 to 2020-6',
      'from 2020-03 to 2020-06',
      '03-2020 to 06-2020',
      '3-2020 to 6-2020',
      'from 03-2020 to 06-2020',
      '2020/03 to 2020/06',
      '2020/3 to 2020/6',
      'from 2020/03 to 2020/06',
      '03/2020 to 06/2020',
      '3/2020 to 6/2020',
      'from 03/2020 to 06/2020',
      'from 2020/03 to 2020/06',
      'Mar 2020 to Jun 2020',
      '2020 mar to 2020 jun',
      '2020 Mar to 2020 Jun',
      'from Mar 2020 to Jun 2020',
      'from 2020 Mar to 2020 Jun',
      'March 2020 to June 2020',
      'from March 2020 to June 2020',
    ];

    foreach ($values as $key => $value) {
      $actual = EntrySearchRepository::search_parse_date($value);
      $this->assertEquals($expected, $actual, 'Error on $key = ' . $key);
    }
  }

  public function test_should_parse_date_value_with_semirange_absolute_value() {
    $expected = [
      'date_from' => '2020-01-01',
      'date_to' => '2020-12-31',
      'comparator' => null,
    ];

    $value = '2020';
    $actual = EntrySearchRepository::search_parse_date($value);
    $this->assertEquals($expected, $actual);

    $expected = [
      'date_from' => '2021-01-01',
      'date_to' => '2021-12-31',
      'comparator' => null,
    ];

    $value = '2021';
    $actual = EntrySearchRepository::search_parse_date($value);
    $this->assertEquals($expected, $actual);

    $expected = [
      'date_from' => '2021-01-01',
      'date_to' => '2021-01-31',
      'comparator' => null,
    ];

    $values = [
      '2021-1',
      '2021-01',
      '1-2021',
      '01-2021',
      '2021/1',
      '2021/01',
      '1/2021',
      '01/2021',
      'jan 2021',
      'Jan 2021',
      'JAN 2021',
      'January 2021',
      '2021 jan',
      '2021 Jan',
      '2021 JAN',
      '2021 January',
    ];

    foreach ($values as $key => $value) {
      $actual = EntrySearchRepository::search_parse_date($value);
      $this->assertEquals($expected, $actual, 'Error on $key = ' . $key);
    }
  }

  public function test_should_parse_date_value_with_absolute_value() {
    $expected = [
      'date_from' => '2020-10-12',
      'date_to' => null,
      'comparator' => null,
    ];

    $values = [
      '2020-10-12',
      '12-10-2020',
      '10/12/2020',
      'oct 12 2020',
      'Oct 12 2020',
      'Oct 12, 2020',
      'october 12 2020',
      'October 12 2020',
      'October 12, 2020',
    ];

    foreach ($values as $key => $value) {
      $actual = EntrySearchRepository::search_parse_date($value);
      $this->assertEquals($expected, $actual, 'Error on $key = ' . $key);
    }
  }

  public function test_should_parse_date_value_with_comparators() {
    $expected = [
      'date_from' => '2020-10-12',
      'date_to' => null,
      'comparator' => '>',
    ];

    $values = [
      '> 2020-10-12',
      'gt 2020-10-12',
      'greater than 2020-10-12',
      '> oct 12 2020',
      'gt oct 12 2020',
      'greater than oct 12 2020',
      '> oct 12, 2020',
      'gt oct 12, 2020',
      'greater than oct 12, 2020',
    ];

    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_date($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = [
      'date_from' => '2020-10-12',
      'date_to' => null,
      'comparator' => '>=',
    ];

    $values = [
      '>= 2020-10-12',
      'gte 2020-10-12',
      'greater than equal 2020-10-12',
      'greater than or equal 2020-10-12',
      '>= oct 12 2020',
      'gte oct 12 2020',
      'greater than equal oct 12 2020',
      'greater than or equal oct 12 2020',
      '>= oct 12, 2020',
      'gte oct 12, 2020',
      'greater than equal oct 12, 2020',
      'greater than or equal oct 12, 2020',
    ];

    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_date($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = [
      'date_from' => '2020-10-12',
      'date_to' => null,
      'comparator' => '<',
    ];

    $values = [
      '< 2020-10-12',
      'lt 2020-10-12',
      'less than 2020-10-12',
      '< oct 12 2020',
      'lt oct 12 2020',
      'less than oct 12 2020',
      '< oct 12, 2020',
      'lt oct 12, 2020',
      'less than oct 12, 2020',
    ];

    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_date($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = [
      'date_from' => '2020-10-12',
      'date_to' => null,
      'comparator' => '<=',
    ];

    $values = [
      '<= 2020-10-12',
      'lte 2020-10-12',
      'less than equal 2020-10-12',
      'less than or equal 2020-10-12',
      '<= oct 12 2020',
      'lte oct 12 2020',
      'less than equal oct 12 2020',
      'less than or equal oct 12 2020',
      '<= oct 12, 2020',
      'lte oct 12, 2020',
      'less than equal oct 12, 2020',
      'less than or equal oct 12, 2020',
    ];

    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_date($value);
      $this->assertEquals($expected, $actual);
    }
  }

  public function test_should_return_null_on_parsing_empty_date() {
    $value = '';
    $actual = EntrySearchRepository::search_parse_date($value);
    $this->assertNull($actual);

    $value = null;
    $actual = EntrySearchRepository::search_parse_date($value);
    $this->assertNull($actual);
  }

  public function test_should_throw_error_on_parsing_invalid_date() {
    $value = '2020-11-20 to 2020-10-21';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_date($value),
      SearchFilterParsingException::class
    );

    $value = '<> invalid';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_date($value),
      SearchFilterParsingException::class
    );

    $value = '< > invalid';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_date($value),
      SearchFilterParsingException::class
    );

    $value = '> jan 40 3000';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_date($value),
      SearchFilterParsingException::class
    );

    $value = '> < jan 40 3000';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_date($value),
      SearchFilterParsingException::class
    );
  }

  public function test_should_parse_filesize_value_with_range() {
    $expected = [
      'filesize_from' => 3_145_728,
      'filesize_to' => 3_221_225_472,
      'comparator' => null,
    ];

    $value = 'from 3145728 to 3221225472';
    $actual = EntrySearchRepository::search_parse_filesize($value);
    $this->assertEquals($expected, $actual);

    $value = 'from 3 MB to 3 GB';
    $actual = EntrySearchRepository::search_parse_filesize($value);
    $this->assertEquals($expected, $actual);

    $value = 'from 3MB to 3GB';
    $actual = EntrySearchRepository::search_parse_filesize($value);
    $this->assertEquals($expected, $actual);

    $expected = [
      'filesize_from' => 3,
      'filesize_to' => 10_995_116_277_760,
      'comparator' => null,
    ];

    $value = 'from 3 to 10995116277760';
    $actual = EntrySearchRepository::search_parse_filesize($value);
    $this->assertEquals($expected, $actual);

    $value = 'from 3 to 10TB';
    $actual = EntrySearchRepository::search_parse_filesize($value);
    $this->assertEquals($expected, $actual);
  }

  public function test_should_parse_filesize_value_correctly() {
    $expected = [
      'filesize_from' => 3,
      'filesize_to' => null,
      'comparator' => '>',
    ];

    $values = ['> 3'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_filesize($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = [
      'filesize_from' => 3_072,
      'filesize_to' => null,
      'comparator' => '>',
    ];

    $values = ['> 3072', '> 3KB', '> 3 KB', '> 3kb', '> 3 kb'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_filesize($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = [
      'filesize_from' => 3_145_728,
      'filesize_to' => null,
      'comparator' => '>',
    ];

    $values = ['> 3145728', '> 3MB', '> 3 MB', '> 3mb', '> 3 mb'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_filesize($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = [
      'filesize_from' => 3_221_225_472,
      'filesize_to' => null,
      'comparator' => '>',
    ];

    $values = ['> 3221225472', '> 3GB', '> 3 GB', '> 3gb', '> 3 gb'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_filesize($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = [
      'filesize_from' => 3_298_534_883_328,
      'filesize_to' => null,
      'comparator' => '>',
    ];

    $values = ['> 3298534883328', '> 3TB', '> 3 TB', '> 3tb', '> 3 tb'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_filesize($value);
      $this->assertEquals($expected, $actual);
    }
  }

  public function test_should_parse_filesize_value_with_comparators() {
    $expected = [
      'filesize_from' => 3_221_225_472,
      'filesize_to' => null,
      'comparator' => '>',
    ];

    $values = ['> 3 gb', 'gt 3 gb', 'greater than 3 gb'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_filesize($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = [
      'filesize_from' => 3_221_225_472,
      'filesize_to' => null,
      'comparator' => '>=',
    ];

    $values = ['>= 3 gb', 'gte 3 gb', 'greater than equal 3 gb', 'greater than or equal 3 gb'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_filesize($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = [
      'filesize_from' => 3_221_225_472,
      'filesize_to' => null,
      'comparator' => '<',
    ];

    $values = ['< 3 gb', 'lt 3 gb', 'less than 3 gb'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_filesize($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = [
      'filesize_from' => 3_221_225_472,
      'filesize_to' => null,
      'comparator' => '<=',
    ];

    $values = ['<= 3 gb', 'lte 3 gb', 'less than equal 3 gb', 'less than or equal 3 gb'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_filesize($value);
      $this->assertEquals($expected, $actual);
    }
  }

  public function test_should_return_null_on_parsing_empty_filesize() {
    $value = '';
    $actual = EntrySearchRepository::search_parse_filesize($value);
    $this->assertNull($actual);

    $value = null;
    $actual = EntrySearchRepository::search_parse_filesize($value);
    $this->assertNull($actual);
  }

  public function test_should_throw_error_on_parsing_invalid_filesize() {
    $value = '6 GB to 3 GB';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_filesize($value),
      SearchFilterParsingException::class
    );

    $value = '5 EB to 6 EB';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_filesize($value),
      SearchFilterParsingException::class
    );

    $value = '>< 6 GB';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_filesize($value),
      SearchFilterParsingException::class
    );

    $value = '> < 6 GB';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_filesize($value),
      SearchFilterParsingException::class
    );
  }

  public function test_should_parse_count_value_with_range() {
    $expected = [
      'count_from' => 3,
      'count_to' => 6,
      'comparator' => null,
    ];

    $value = 'from 3 to 6';
    $actual = EntrySearchRepository::search_parse_count($value, 'test_field');
    $this->assertEquals($expected, $actual);

    $value = '3 to 6';
    $actual = EntrySearchRepository::search_parse_count($value, 'test_field');
    $this->assertEquals($expected, $actual);
  }

  public function test_should_parse_count_value_with_absolute_value() {
    $expected = [
      'count_from' => 3,
      'count_to' => null,
      'comparator' => null,
    ];

    $value = '3';
    $actual = EntrySearchRepository::search_parse_count($value, 'test_field');
    $this->assertEquals($expected, $actual);
  }

  public function test_should_parse_count_value_with_comparators() {
    $expected = [
      'count_from' => 3,
      'count_to' => null,
      'comparator' => '>',
    ];

    $values = ['> 3', 'gt 3', 'greater than 3'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_count($value, 'test_field');
      $this->assertEquals($expected, $actual);
    }

    $expected = [
      'count_from' => 3,
      'count_to' => null,
      'comparator' => '>=',
    ];

    $values = ['>= 3', 'gte 3', 'greater than equal 3', 'greater than or equal 3'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_count($value, 'test_field');
      $this->assertEquals($expected, $actual);
    }

    $expected = [
      'count_from' => 3,
      'count_to' => null,
      'comparator' => '<',
    ];

    $values = ['< 3', 'lt 3', 'less than 3'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_count($value, 'test_field');
      $this->assertEquals($expected, $actual);
    }

    $expected = [
      'count_from' => 3,
      'count_to' => null,
      'comparator' => '<=',
    ];

    $values = ['<= 3', 'lte 3', 'less than equal 3', 'less than or equal 3'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_count($value, 'test_field');
      $this->assertEquals($expected, $actual);
    }
  }

  public function test_should_return_null_on_parsing_empty_count() {
    $value = '';
    $actual = EntrySearchRepository::search_parse_count($value, 'test_field');
    $this->assertNull($actual);

    $value = null;
    $actual = EntrySearchRepository::search_parse_count($value, 'test_field');
    $this->assertNull($actual);
  }

  public function test_should_throw_error_on_parsing_invalid_count_value() {
    $value = '6 to 3';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_count($value, 'test_field'),
      SearchFilterParsingException::class,
    );

    $value = 'invalid';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_count($value, 'test_field'),
      SearchFilterParsingException::class,
    );

    $value = '>< 6';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_count($value, 'test_field'),
      SearchFilterParsingException::class
    );

    $value = '> < 6';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_count($value, 'test_field'),
      SearchFilterParsingException::class
    );
  }

  public function test_should_parse_rating_value_with_range() {
    $expected = [
      'rating_from' => 3,
      'rating_to' => 6,
      'comparator' => null,
    ];

    $value = 'from 3 to 6';
    $actual = EntrySearchRepository::search_parse_rating($value);
    $this->assertEquals($expected, $actual);

    $value = '3 to 6';
    $actual = EntrySearchRepository::search_parse_rating($value);
    $this->assertEquals($expected, $actual);

    $expected = [
      'rating_from' => 3.3,
      'rating_to' => 6.75,
      'comparator' => null,
    ];

    $value = 'from 3.3 to 6.75';
    $actual = EntrySearchRepository::search_parse_rating($value);
    $this->assertEquals($expected, $actual);

    $value = '3.3 to 6.75';
    $actual = EntrySearchRepository::search_parse_rating($value);
    $this->assertEquals($expected, $actual);
  }

  public function test_should_parse_rating_value_with_absolute_value() {
    $expected = [
      'rating_from' => 3,
      'rating_to' => null,
      'comparator' => null,
    ];

    $value = '3';
    $actual = EntrySearchRepository::search_parse_rating($value);
    $this->assertEquals($expected, $actual);

    $expected = [
      'rating_from' => 3.75,
      'rating_to' => null,
      'comparator' => null,
    ];

    $value = '3.75';
    $actual = EntrySearchRepository::search_parse_rating($value);
    $this->assertEquals($expected, $actual);
  }

  public function test_should_parse_rating_value_with_comparators() {
    $expected = [
      'rating_from' => 10,
      'rating_to' => null,
      'comparator' => '>=',
    ];

    $values = ['>= 10', 'gte 10', 'greater than equal 10', 'greater than or equal 10'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_rating($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = [
      'rating_from' => 3,
      'rating_to' => null,
      'comparator' => '>',
    ];

    $values = ['> 3', 'gt 3', 'greater than 3'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_rating($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = [
      'rating_from' => 3,
      'rating_to' => null,
      'comparator' => '>=',
    ];

    $values = ['>= 3', 'gte 3', 'greater than equal 3', 'greater than or equal 3'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_rating($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = [
      'rating_from' => 3,
      'rating_to' => null,
      'comparator' => '<',
    ];

    $values = ['< 3', 'lt 3', 'less than 3'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_rating($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = [
      'rating_from' => 3,
      'rating_to' => null,
      'comparator' => '<=',
    ];

    $values = ['<= 3', 'lte 3', 'less than equal 3', 'less than or equal 3'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_rating($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = [
      'rating_from' => 3.75,
      'rating_to' => null,
      'comparator' => '>',
    ];

    $values = ['> 3.75', 'gt 3.75', 'greater than 3.75'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_rating($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = [
      'rating_from' => 3.75,
      'rating_to' => null,
      'comparator' => '>=',
    ];

    $values = ['>= 3.75', 'gte 3.75', 'greater than equal 3.75', 'greater than or equal 3.75'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_rating($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = [
      'rating_from' => 3.75,
      'rating_to' => null,
      'comparator' => '<',
    ];

    $values = ['< 3.75', 'lt 3.75', 'less than 3.75'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_rating($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = [
      'rating_from' => 3.75,
      'rating_to' => null,
      'comparator' => '<=',
    ];

    $values = ['<= 3.75', 'lte 3.75', 'less than equal 3.75', 'less than or equal 3.75'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_rating($value);
      $this->assertEquals($expected, $actual);
    }
  }

  public function test_should_return_null_on_parsing_empty_rating() {
    $value = '';
    $actual = EntrySearchRepository::search_parse_rating($value);
    $this->assertNull($actual);

    $value = null;
    $actual = EntrySearchRepository::search_parse_rating($value);
    $this->assertNull($actual);
  }

  public function test_should_throw_error_on_parsing_invalid_rating_value() {
    $value = '6 to 3';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_rating($value),
      SearchFilterParsingException::class,
    );

    $value = 'invalid';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_rating($value),
      SearchFilterParsingException::class,
    );

    $value = '>< 6';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_rating($value),
      SearchFilterParsingException::class
    );

    $value = '> < 6';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_rating($value),
      SearchFilterParsingException::class
    );

    $value = '> 10';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_rating($value),
      SearchFilterParsingException::class
    );

    $value = '10 to 10';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_rating($value),
      SearchFilterParsingException::class
    );

    $value = '5 to 5';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_rating($value),
      SearchFilterParsingException::class
    );

    $value = '11';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_rating($value),
      SearchFilterParsingException::class
    );

    $value = '10.1';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_rating($value),
      SearchFilterParsingException::class
    );

    $value = '-1';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_rating($value),
      SearchFilterParsingException::class
    );

    $value = '-0.1';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_rating($value),
      SearchFilterParsingException::class
    );
  }

  public function test_should_parse_release_value_with_range() {
    $expected = [
      'release_from_year' => 2020,
      'release_from_season' => 'winter',
      'release_to_year' => 2021,
      'release_to_season' => 'fall',
      'comparator' => null,
    ];

    $value = 'from 2020 to 2021';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $value = 'from winter 2020 to fall 2021';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $value = '2020 to 2021';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $value = 'winter 2020 to fall 2021';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $expected = [
      'release_from_year' => 2020,
      'release_from_season' => 'spring',
      'release_to_year' => 2099,
      'release_to_season' => 'summer',
      'comparator' => null,
    ];

    $value = 'from spring 2020 to summer 2099';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);
  }

  public function test_should_parse_release_value_with_absolute_value() {
    $expected = [
      'release_from_year' => 2020,
      'release_from_season' => 'winter',
      'release_to_year' => null,
      'release_to_season' => null,
      'comparator' => null,
    ];

    $value = 'winter 2020';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $value = 'Winter 2020';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $value = 'WINTER 2020';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $value = '2020 winter';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $value = '2020 Winter';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $value = '2020 WINTER';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $expected = [
      'release_from_year' => 2999,
      'release_from_season' => 'spring',
      'release_to_year' => null,
      'release_to_season' => null,
      'comparator' => null,
    ];

    $value = 'spring 2999';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $value = '2999 spring';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $expected = [
      'release_from_year' => 1900,
      'release_from_season' => 'winter',
      'release_to_year' => null,
      'release_to_season' => null,
      'comparator' => null,
    ];

    $value = 'winter 1900';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $value = '1900 winter';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $expected = [
      'release_from_year' => 1900,
      'release_from_season' => null,
      'release_to_year' => null,
      'release_to_season' => null,
      'comparator' => null,
    ];

    $value = '1900';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $expected = [
      'release_from_year' => 2999,
      'release_from_season' => null,
      'release_to_year' => null,
      'release_to_season' => null,
      'comparator' => null,
    ];

    $value = '2999';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);
  }

  public function test_should_parse_release_value_with_comparators() {
    $expected = [
      'release_from_year' => 2020,
      'release_from_season' => 'winter',
      'release_to_year' => null,
      'release_to_season' => null,
      'comparator' => '>',
    ];

    $values = [
      '> winter 2020',
      '> 2020 winter',
      'gt winter 2020',
      'gt 2020 winter',
      'greater than winter 2020',
      'greater than 2020 winter',
    ];

    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_release($value);
      $this->assertEquals($expected, $actual, 'Error in $value="' . $value . '"');
    }

    $expected = [
      'release_from_year' => 2020,
      'release_from_season' => 'spring',
      'release_to_year' => null,
      'release_to_season' => null,
      'comparator' => '>=',
    ];

    $values = [
      '>= spring 2020',
      '>= 2020 spring',
      'gte spring 2020',
      'gte 2020 spring',
      'greater than equal spring 2020',
      'greater than equal 2020 spring',
      'greater than or equal spring 2020',
      'greater than or equal 2020 spring',
    ];

    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_release($value);
      $this->assertEquals($expected, $actual, 'Error in $value="' . $value . '"');
    }

    $expected = [
      'release_from_year' => 2020,
      'release_from_season' => 'summer',
      'release_to_year' => null,
      'release_to_season' => null,
      'comparator' => '<=',
    ];

    $values = [
      '<= summer 2020',
      '<= 2020 summer',
      'lte summer 2020',
      'lte 2020 summer',
      'less than equal summer 2020',
      'less than equal 2020 summer',
      'less than or equal summer 2020',
      'less than or equal 2020 summer',
    ];

    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_release($value);
      $this->assertEquals($expected, $actual, 'Error in $value="' . $value . '"');
    }

    $expected = [
      'release_from_year' => 2020,
      'release_from_season' => 'fall',
      'release_to_year' => null,
      'release_to_season' => null,
      'comparator' => '<',
    ];

    $values = [
      '< fall 2020',
      '< 2020 fall',
      'lt fall 2020',
      'lt 2020 fall',
      'less than fall 2020',
      'less than 2020 fall',
    ];

    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_release($value);
      $this->assertEquals($expected, $actual, 'Error in $value="' . $value . '"');
    }

    $expected = [
      'release_from_year' => 2020,
      'release_from_season' => null,
      'release_to_year' => null,
      'release_to_season' => null,
      'comparator' => '>',
    ];

    $values = [
      '> 2020',
      'gt 2020',
      'greater than 2020',
    ];

    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_release($value);
      $this->assertEquals($expected, $actual, 'Error in $value="' . $value . '"');
    }

    $expected = [
      'release_from_year' => 2020,
      'release_from_season' => null,
      'release_to_year' => null,
      'release_to_season' => null,
      'comparator' => '>=',
    ];

    $values = [
      '>= 2020',
      'gte 2020',
      'greater than equal 2020',
      'greater than or equal 2020',
    ];

    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_release($value);
      $this->assertEquals($expected, $actual, 'Error in $value="' . $value . '"');
    }

    $expected = [
      'release_from_year' => 2020,
      'release_from_season' => null,
      'release_to_year' => null,
      'release_to_season' => null,
      'comparator' => '<=',
    ];

    $values = [
      '<= 2020',
      'lte 2020',
      'less than equal 2020',
      'less than or equal 2020',
    ];

    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_release($value);
      $this->assertEquals($expected, $actual, 'Error in $value="' . $value . '"');
    }


    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_release($value);
      $this->assertEquals($expected, $actual, 'Error in $value="' . $value . '"');
    }

    $expected = [
      'release_from_year' => 2020,
      'release_from_season' => null,
      'release_to_year' => null,
      'release_to_season' => null,
      'comparator' => '<',
    ];

    $values = [
      '< 2020',
      'lt 2020',
      'less than 2020',
    ];

    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_release($value);
      $this->assertEquals($expected, $actual, 'Error in $value="' . $value . '"');
    }
  }

  public function test_should_parse_release_value_with_seasons_range() {
    $expected = [
      'release_from_year' => null,
      'release_from_season' => 'winter',
      'release_to_year' => null,
      'release_to_season' => 'fall',
      'comparator' => null,
    ];

    $value = 'from winter to fall';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $value = 'From Winter to Fall';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $value = 'winter to fall';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $expected = [
      'release_from_year' => null,
      'release_from_season' => 'spring',
      'release_to_year' => null,
      'release_to_season' => 'summer',
      'comparator' => null,
    ];

    $value = 'from spring to summer';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $value = 'spring to summer';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $expected = [
      'release_from_year' => null,
      'release_from_season' => 'spring',
      'release_to_year' => null,
      'release_to_season' => 'fall',
      'comparator' => null,
    ];

    $value = 'from spring to fall';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $value = 'spring to fall';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);
  }

  public function test_should_parse_release_value_with_absolute_season() {
    $expected = [
      'release_from_year' => null,
      'release_from_season' => 'winter',
      'release_to_year' => null,
      'release_to_season' => null,
      'comparator' => null,
    ];

    $value = 'winter';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $value = 'WINTER';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $value = 'Winter';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $expected = [
      'release_from_year' => null,
      'release_from_season' => 'spring',
      'release_to_year' => null,
      'release_to_season' => null,
      'comparator' => null,
    ];

    $value = 'spring';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $value = 'SPRING';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $value = 'Spring';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $expected = [
      'release_from_year' => null,
      'release_from_season' => 'summer',
      'release_to_year' => null,
      'release_to_season' => null,
      'comparator' => null,
    ];

    $value = 'summer';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $value = 'SUMMER';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $value = 'Summer';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $expected = [
      'release_from_year' => null,
      'release_from_season' => 'fall',
      'release_to_year' => null,
      'release_to_season' => null,
      'comparator' => null,
    ];

    $value = 'fall';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $value = 'FALL';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $value = 'Fall';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);
  }

  public function test_should_return_null_on_parsing_empty_release() {
    $value = '';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertNull($actual);

    $value = null;
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertNull($actual);
  }

  public function test_should_throw_error_on_parsing_invalid_release_value() {
    $value = 'invalid value';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_release($value),
      Exception::class
    );

    $value = '> 3000';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_release($value),
      SearchFilterParsingException::class
    );

    $value = 'invalid to invalid';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_release($value),
      SearchFilterParsingException::class
    );

    $value = 'invalid 3000';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_release($value),
      SearchFilterParsingException::class
    );

    $value = '3000';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_release($value),
      SearchFilterParsingException::class
    );

    $value = '1899';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_release($value),
      SearchFilterParsingException::class
    );

    $value = '1899 to 3000';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_release($value),
      SearchFilterParsingException::class
    );

    $value = 'invalid 1899 to invalid 3000';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_release($value),
      SearchFilterParsingException::class
    );

    $value = '2020 to 2019';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_release($value),
      SearchFilterParsingException::class
    );

    $value = '> spring';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_release($value),
      SearchFilterParsingException::class
    );

    $value = '>< spring';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_release($value),
      SearchFilterParsingException::class
    );

    $value = '> < spring';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_release($value),
      SearchFilterParsingException::class
    );

    $value = '> < 2020';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_release($value),
      SearchFilterParsingException::class
    );

    $value = 'spring 2020 to winter 2020';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_release($value),
      SearchFilterParsingException::class
    );

    $value = 'spring to winter';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_release($value),
      SearchFilterParsingException::class
    );

    $value = 'summer to spring';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_release($value),
      SearchFilterParsingException::class
    );

    $value = 'summer to winter';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_release($value),
      SearchFilterParsingException::class
    );

    $value = 'fall to winter';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_release($value),
      SearchFilterParsingException::class
    );

    $value = 'fall to summer';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_release($value),
      SearchFilterParsingException::class
    );
  }

  public function test_should_parse_has_value_as_yes() {
    $expected = EntrySearchHasEnum::YES;

    $actual = EntrySearchRepository::search_parse_has_value('yes');
    $this->assertEquals($expected, $actual);

    $actual = EntrySearchRepository::search_parse_has_value('YES');
    $this->assertEquals($expected, $actual);

    $actual = EntrySearchRepository::search_parse_has_value('Yes');
    $this->assertEquals($expected, $actual);

    $actual = EntrySearchRepository::search_parse_has_value('true');
    $this->assertEquals($expected, $actual);

    $actual = EntrySearchRepository::search_parse_has_value('TRUE');
    $this->assertEquals($expected, $actual);

    $actual = EntrySearchRepository::search_parse_has_value('True');
    $this->assertEquals($expected, $actual);

    $actual = EntrySearchRepository::search_parse_has_value('TruE');
    $this->assertEquals($expected, $actual);

    $actual = EntrySearchRepository::search_parse_has_value(true);
    $this->assertEquals($expected, $actual);
  }

  public function test_should_parse_has_value_as_no() {
    $expected = EntrySearchHasEnum::NO;

    $actual = EntrySearchRepository::search_parse_has_value('no');
    $this->assertEquals($expected, $actual);

    $actual = EntrySearchRepository::search_parse_has_value('NO');
    $this->assertEquals($expected, $actual);

    $actual = EntrySearchRepository::search_parse_has_value('No');
    $this->assertEquals($expected, $actual);

    $actual = EntrySearchRepository::search_parse_has_value('false');
    $this->assertEquals($expected, $actual);

    $actual = EntrySearchRepository::search_parse_has_value('FALSE');
    $this->assertEquals($expected, $actual);

    $actual = EntrySearchRepository::search_parse_has_value('False');
    $this->assertEquals($expected, $actual);

    $actual = EntrySearchRepository::search_parse_has_value('FalsE');
    $this->assertEquals($expected, $actual);

    $actual = EntrySearchRepository::search_parse_has_value(false);
    $this->assertEquals($expected, $actual);
  }

  public function test_should_parse_has_value_as_any() {
    $expected = EntrySearchHasEnum::ANY;

    $actual = EntrySearchRepository::search_parse_has_value('any');
    $this->assertEquals($expected, $actual);

    $actual = EntrySearchRepository::search_parse_has_value('null');
    $this->assertEquals($expected, $actual);

    $actual = EntrySearchRepository::search_parse_has_value('default');
    $this->assertEquals($expected, $actual);

    $actual = EntrySearchRepository::search_parse_has_value('');
    $this->assertEquals($expected, $actual);

    $actual = EntrySearchRepository::search_parse_has_value(null);
    $this->assertEquals($expected, $actual);
  }

  public function test_should_parse_codec_value() {
    $codecs = CodecVideo::select('id')->pluck('id')->toArray();

    $value = implode(',', $codecs);
    $actual = EntrySearchRepository::search_parse_codec($value, 'video');
    $this->assertEquals($codecs, $actual);

    $value = implode(', ', $codecs);
    $actual = EntrySearchRepository::search_parse_codec($value, 'video');
    $this->assertEquals($codecs, $actual);

    $expected = [$codecs[0]];
    $value = '' . $codecs[0];
    $actual = EntrySearchRepository::search_parse_codec($value, 'video');
    $this->assertEquals($expected, $actual);

    $codecs = CodecAudio::select('id')->pluck('id')->toArray();

    $value = implode(',', $codecs);
    $actual = EntrySearchRepository::search_parse_codec($value, 'audio');
    $this->assertEquals($codecs, $actual);

    $value = implode(', ', $codecs);
    $actual = EntrySearchRepository::search_parse_codec($value, 'audio');
    $this->assertEquals($codecs, $actual);

    $expected = [$codecs[0]];
    $value = '' . $codecs[0];
    $actual = EntrySearchRepository::search_parse_codec($value, 'audio');
    $this->assertEquals($expected, $actual);
  }

  public function test_should_parse_codec_value_with_valid_and_invalid_id() {
    $codecs = CodecVideo::select('id')->pluck('id')->toArray();
    $invalid_ids = [-1, -99999, 99999];

    $value = implode(',', [...$codecs, ...$invalid_ids]);
    $actual = EntrySearchRepository::search_parse_codec($value, 'video');
    $this->assertEquals($codecs, $actual);

    $value = implode(', ', [...$codecs, ...$invalid_ids]);
    $actual = EntrySearchRepository::search_parse_codec($value, 'video');
    $this->assertEquals($codecs, $actual);

    foreach ($invalid_ids as $value) {
      $this->assertNotContains($value, $actual);
    }
  }

  public function test_should_return_null_on_parsing_empty_codec_or_invalid_type() {
    $value = '';
    $actual = EntrySearchRepository::search_parse_codec($value, '');
    $this->assertNull($actual);

    $value = null;
    $actual = EntrySearchRepository::search_parse_codec($value, '');
    $this->assertNull($actual);

    $value = '-1';
    $invalid_type = '';
    $actual = EntrySearchRepository::search_parse_codec($value, $invalid_type);
    $this->assertNull($actual);

    $value = '-1';
    $invalid_type = 'invalid';
    $actual = EntrySearchRepository::search_parse_codec($value, $invalid_type);
    $this->assertNull($actual);
  }

  public function test_should_throw_error_on_parsing_invalid_codec_value() {
    $value = 'invalid,value';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_codec($value, 'video'),
      Exception::class
    );

    $value = 'invalid,1,2,3';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_codec($value, 'video'),
      Exception::class
    );

    $value = 'invalid';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_codec($value, 'video'),
      Exception::class
    );
  }
}
