<?php

namespace Tests\Feature\Entry;

use Tests\BaseTestCase;
use Carbon\Carbon;

use App\Models\Entry;

class EntryTest extends BaseTestCase {

  public function test_get_entry() {
    $test_id = 9999;
    $test_uuid = "e9597119-8452-4f2b-96d8-f2b1b1d2f158";
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

    // Clearing possible duplicate data
    Entry::where('uuid', $test_uuid)->delete();
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

    $response = $this->withoutMiddleware()
      ->get('/api/entries/' . $test_uuid);

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
    Entry::where('uuid', $test_uuid)->delete();
  }
}
