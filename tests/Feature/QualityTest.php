<?php

namespace Tests\Feature;

use Tests\BaseTestCase;

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
}
