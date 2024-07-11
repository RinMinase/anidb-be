<?php

namespace Tests\Feature;

use Tests\BaseTestCase;

class AnilistTest extends BaseTestCase {

  public function test_should_search_by_search_keyword_successfully() {
    $search_keyword = "ten";

    $response = $this->withoutMiddleware()
      ->get('/api/anilist/search?query=' . $search_keyword);

    $response->assertStatus(200)
      ->assertJsonCount(10, 'data')
      ->assertJsonStructure([
        'data' => [[
          'id',
          'title',
        ]]
      ]);
  }

  public function test_should_not_search_on_no_auth() {
    $search_keyword = "ten";

    $response = $this->get('/api/anilist/search?query=' . $search_keyword);

    $response->assertStatus(401)
      ->assertJson(['message' => 'Unauthorized']);
  }

  public function test_should_retreive_information_successfully() {
    $id = "101280";

    $response = $this->withoutMiddleware()
      ->get('/api/anilist/title/' . $id);

    $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
          'url',
          'title',
          'episodes',
          'premiered',
        ]
      ]);
  }

  public function test_should_not_retreive_information_on_no_auth() {
    $id = "101280";

    $response = $this->get('/api/anilist/title/' . $id);

    $response->assertStatus(401)
      ->assertJson(['message' => 'Unauthorized']);
  }
}
