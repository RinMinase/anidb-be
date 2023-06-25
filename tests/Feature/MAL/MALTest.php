<?php

namespace Tests\Feature\MAL;

use Tests\BaseTestCase;

class MALTest extends BaseTestCase {

  public function test_mal_search() {
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

  public function test_mal_get_info() {
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
