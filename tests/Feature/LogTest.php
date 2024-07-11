<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\BaseTestCase;

use App\Models\Log;

class LogTest extends BaseTestCase {

  private $log_id_1 = 99999;
  private $log_id_2 = 99998;
  private $log_id_3 = 99997;
  private $log_created_at_1 = '2090-01-01 13:00:00';
  private $log_created_at_2 = '2089-12-31 13:00:00';
  private $log_created_at_3 = '2089-12-30 13:00:00';

  private function setup_config() {
    // Clearing possible duplicate data
    $this->setup_clear();

    Log::insert([[
      'id' => $this->log_id_1,
      'table_changed' => 'hdd',
      'id_changed' => '8eab429d-edc7-4c11-a94c-30903dd65dc6',
      'description' => 'description',
      'action' => 'add',
      'created_at' => $this->log_created_at_1,
    ], [
      'id' => $this->log_id_2,
      'table_changed' => 'entry',
      'id_changed' => 'e17d35dd-e9cc-4098-97e5-4891083038a1',
      'description' => 'description',
      'action' => 'edit',
      'created_at' => $this->log_created_at_2,
    ], [
      'id' => $this->log_id_3,
      'table_changed' => 'entry',
      'id_changed' => '22728a9f-7ee7-4290-8b14-1cbe7c780a6e',
      'description' => 'description',
      'action' => 'edit',
      'created_at' => $this->log_created_at_3,
    ]]);
  }

  private function setup_clear() {
    Log::whereIn('id', [$this->log_id_1, $this->log_id_2, $this->log_id_3])
      ->forceDelete();
  }

  public function test_should_get_all_data() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->get('/api/logs');

    $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [[
          'id',
          'tableChanged',
          'idChanged',
          'description',
          'action',
          'createdAt',
        ]],
        'meta' => [
          'page',
          'limit',
          'results',
          'totalResults',
          'totalPages',
          'hasNext',
        ]
      ]);

    $this->setup_clear();
  }

  public function test_should_get_all_data_sorted_by_column() {
    $this->setup_config();

    $test_column = 'created_at';

    $response = $this->withoutMiddleware()->get('/api/logs?column=' . $test_column);

    $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [[
          'id',
          'tableChanged',
          'idChanged',
          'description',
          'action',
          'createdAt',
        ]],
        'meta' => [
          'page',
          'limit',
          'results',
          'totalResults',
          'totalPages',
          'hasNext',
        ]
      ]);

    $this->assertSame(
      Carbon::parse($this->log_created_at_1)->toString(),
      Carbon::parse($response['data'][0]['createdAt'])->toString(),
    );

    $this->setup_clear();
  }

  public function test_should_get_all_data_ordered() {
    $this->setup_config();

    $test_column = 'created_at';
    $test_order = 'desc';

    $response = $this->withoutMiddleware()->get(
      '/api/logs?column=' . $test_column .
        '&order=' . $test_order
    );

    $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [[
          'id',
          'tableChanged',
          'idChanged',
          'description',
          'action',
          'createdAt',
        ]],
        'meta' => [
          'page',
          'limit',
          'results',
          'totalResults',
          'totalPages',
          'hasNext',
        ]
      ]);

    $this->assertSame(
      Carbon::parse($this->log_created_at_1)->toString(),
      Carbon::parse($response['data'][0]['createdAt'])->toString(),
    );

    $this->setup_clear();
  }

  public function test_should_get_all_data_by_page() {
    $this->setup_config();

    $test_column = 'created_at';
    $test_order = 'desc';
    $test_page = 2;
    $test_limit = 1;

    $response = $this->withoutMiddleware()->get(
      '/api/logs?column=' . $test_column .
        '&order=' . $test_order .
        '&page=' . $test_page .
        '&limit=' . $test_limit
    );

    $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [[
          'id',
          'tableChanged',
          'idChanged',
          'description',
          'action',
          'createdAt',
        ]],
        'meta' => [
          'page',
          'limit',
          'results',
          'totalResults',
          'totalPages',
          'hasNext',
        ]
      ]);

    $this->assertSame(
      Carbon::parse($this->log_created_at_2)->toString(),
      Carbon::parse($response['data'][0]['createdAt'])->toString(),
    );

    $this->setup_clear();
  }

  public function test_should_get_all_data_with_limit() {

    $this->setup_config();

    $test_column = 'created_at';
    $test_order = 'desc';
    $test_page = 1;
    $test_limit = 2;

    $response = $this->withoutMiddleware()->get(
      '/api/logs?column=' . $test_column .
        '&order=' . $test_order .
        '&page=' . $test_page .
        '&limit=' . $test_limit
    );

    $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [[
          'id',
          'tableChanged',
          'idChanged',
          'description',
          'action',
          'createdAt',
        ]],
        'meta' => [
          'page',
          'limit',
          'results',
          'totalResults',
          'totalPages',
          'hasNext',
        ]
      ]);

    $this->assertCount(
      $test_limit,
      $response['data'],
    );

    $this->setup_clear();
  }
}
