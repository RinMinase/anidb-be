<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\BaseTestCase;

use App\Models\Log;
use App\Repositories\LogRepository;

use function PHPSTORM_META\map;

class LogTest extends BaseTestCase {

  // Backup related variables
  private $log_backup = null;

  // Class variables
  private $log_id_1 = 99999;
  private $log_id_2 = 99998;
  private $log_id_3 = 99997;

  private $log_uuid_1 = '1957ee99-e510-4049-b2f3-1ac113133228';
  private $log_uuid_2 = '6e6b1f31-6ba7-4ee4-88b6-d837ae424ac3';
  private $log_uuid_3 = 'e97f0a9d-9be0-417f-a919-b152262068aa';

  private $log_created_at_1 = '2090-01-01 13:00:00';
  private $log_created_at_2 = '2089-12-31 13:00:00';
  private $log_created_at_3 = '2089-12-30 13:00:00';

  // Backup related tables
  private function setup_backup() {
    $hidden_columns = ['id'];
    $this->log_backup = Log::all()->makeVisible($hidden_columns)->toArray();
  }

  // Restore related tables
  private function setup_restore() {
    Log::truncate();
    Log::insert($this->log_backup);
    Log::refreshAutoIncrements();
  }

  // Setup data for testing
  private function setup_config() {
    Log::truncate();

    Log::insert([[
      'id' => $this->log_id_1,
      'uuid' => $this->log_uuid_1,
      'table_changed' => 'hdd',
      'id_changed' => '8eab429d-edc7-4c11-a94c-30903dd65dc6',
      'description' => 'description',
      'action' => 'add',
      'created_at' => $this->log_created_at_1,
    ], [
      'id' => $this->log_id_2,
      'uuid' => $this->log_uuid_2,
      'table_changed' => 'entry',
      'id_changed' => 'e17d35dd-e9cc-4098-97e5-4891083038a1',
      'description' => 'description',
      'action' => 'edit',
      'created_at' => $this->log_created_at_2,
    ], [
      'id' => $this->log_id_3,
      'uuid' => $this->log_uuid_3,
      'table_changed' => 'entry',
      'id_changed' => '22728a9f-7ee7-4290-8b14-1cbe7c780a6e',
      'description' => 'description',
      'action' => 'edit',
      'created_at' => $this->log_created_at_3,
    ]]);
  }

  // Fixtures
  public function setUp(): void {
    parent::setUp();
    $this->setup_backup();
  }

  public function tearDown(): void {
    $this->setup_restore();
    parent::tearDown();
  }

  // Test Cases
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

    $actual_log_ids = collect($response['data'])->pluck('id')->toArray();

    $expected_log_ids = [
      $this->log_uuid_1,
      $this->log_uuid_2,
      $this->log_uuid_3,
    ];

    $this->assertEqualsCanonicalizing($expected_log_ids, $actual_log_ids);
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

    $this->assertEquals(
      Carbon::parse($this->log_created_at_1)->toString(),
      Carbon::parse($response['data'][0]['createdAt'])->toString(),
    );
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

    $this->assertEquals(
      Carbon::parse($this->log_created_at_1)->toString(),
      Carbon::parse($response['data'][0]['createdAt'])->toString(),
    );
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

    $this->assertEquals(
      Carbon::parse($this->log_created_at_2)->toString(),
      Carbon::parse($response['data'][0]['createdAt'])->toString(),
    );
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

    $this->assertCount($test_limit, $response['data'],);
  }

  public function test_should_create_data_with_static_function_call() {
    Log::truncate();

    $test_table_changed = 'entry';
    $test_id_changed = '06f91954-6146-4933-841b-720f06a893cc';
    $test_description = 'description';
    $test_action = 'edit';

    LogRepository::generateLogs(
      $test_table_changed,
      $test_id_changed,
      $test_description,
      $test_action
    );

    $actual = Log::where('id_changed', $test_id_changed)->first();

    $this->assertModelExists($actual);

    $this->assertEquals($test_table_changed, $actual->table_changed);
    $this->assertEquals($test_id_changed, $actual->id_changed);
    $this->assertEquals($test_description, $actual->description);
    $this->assertEquals($test_action, $actual->action);
  }
}
