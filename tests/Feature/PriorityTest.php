<?php

namespace Tests\Feature;

use Tests\BaseTestCase;

class PriorityTest extends BaseTestCase {

  public function test_should_get_all_priorities_successfully() {
    $response = $this->withoutMiddleware()->get('/api/priorities');

    $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [[
          'id',
          'priority',
        ]]
      ]);
  }
}
