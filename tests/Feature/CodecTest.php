<?php

namespace Tests\Feature;

use Tests\BaseTestCase;

use App\Models\CodecAudio;
use App\Models\CodecVideo;

class CodecTest extends BaseTestCase {

  private $audio_codec_id = 99999;
  private $video_codec_id = 99999;

  private function setup_config() {
    // Clearing possible duplicate data
    $this->setup_clear();

    CodecAudio::insert([
      'id' => $this->audio_codec_id,
      'codec' => 'test audio codec',
      'order' => 100,
      'created_at' => '2020-01-01 13:00:00',
      'updated_at' => '2020-01-01 13:00:00',
    ]);

    CodecVideo::insert([
      'id' => $this->video_codec_id,
      'codec' => 'test video codec',
      'order' => 100,
      'created_at' => '2020-01-01 13:00:00',
      'updated_at' => '2020-01-01 13:00:00',
    ]);
  }

  private function setup_clear() {
    CodecAudio::where('id', $this->audio_codec_id)->forceDelete();
    CodecVideo::where('id', $this->video_codec_id)->forceDelete();
  }

  public function test_should_get_all_data() {
    $response = $this->withoutMiddleware()->get('/api/codecs');

    $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
          'audio' => [[
            'id',
            'codec',
            'order',
          ]],
          'video' => [[
            'id',
            'codec',
            'order',
          ]],
        ],
      ]);
  }

  public function test_should_not_get_all_data_when_not_authorized() {
    $response = $this->get('/api/codecs');

    $response->assertStatus(401)
      ->assertJson(['message' => 'Unauthorized']);
  }

  public function test_should_get_all_audio_codecs() {
    $response = $this->withoutMiddleware()->get('/api/codecs/audio');

    $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [[
          'id',
          'codec',
          'order',
        ]],
      ]);
  }

  public function test_should_get_all_video_codecs() {
    $response = $this->withoutMiddleware()->get('/api/codecs/video');

    $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [[
          'id',
          'codec',
          'order',
        ]],
      ]);
  }

  public function test_should_add_audio_codec_successfully() {
    $test_codec = 'testing codec';
    $test_order = 9999;

    // Clearing possible duplicate data
    CodecAudio::where('codec', $test_codec)
      ->where('order', $test_order)
      ->delete();

    $response = $this->withoutMiddleware()->post('/api/codecs/audio', [
      'codec' => $test_codec,
      'order' => $test_order,
    ]);

    $response->assertStatus(200);

    $data = CodecAudio::where('codec', $test_codec)
      ->where('order', $test_order)
      ->first();

    $actual = $data->toArray();

    $this->assertSame($test_codec, $actual['codec']);
    $this->assertSame($test_order, $actual['order']);

    $data->delete();
  }

  public function test_should_add_video_codec_successfully() {
    $test_codec = 'testing codec';
    $test_order = 9999;

    // Clearing possible duplicate data
    CodecVideo::where('codec', $test_codec)
      ->where('order', $test_order)
      ->delete();

    $response = $this->withoutMiddleware()->post('/api/codecs/video', [
      'codec' => $test_codec,
      'order' => $test_order,
    ]);

    $response->assertStatus(200);

    $data = CodecVideo::where('codec', $test_codec)
      ->where('order', $test_order)
      ->first();

    $actual = $data->toArray();

    $this->assertSame($test_codec, $actual['codec']);
    $this->assertSame($test_order, $actual['order']);

    $data->delete();
  }

  public function test_should_not_add_audio_codec_on_form_errors() {
    $test_codec = 'testing sample code';

    $response = $this->withoutMiddleware()->post('/api/codecs/audio', [
      'codec' => $test_codec,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['codec']]);
  }

  public function test_should_not_add_video_codec_on_form_errors() {
    $test_codec = 'testing sample code';

    $response = $this->withoutMiddleware()->post('/api/codecs/video', [
      'codec' => $test_codec,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['codec']]);
  }

  public function test_should_edit_audio_codec_successfully() {
    $this->setup_config();

    $test_codec = 'testing codec';
    $test_order = 150;

    $response = $this->withoutMiddleware()->put('/api/codecs/audio/' . $this->audio_codec_id, [
      'codec' => $test_codec,
      'order' => $test_order,
    ]);

    $response->assertStatus(200);

    $actual = CodecAudio::where('id', $this->audio_codec_id)
      ->first()
      ->toArray();

    $this->assertSame($test_codec, $actual['codec']);
    $this->assertSame($test_order, $actual['order']);

    $this->setup_clear();
  }

  public function test_should_edit_video_codec_successfully() {
    $this->setup_config();

    $test_codec = 'testing codec';
    $test_order = 150;

    $response = $this->withoutMiddleware()->put('/api/codecs/video/' . $this->video_codec_id, [
      'codec' => $test_codec,
      'order' => $test_order,
    ]);

    $response->assertStatus(200);

    $actual = CodecVideo::where('id', $this->video_codec_id)
      ->first()
      ->toArray();

    $this->assertSame($test_codec, $actual['codec']);
    $this->assertSame($test_order, $actual['order']);

    $this->setup_clear();
  }

  public function test_should_not_edit_audio_codec_on_form_errors() {
    $this->setup_config();

    $test_codec = 'testing very long codec';
    $test_order = -1;

    $response = $this->withoutMiddleware()
      ->put(
        '/api/codecs/audio/' . $this->audio_codec_id,
        [
          'codec' => $test_codec,
          'order' => $test_order,
        ]
      );

    $response->assertStatus(401)
      ->assertJsonStructure([
        'data' => [
          'codec',
          'order',
        ],
      ]);

    $test_order = 'string value';

    $response = $this->withoutMiddleware()
      ->put(
        '/api/codecs/audio/' . $this->audio_codec_id,
        [
          'order' => $test_order,
        ]
      );

    $response->assertStatus(401)
      ->assertJsonStructure([
        'data' => [
          'codec',
          'order',
        ],
      ]);

    $this->setup_clear();
  }

  public function test_should_not_edit_video_codec_on_form_errors() {
    $this->setup_config();

    $test_codec = 'testing very long codec';
    $test_order = -1;

    $response = $this->withoutMiddleware()
      ->put(
        '/api/codecs/video/' . $this->video_codec_id,
        [
          'codec' => $test_codec,
          'order' => $test_order,
        ]
      );

    $response->assertStatus(401)
      ->assertJsonStructure([
        'data' => [
          'codec',
          'order',
        ],
      ]);

    $test_order = 'string value';

    $response = $this->withoutMiddleware()
      ->put(
        '/api/codecs/video/' . $this->video_codec_id,
        [
          'order' => $test_order,
        ]
      );

    $response->assertStatus(401)
      ->assertJsonStructure([
        'data' => [
          'codec',
          'order',
        ],
      ]);

    $this->setup_clear();
  }

  public function test_should_delete_audio_codec_successfully() {
    $this->setup_config();

    $response = $this->withoutMiddleware()
      ->delete('/api/codecs/audio/' . $this->audio_codec_id);

    $response->assertStatus(200);

    $actual = CodecAudio::where('id', $this->audio_codec_id)->first();

    $this->assertNull($actual);

    $this->setup_clear();
  }

  public function test_should_delete_video_codec_successfully() {
    $this->setup_config();

    $response = $this->withoutMiddleware()
      ->delete('/api/codecs/video/' . $this->video_codec_id);

    $response->assertStatus(200);

    $actual = CodecVideo::where('id', $this->video_codec_id)->first();

    $this->assertNull($actual);

    $this->setup_clear();
  }

  public function test_should_not_delete_non_existent_audio_codec() {
    $invalid_id = -1;

    $response = $this->withoutMiddleware()->delete('/api/codecs/audio/' . $invalid_id);

    $response->assertStatus(404);
  }

  public function test_should_not_delete_non_existent_video_codec() {
    $invalid_id = -1;

    $response = $this->withoutMiddleware()->delete('/api/codecs/video/' . $invalid_id);

    $response->assertStatus(404);
  }
}
