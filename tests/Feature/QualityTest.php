<?php

namespace Tests\Feature;

use Tests\BaseTestCase;

use App\Repositories\QualityRepository;

class QualityTest extends BaseTestCase {

  public function test_should_get_all_qualities_successfully() {
    $response = $this->withoutMiddleware()->get('/api/qualities');

    $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [[
          'id',
          'quality',
        ]]
      ]);
  }

  public function test_should_return_the_correct_quality_on_parsing_string() {
    $expected = '4K 2160p';
    $values = ['4k', 'uhd', '2160p', '2160'];
    foreach ($values as $value) {
      $actual = QualityRepository::parseQuality($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = 'FHD 1080p';
    $values = ['fhd', '1080p', '1080'];
    foreach ($values as $value) {
      $actual = QualityRepository::parseQuality($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = 'HD 720p';
    $values = ['hd', '720p', '720'];
    foreach ($values as $value) {
      $actual = QualityRepository::parseQuality($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = 'HQ 480p';
    $values = ['hq', '480p', '480'];
    foreach ($values as $value) {
      $actual = QualityRepository::parseQuality($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = 'LQ 360p';
    $values = ['lq', '360p', '360'];
    foreach ($values as $value) {
      $actual = QualityRepository::parseQuality($value);
      $this->assertEquals($expected, $actual);
    }
  }

  public function test_should_return_null_on_parsing_invalid_string() {
    $value = 'invalid string';
    $actual = QualityRepository::parseQuality($value);
    $this->assertNull($actual);

    $value = 'invalid';
    $actual = QualityRepository::parseQuality($value);
    $this->assertNull($actual);

    $value = '';
    $actual = QualityRepository::parseQuality($value);
    $this->assertNull($actual);
  }
}
