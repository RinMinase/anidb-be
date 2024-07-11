<?php

namespace Tests\Feature;

use Tests\BaseTestCase;

class MALTest extends BaseTestCase {

  public function test_should_search_by_keyword_successfully() {
    $search_keyword = "tensei";

    $response = $this->withoutMiddleware()
      ->get('/api/mal/search/' . $search_keyword);

    $response->assertStatus(200)
      ->assertJsonCount(5, 'data')
      ->assertJsonStructure([
        'data' => [[
          'id',
          'title',
        ]]
      ]);
  }

  public function test_should_retreive_information_successfully() {
    $id = "39535";

    $response = $this->withoutMiddleware()
      ->get('/api/mal/title/' . $id);

    $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
          'url',
          'title',
          'synonyms',
          'episodes',
          'premiered',
        ]
      ]);
  }
}
