<?php

namespace Tests\Feature\Entry;

use Tests\BaseTestCase;

class EntryBySequenceTest extends BaseTestCase {

  public function test_get_entries_by_sequence() {
    $response = $this->withoutMiddleware()
      ->get('/api/entries/by-sequence/1');

    $response->assertStatus(200)
      ->assertJsonStructure([
        'data',
        'stats' => [
          'titlesPerDay',
          'epsPerDay',
          'quality2160',
          'quality1080',
          'quality720',
          'quality480',
          'quality360',
          'totalTitles',
          'totalEps',
          'totalSize',
          'totalDays',
          'startDate',
          'endDate',
        ],
      ]);
  }

  public function test_get_entries_by_sequence_no_auth() {
    $response = $this->get('/api/entries/by-sequence/1');

    $response->assertStatus(401)
      ->assertJson(['message' => 'Unauthorized']);
  }
}
