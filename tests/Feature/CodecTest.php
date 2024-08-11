<?php

namespace Tests\Feature;

use Tests\BaseTestCase;

use App\Models\CodecAudio;
use App\Models\CodecVideo;

class CodecTest extends BaseTestCase {

  // Backup related variables
  private $codec_audio_backup = null;
  private $codec_video_backup = null;

  // Class variables
  private $audio_codec_id = 99999;
  private $video_codec_id = 99999;

  private $audio_codec_1 = 'test audio codec';
  private $audio_order_1 = 100;

  private $video_codec_1 = 'test video codec';
  private $video_order_1 = 200;

  // Backup related tables
  private function setup_backup() {
    $hidden_columns = ['created_at', 'updated_at'];
    $this->codec_audio_backup = CodecAudio::all()->makeVisible($hidden_columns)->toArray();

    $hidden_columns = ['created_at', 'updated_at'];
    $this->codec_video_backup = CodecVideo::all()->makeVisible($hidden_columns)->toArray();
  }

  // Restore related tables
  private function setup_restore() {
    CodecAudio::truncate();
    CodecAudio::insert($this->codec_audio_backup);
    CodecAudio::refreshAutoIncrements();

    CodecVideo::truncate();
    CodecVideo::insert($this->codec_video_backup);
    CodecVideo::refreshAutoIncrements();
  }

