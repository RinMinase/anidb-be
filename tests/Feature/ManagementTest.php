<?php

namespace Tests\Feature;

use Tests\BaseTestCase;

class ManagementTest extends BaseTestCase {
  public function test_should_get_all_managment_data() {
    $response = $this->withoutMiddleware()->get('/api/management');

    $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
          'count' => [
            'entries',
            'buckets',
            'partials',
          ],
          'stats' => [
            'watchSeconds',
            'watch',
            'watchSubtext',
            'rewatchSeconds',
            'rewatch',
            'rewatchSubtext',
            'bucketSize',
            'entrySize',
            'episodes',
            'titles',
            'seasons',
          ],
          'graph' => [
            'quality' => [
              'quality2160',
              'quality1080',
              'quality720',
              'quality480',
              'quality360',
            ],
            'months' => [
              'jan',
              'feb',
              'mar',
              'apr',
              'may',
              'jun',
              'jul',
              'aug',
              'sep',
              'oct',
              'nov',
              'dec',
            ]
          ]
        ],
      ]);
  }

  public function test_should_not_get_all_data_when_not_authorized() {
    $response = $this->get('/api/management');

    $response->assertStatus(401)
      ->assertJson(['message' => 'Unauthorized']);
  }
}
