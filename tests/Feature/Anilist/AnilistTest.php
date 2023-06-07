<?php

namespace Tests\Feature\Anilist;

use Tests\BaseTestCase;

class AnilistTest extends BaseTestCase {

  public function test_anilist_search() {
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

  public function test_anilist_search_no_auth() {
    $search_keyword = "ten";

    $response = $this->get('/api/anilist/search?query=' . $search_keyword);

    $response->assertStatus(401)
      ->assertJson(['message' => 'Unauthorized']);
  }

  public function test_anilist_get_title() {
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

  public function test_anilist_get_title_no_auth() {
    $id = "101280";

    $response = $this->get('/api/anilist/title/' . $id);

    $response->assertStatus(401)
      ->assertJson(['message' => 'Unauthorized']);
  }
}