  // Setup data for testing
  private function setup_config() {
    CodecAudio::truncate();
    CodecVideo::truncate();

    CodecAudio::insert([
      'id' => $this->audio_codec_id,
      'codec' => $this->audio_codec_1,
      'order' => $this->audio_order_1,
      'created_at' => '2020-01-01 13:00:00',
      'updated_at' => '2020-01-01 13:00:00',
    ]);

    CodecVideo::insert([
      'id' => $this->video_codec_id,
      'codec' => $this->video_codec_1,
      'order' => $this->video_order_1,
      'created_at' => '2020-01-01 13:00:00',
      'updated_at' => '2020-01-01 13:00:00',
    ]);
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
  public function test_should_get_all_data() {
    $this->setup_config();

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

    $expected = [
      'audio' => [[
        'id' => $this->audio_codec_id,
        'codec' => $this->audio_codec_1,
        'order' => $this->audio_order_1,
      ]],
      'video' => [[
        'id' => $this->video_codec_id,
        'codec' => $this->video_codec_1,
        'order' => $this->video_order_1,
      ]],
    ];

    $this->assertEquals($expected, $response['data']);
  }

  public function test_should_not_get_all_data_when_not_authorized() {
    $response = $this->get('/api/codecs');

    $response->assertStatus(401)
      ->assertJson(['message' => 'Unauthorized']);
  }

  public function test_should_get_all_audio_codecs() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->get('/api/codecs/audio');

    $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [[
          'id',
          'codec',
          'order',
        ]],
      ]);

    $expected = [
      [
        'id' => $this->audio_codec_id,
        'codec' => $this->audio_codec_1,
        'order' => $this->audio_order_1,
      ]
    ];

    $this->assertEquals($expected, $response['data']);
  }

  public function test_should_get_all_video_codecs() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->get('/api/codecs/video');

    $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [[
          'id',
          'codec',
          'order',
        ]],
      ]);

    $expected = [
      [
        'id' => $this->video_codec_id,
        'codec' => $this->video_codec_1,
        'order' => $this->video_order_1,
      ]
    ];

    $this->assertEquals($expected, $response['data']);
  }

  public function test_should_add_audio_codec_successfully() {
    $test_codec = 'testing codec';
    $test_order = 9999;

    $response = $this->withoutMiddleware()->post('/api/codecs/audio', [
      'codec' => $test_codec,
      'order' => $test_order,
    ]);

    $response->assertStatus(200);

    $data = CodecAudio::where('codec', $test_codec)
      ->where('order', $test_order)
      ->first();

    $actual = $data->toArray();

    $this->assertModelExists($data);
    $this->assertEquals($test_codec, $actual['codec']);
    $this->assertEquals($test_order, $actual['order']);
  }

  public function test_should_add_video_codec_successfully() {
    $test_codec = 'testing codec';
    $test_order = 9999;

    $response = $this->withoutMiddleware()->post('/api/codecs/video', [
      'codec' => $test_codec,
      'order' => $test_order,
    ]);

    $response->assertStatus(200);

    $data = CodecVideo::where('codec', $test_codec)
      ->where('order', $test_order)
      ->first();

    $actual = $data->toArray();

    $this->assertModelExists($data);
    $this->assertEquals($test_codec, $actual['codec']);
    $this->assertEquals($test_order, $actual['order']);
  }

  public function test_should_not_add_audio_codec_on_form_errors() {
    $response = $this->withoutMiddleware()->post('/api/codecs/audio');

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['codec']]);

    $test_codec = 'testing codec' . rand_str(16);
    $test_order = 'string';

    $response = $this->withoutMiddleware()->post('/api/codecs/audio', [
      'codec' => $test_codec,
      'order' => $test_order,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['codec', 'order']]);

    $test_codec = -1;
    $test_order = -1;

    $response = $this->withoutMiddleware()->post('/api/codecs/audio', [
      'codec' => $test_codec,
      'order' => $test_order,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['codec', 'order']]);
  }

  public function test_should_not_add_video_codec_on_form_errors() {
    $response = $this->withoutMiddleware()->post('/api/codecs/video');

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['codec']]);

    $test_codec = 'testing codec' . rand_str(16);
    $test_order = 'string';

    $response = $this->withoutMiddleware()->post('/api/codecs/video', [
      'codec' => $test_codec,
      'order' => $test_order,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['codec', 'order']]);

    $test_codec = -1;
    $test_order = -1;

    $response = $this->withoutMiddleware()->post('/api/codecs/video', [
      'codec' => $test_codec,
      'order' => $test_order,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['codec', 'order']]);
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

    $this->assertEquals($test_codec, $actual['codec']);
    $this->assertEquals($test_order, $actual['order']);
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

    $this->assertEquals($test_codec, $actual['codec']);
    $this->assertEquals($test_order, $actual['order']);
  }

  public function test_should_not_edit_audio_codec_on_form_errors() {
    $this->setup_config();

    $response = $this->withoutMiddleware()
      ->put('/api/codecs/audio/' . $this->audio_codec_id,);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['codec']]);

    $test_codec = 'testing codec' . rand_str(16);
    $test_order = 'string';

    $response = $this->withoutMiddleware()
      ->put(
        '/api/codecs/audio/' . $this->audio_codec_id,
        [
          'codec' => $test_codec,
          'order' => $test_order,
        ]
      );

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['codec', 'order']]);

    $test_codec = -1;
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
      ->assertJsonStructure(['data' => ['codec', 'order']]);
  }

  public function test_should_not_edit_video_codec_on_form_errors() {
    $this->setup_config();

    $response = $this->withoutMiddleware()
      ->put('/api/codecs/video/' . $this->video_codec_id,);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['codec']]);

    $test_codec = 'testing codec' . rand_str(16);
    $test_order = 'string';

    $response = $this->withoutMiddleware()
      ->put(
        '/api/codecs/video/' . $this->video_codec_id,
        [
          'codec' => $test_codec,
          'order' => $test_order,
        ]
      );

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['codec', 'order']]);

    $test_codec = -1;
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
      ->assertJsonStructure(['data' => ['codec', 'order']]);
  }

  public function test_should_delete_audio_codec_successfully() {
    $this->setup_config();

    $response = $this->withoutMiddleware()
      ->delete('/api/codecs/audio/' . $this->audio_codec_id);

    $response->assertStatus(200);

    $actual = CodecAudio::where('id', $this->audio_codec_id)->first();

    $this->assertNull($actual);
  }

  public function test_should_delete_video_codec_successfully() {
    $this->setup_config();

    $response = $this->withoutMiddleware()
      ->delete('/api/codecs/video/' . $this->video_codec_id);

    $response->assertStatus(200);

    $actual = CodecVideo::where('id', $this->video_codec_id)->first();

    $this->assertNull($actual);
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
