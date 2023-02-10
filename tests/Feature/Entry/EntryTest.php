<?php

namespace Tests\Feature\Entry;

use Tests\BaseTestCase;
use Carbon\Carbon;

use App\Models\Entry;

class EntryTest extends BaseTestCase {

  private $entry_uuid = "e9597119-8452-4f2b-96d8-f2b1b1d2f158";
  private $entry_uuid_2 = "99831cce-4ab5-46f3-8a84-371d1bd1624f";
  private $entry_uuid_3 = "620f8679-1b3c-4fe7-a18c-733d0a05dacd";

  private function setup_config() {
    $test_id = 9999;
    $test_uuid = $this->entry_uuid;
    $test_quality = 1;
    $test_title = "title";
    $test_date_finished = "2001-01-01";
    $test_duration = 10_000;
    $test_filesize = 10_000_000;
    $test_episodes = 30;
    $test_ovas = 20;
    $test_specials = 10;
    $test_encoder_video = "encoder video";
    $test_encoder_audio = "encoder audio";
    $test_encoder_subs = "encoder subs";
    $test_codec_hdr = 1;
    $test_codec_video = 1;
    $test_codec_audio = 1;
    $test_release_year = 2000;
    $test_release_season = "Winter";
    $test_variants = "variant";
    $test_remarks = "remark";
    $test_created_at = Carbon::now();
    $test_updated_at = Carbon::now();

    $test_id_2 = 10000;
    $test_uuid_2 = $this->entry_uuid_2;
    $test_quality_2 = 1;
    $test_title_2 = "title 2";
    $test_created_at_2 = Carbon::now();
    $test_updated_at_2 = Carbon::now();

    $test_id_3 = 10001;
    $test_uuid_3 = $this->entry_uuid_3;
    $test_quality_3 = 1;
    $test_title_3 = "title 3";
    $test_created_at_3 = Carbon::now();
    $test_updated_at_3 = Carbon::now();

    // Clearing possible duplicate data
    Entry::where('uuid', $test_uuid)->forceDelete();
    Entry::where('uuid', $test_uuid_2)->forceDelete();
    Entry::where('uuid', $test_uuid_3)->forceDelete();

    Entry::create([
      'id' => $test_id,
      'uuid' => $test_uuid,
      'id_quality' => $test_quality,
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
      'codec_hdr' => $test_codec_hdr,
      'codec_video' => $test_codec_video,
      'codec_audio' => $test_codec_audio,
      'release_year' => $test_release_year,
      'release_season' => $test_release_season,
      'variants' => $test_variants,
      'remarks' => $test_remarks,
      'created_at' => $test_created_at,
      'updated_at' => $test_updated_at,
    ]);

    Entry::create([
      'id' => $test_id_2,
      'uuid' => $test_uuid_2,
      'id_quality' => $test_quality_2,
      'title' => $test_title_2,
      'created_at' => $test_created_at_2,
      'updated_at' => $test_updated_at_2,
    ]);

    Entry::create([
      'id' => $test_id_3,
      'uuid' => $test_uuid_3,
      'id_quality' => $test_quality_3,
      'title' => $test_title_3,
      'created_at' => $test_created_at_3,
      'updated_at' => $test_updated_at_3,
    ]);
  }

  private function setup_clear() {
    Entry::where('uuid', $this->entry_uuid)->forceDelete();
    Entry::where('uuid', $this->entry_uuid_2)->forceDelete();
    Entry::where('uuid', $this->entry_uuid_3)->forceDelete();
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
}
