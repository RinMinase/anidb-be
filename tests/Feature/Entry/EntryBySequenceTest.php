<?php

namespace Tests\Feature\Entry;

use Tests\BaseTestCase;

class EntryBySequenceTest extends BaseTestCase {

  public function test_get_entries_by_sequence() {
    $response = $this->withoutMiddleware()
      ->get('/api/entries/by-sequence/1');

    $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
          'data',
          'stats' => [
            'titles_per_day',
            'eps_per_day',
            'quality_2160',
            'quality_1080',
            'quality_720',
            'quality_480',
            'quality_360',
            'total_titles',
            'total_eps',
            'total_size',
            'total_days',
            'start_date',
            'end_date',
          ],
        ],
      ]);
  }

  public function test_get_entries_by_sequence_no_auth() {
    $response = $this->get('/api/entries/by-sequence/1');

    $response->assertStatus(401)
      ->assertJson(['message' => 'Unauthorized']);
  }
}
