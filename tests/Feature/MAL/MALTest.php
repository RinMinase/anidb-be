<?php

namespace Tests\Feature\MAL;

use Tests\BaseTestCase;

class MALTest extends BaseTestCase {

  public function test_mal_search() {
    $search_keyword = "tensei";

    $this->withoutMiddleware();

    $response = $this->get('/api/mal/' . $search_keyword);

    $response->assertStatus(200)
      ->assertJsonCount(5)
      ->assertJsonStructure([['id', 'title']]);
  }

  public function test_mal_get_info() {
    $search_keyword = "39535";

    $this->withoutMiddleware();

    $response = $this->get('/api/mal/' . $search_keyword);

    $response->assertStatus(200)
      ->assertJsonStructure([
        'url',
        'title',
        'synonyms',
        'episodes',
        'premiered',
      ]);
  }
}
