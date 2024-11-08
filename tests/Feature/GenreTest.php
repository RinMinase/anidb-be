<?php

namespace Tests\Feature;

use Tests\BaseTestCase;

class GenreTest extends BaseTestCase {

  public function test_should_get_all_genres_successfully() {
    $response = $this->withoutMiddleware()->get('/api/genres');

    $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [[
          'id',
          'genre',
        ]]
      ]);
  }
}
