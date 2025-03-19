<?php

namespace Tests\Feature;

use Tests\BaseTestCase;

use App\Models\Catalog;
use App\Models\Partial;
use App\Models\Priority;

class CatalogTest extends BaseTestCase {

  // Backup related variables
  private $catalog_backup = null;
  private $partial_backup = null;

  // Class variables
  private $catalog_id_1 = 99999;
  private $catalog_uuid_1 = 'ee7163e8-0be6-4bdf-85a7-eba3af21dfbe';
  private $catalog_year_1 = 2050;
  private $catalog_season_1 = 'Spring';

  private $catalog_id_2 = 99998;
  private $catalog_uuid_2 = '41ecbac2-8e65-45eb-a0fe-a2bb82f701c4';
  private $catalog_year_2 = 2050;
  private $catalog_season_2 = 'Fall';

  private $total_partial_count = 4;

  private $partial_id_1 = 99999;
  private $partial_uuid_1 = '0f98f5c0-f493-41a1-a184-d43782223d47';
  private $partial_title_1 = 'sample partial title 1';

  private $partial_id_2 = 99998;
  private $partial_uuid_2 = 'a44a5351-a342-4f71-a95c-bce04d161442';
  private $partial_title_2 = 'for searching title 2';

  private $partial_id_3 = 99997;
  private $partial_uuid_3 = '39a393d4-5a27-4936-aaad-cf68bd148d4b';
  private $partial_title_3 = 'for searching title 3';

  private $partial_id_4 = 99996;
  private $partial_uuid_4 = '21822256-5171-4b7d-8cfc-0a03a88753cb';
  private $partial_title_4 = 'sample partial title 4';

  // Backup related tables
  private function setup_backup() {
    $hidden_columns = ['id', 'updated_at', 'deleted_at'];
    $this->catalog_backup = Catalog::all()->makeVisible($hidden_columns)->toArray();

    $hidden_columns = ['id', 'created_at', 'updated_at', 'deleted_at'];
    $this->partial_backup = Partial::all()->makeVisible($hidden_columns)->toArray();
  }

  // Restore related tables
  private function setup_restore() {
    Partial::truncate();
    Catalog::truncate();

    Catalog::insert($this->catalog_backup);
    Catalog::refreshAutoIncrements();

    Partial::insert($this->partial_backup);
    Partial::refreshAutoIncrements();
  }

  // Setup data for testing
  private function setup_config() {
    Partial::truncate();
    Catalog::truncate();

    $id_priority = Priority::where('priority', 'High')->first()->id;

    Catalog::insert([[
      'id' => $this->catalog_id_1,
      'uuid' => $this->catalog_uuid_1,
      'year' => $this->catalog_year_1,
      'season' => $this->catalog_season_1,
      'created_at' => '2020-01-01 13:00:00',
      'updated_at' => '2020-01-01 13:00:00',
    ], [
      'id' => $this->catalog_id_2,
      'uuid' => $this->catalog_uuid_2,
      'year' => $this->catalog_year_2,
      'season' => $this->catalog_season_2,
      'created_at' => '2020-01-01 13:00:00',
      'updated_at' => '2020-01-01 13:00:00',
    ]]);

    Partial::insert([[
      'id' => $this->partial_id_1,
      'uuid' => $this->partial_uuid_1,
      'title' => $this->partial_title_1,
      'id_catalog' => $this->catalog_id_1,
      'id_priority' => $id_priority,
      'created_at' => '2020-01-01 13:00:00',
      'updated_at' => '2020-01-01 13:00:00',
    ], [
      'id' => $this->partial_id_2,
      'uuid' => $this->partial_uuid_2,
      'title' => $this->partial_title_2,
      'id_catalog' => $this->catalog_id_1,
      'id_priority' => $id_priority,
      'created_at' => '2020-01-01 13:00:00',
      'updated_at' => '2020-01-01 13:00:00',
    ], [
      'id' => $this->partial_id_3,
      'uuid' => $this->partial_uuid_3,
      'title' => $this->partial_title_3,
      'id_catalog' => $this->catalog_id_2,
      'id_priority' => $id_priority,
      'created_at' => '2020-01-01 13:00:00',
      'updated_at' => '2020-01-01 13:00:00',
    ], [
      'id' => $this->partial_id_4,
      'uuid' => $this->partial_uuid_4,
      'title' => $this->partial_title_4,
      'id_catalog' => $this->catalog_id_2,
      'id_priority' => $id_priority,
      'created_at' => '2020-01-01 13:00:00',
      'updated_at' => '2020-01-01 13:00:00',
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

  // Helper Functions
  private function stringify_multi_data(array $data): string {
    $ret_val = '';

    foreach ($data as $base_key => $item) {
      foreach ($item as $key => $value) {
        $ret_val .= $base_key . '[' . $key . ']=' . $value . '&';
      }
    }

    if ($ret_val) {
      $ret_val = rtrim($ret_val, '&');
    }

    return $ret_val;
  }

  /**
   * Catalogs
   */
  public function test_should_get_all_catalogs() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->get('/api/catalogs');

    $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [[
          'uuid',
          'year',
          'season',
        ]],
      ]);

    $expected = [
      [
        'uuid' => $this->catalog_uuid_1,
        'year' => 2050,
        'season' => 'Spring',
      ],
      [
        'uuid' => $this->catalog_uuid_2,
        'year' => 2050,
        'season' => 'Fall',
      ],
    ];

    $this->assertEqualsCanonicalizing($expected, $response['data']);
  }

  public function test_should_add_catalog_successfully() {
    $test_year = 2051;
    $test_season = 'Spring';

    $response = $this->withoutMiddleware()->post('/api/catalogs', [
      'year' => $test_year,
      'season' => $test_season,
    ]);

    $response->assertStatus(200);

    $data = Catalog::where('year', $test_year)
      ->where('season', $test_season)
      ->first();

    $actual = $data->toArray();

    $this->assertNotNull($actual);
    $this->assertNotNull($actual['uuid']);
    $this->assertEquals($test_year, $actual['year']);
    $this->assertEquals($test_season, $actual['season']);
  }

  public function test_should_not_add_catalog_on_form_errors() {
    $response = $this->withoutMiddleware()->post('/api/catalogs');

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['year', 'season']]);

    $test_year = 3000;
    $test_season = 'Invalid';

    $response = $this->withoutMiddleware()->post('/api/catalogs', [
      'year' => $test_year,
      'season' => $test_season,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['year', 'season']]);

    $test_valid_season = 'Spring';

    $test_year = 3000;

    $response = $this->withoutMiddleware()->post('/api/catalogs', [
      'year' => $test_year,
      'season' => $test_valid_season,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['year']]);

    $test_year = 1899;

    $response = $this->withoutMiddleware()->post('/api/catalogs', [
      'year' => $test_year,
      'season' => $test_valid_season,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['year']]);

    $test_year = 'string';

    $response = $this->withoutMiddleware()->post('/api/catalogs', [
      'year' => $test_year,
      'season' => $test_valid_season,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['year']]);
  }

  public function test_should_edit_catalog_successfully() {
    $this->setup_config();

    $test_year = 2051;
    $test_season = 'Fall';

    $response = $this->withoutMiddleware()->put('/api/catalogs/' . $this->catalog_uuid_1, [
      'year' => $test_year,
      'season' => $test_season,
    ]);

    $response->assertStatus(200);

    $data = Catalog::where('year', $test_year)
      ->where('season', $test_season)
      ->first();

    $actual = $data->toArray();

    $this->assertNotNull($actual);
    $this->assertNotNull($actual['uuid']);
    $this->assertEquals($test_year, $actual['year']);
    $this->assertEquals($test_season, $actual['season']);

    $data->delete();
  }

  public function test_should_not_edit_catalog_on_form_errors() {
    $response = $this->withoutMiddleware()->put('/api/catalogs/' . $this->catalog_uuid_1);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['year', 'season']]);

    $test_year = 3000;
    $test_season = 'Invalid';

    $response = $this->withoutMiddleware()->put('/api/catalogs/' . $this->catalog_uuid_1, [
      'year' => $test_year,
      'season' => $test_season,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['year', 'season']]);

    $test_valid_season = 'Spring';

    $test_year = 3000;

    $response = $this->withoutMiddleware()->put('/api/catalogs/' . $this->catalog_uuid_1, [
      'year' => $test_year,
      'season' => $test_valid_season,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['year']]);

    $test_year = 1899;

    $response = $this->withoutMiddleware()->put('/api/catalogs/' . $this->catalog_uuid_1, [
      'year' => $test_year,
      'season' => $test_valid_season,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['year']]);

    $test_year = 'string';

    $response = $this->withoutMiddleware()->put('/api/catalogs/' . $this->catalog_uuid_1, [
      'year' => $test_year,
      'season' => $test_valid_season,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['year']]);
  }

  public function test_should_not_edit_catalog_when_id_is_used_instead_of_uuid() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->put('/api/catalogs/' . $this->catalog_id_1);

    $response->assertStatus(404);
  }

  public function test_should_delete_catalog_successfully() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->delete('/api/catalogs/' . $this->catalog_uuid_1);

    $response->assertStatus(200);

    $actualCatalog = Catalog::where('uuid', $this->catalog_uuid_1)->first();
    $actualPartials = Partial::where('id_catalog', $this->catalog_id_1)->get();

    $this->assertNull($actualCatalog);
    $this->assertCount(0, $actualPartials);
  }

  public function test_should_not_delete_catalog_when_id_is_used_instead_of_uuid() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->delete('/api/catalogs/' . $this->catalog_id_1);

    $response->assertStatus(404);
  }

  public function test_should_not_delete_non_existent_catalog() {
    $invalid_id = 'aaaaaaaa-1234-1234-1234-aaaaaaaa1234';

    $response = $this->withoutMiddleware()->delete('/api/catalogs/' . $invalid_id);

    $response->assertStatus(404);

    $invalid_id = -1;

    $response = $this->withoutMiddleware()->delete('/api/catalogs/' . $invalid_id);

    $response->assertStatus(404);
  }

  public function test_should_get_partials_in_catalog_successfully() {
    $this->setup_config();

    $response = $this->withoutMiddleware()
      ->get('/api/catalogs/' . $this->catalog_uuid_1 . '/partials');

    $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [[
          'uuid',
          'title',
          'priority',
        ]],
        'stats' => [
          'uuid',
          'year',
          'season',
        ],
      ]);

    $expected_data = [[
      'uuid' => $this->partial_uuid_1,
      'title' => $this->partial_title_1,
    ], [
      'uuid' => $this->partial_uuid_2,
      'title' => $this->partial_title_2,
    ]];

    $expected_stats = [
      'uuid' => $this->catalog_uuid_1,
      'year' => $this->catalog_year_1,
      'season' => $this->catalog_season_1,
    ];

    $this->assertArrayIsEqualToArrayOnlyConsideringListOfKeys(
      $expected_data[0],
      $response['data'][0],
      array_keys($expected_data),
    );

    $this->assertArrayIsEqualToArrayOnlyConsideringListOfKeys(
      $expected_data[1],
      $response['data'][1],
      array_keys($expected_data),
    );

    $this->assertEqualsCanonicalizing($expected_stats, $response['stats']);

    $response = $this->withoutMiddleware()
      ->get('/api/catalogs/' . $this->catalog_uuid_2 . '/partials');

    $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [[
          'uuid',
          'title',
          'priority',
        ]],
        'stats' => [
          'uuid',
          'year',
          'season',
        ],
      ]);

    $expected_data = [[
      'uuid' => $this->partial_uuid_3,
      'title' => $this->partial_title_3,
    ], [
      'uuid' => $this->partial_uuid_4,
      'title' => $this->partial_title_4,
    ]];

    $expected_stats = [
      'uuid' => $this->catalog_uuid_2,
      'year' => $this->catalog_year_2,
      'season' => $this->catalog_season_2,
    ];

    $this->assertArrayIsEqualToArrayOnlyConsideringListOfKeys(
      $expected_data[0],
      $response['data'][0],
      array_keys($expected_data),
    );

    $this->assertArrayIsEqualToArrayOnlyConsideringListOfKeys(
      $expected_data[1],
      $response['data'][1],
      array_keys($expected_data),
    );

    $this->assertEqualsCanonicalizing($expected_stats, $response['stats']);
  }

  public function test_should_not_get_partials_when_catalog_id_is_used_instead_of_uuid() {
    $this->setup_config();

    $response = $this->withoutMiddleware()
      ->get('/api/catalogs/' . $this->catalog_id_1 . '/partials');

    $response->assertStatus(404);
  }

  public function test_should_not_get_partials_in_invalid_catalog() {
    $invalid_id = 'aaaaaaaa-1234-1234-1234-aaaaaaaa1234';

    $response = $this->withoutMiddleware()->get('/api/catalogs/' . $invalid_id . '/partials');

    $response->assertStatus(404);

    $invalid_id = -1;

    $response = $this->withoutMiddleware()->get('/api/catalogs/' . $invalid_id . '/partials');

    $response->assertStatus(404);
  }

  /**
   * Partials
   */
  public function test_should_get_partials_in_all_catalogs_successfully() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->get('/api/partials');

    $response->assertStatus(200)
      ->assertJsonCount($this->total_partial_count, 'data')
      ->assertJsonStructure([
        'data' => [[
          'uuid',
          'catalog',
          'title',
          'priority',
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
  }

  public function test_should_get_partials_in_all_catalogs_and_verify_paginated_data() {
    $this->setup_config();

    $test_page = 2;
    $test_limit = 2;
    $response = $this->withoutMiddleware()
      ->get('/api/partials?page=' . $test_page . '&limit=' . $test_limit);

    $response->assertStatus(200)
      ->assertJsonCount($test_limit, 'data')
      ->assertJsonStructure([
        'data' => [[
          'uuid',
          'catalog',
          'title',
          'priority',
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

    $actual_meta = $response['meta'];

    $expected_total_pages = intval(ceil($this->total_partial_count / $test_limit));
    $expected_has_next = $test_page < $expected_total_pages;
    $expected_meta = [
      'page' => $test_page,
      'limit' => $test_limit,
      'results' => $test_limit,
      'totalResults' => $this->total_partial_count,
      'totalPages' => $expected_total_pages,
      'hasNext' => $expected_has_next,
    ];

    $this->assertEqualsCanonicalizing($expected_meta, $actual_meta);
  }

  public function test_should_get_partials_in_all_catalogs_and_search_title() {
    $this->setup_config();

    $test_query = 'for searching title';
    $response = $this->withoutMiddleware()
      ->get('/api/partials?query=' . $test_query);

    $response->assertStatus(200)
      ->assertJsonCount(4, 'data')
      ->assertJsonStructure([
        'data' => [[
          'uuid',
          'catalog',
          'title',
          'priority',
        ]]
      ]);

    $expected = [
      'uuid' => $this->partial_uuid_2,
      'catalog' => $this->catalog_season_1 . ' ' . $this->catalog_year_1,
      'title' => $this->partial_title_2,
    ];

    $this->assertArrayIsEqualToArrayOnlyConsideringListOfKeys(
      $expected,
      $response['data'][0],
      array_keys($expected),
    );

    $expected = [
      'uuid' => $this->partial_uuid_3,
      'catalog' => $this->catalog_season_2 . ' ' . $this->catalog_year_2,
      'title' => $this->partial_title_3,
    ];

    $this->assertArrayIsEqualToArrayOnlyConsideringListOfKeys(
      $expected,
      $response['data'][1],
      array_keys($expected),
    );
  }

  public function test_should_get_partials_in_all_catalogs_and_search_title_with_column_ordering() {
    $this->setup_config();

    $test_query = 'for searching title';
    $test_column = 'id_catalog';
    $test_order = 'desc';
    $response = $this->withoutMiddleware()
      ->get(
        '/api/partials?query=' . $test_query
          . '&column=' . $test_column
          . '&order=' . $test_order
      );

    $response->assertStatus(200)
      ->assertJsonCount(4, 'data')
      ->assertJsonStructure([
        'data' => [[
          'uuid',
          'catalog',
          'title',
          'priority',
        ]]
      ]);

    $expected = [
      'uuid' => $this->partial_uuid_2,
      'catalog' => $this->catalog_season_1 . ' ' . $this->catalog_year_1,
      'title' => $this->partial_title_2,
    ];

    $this->assertArrayIsEqualToArrayOnlyConsideringListOfKeys(
      $expected,
      $response['data'][0],
      array_keys($expected),
    );

    $expected = [
      'uuid' => $this->partial_uuid_1,
      'catalog' => $this->catalog_season_1 . ' ' . $this->catalog_year_1,
      'title' => $this->partial_title_1,
    ];

    $this->assertArrayIsEqualToArrayOnlyConsideringListOfKeys(
      $expected,
      $response['data'][1],
      array_keys($expected),
    );

    $expected = [
      'uuid' => $this->partial_uuid_3,
      'catalog' => $this->catalog_season_2 . ' ' . $this->catalog_year_2,
      'title' => $this->partial_title_3,
    ];

    $this->assertArrayIsEqualToArrayOnlyConsideringListOfKeys(
      $expected,
      $response['data'][2],
      array_keys($expected),
    );
  }

  public function test_should_not_get_partials_in_all_catalogs_on_form_errors() {
    $test_column = 'invalid_column';
    $test_order = 'invalid_order';
    $test_page = 'string';
    $test_limit = 'string';

    $response = $this->withoutMiddleware()
      ->get(
        '/api/partials?column=' . $test_column
          . '&order=' . $test_order
          . '&page=' . $test_page
          . '&limit=' . $test_limit
      );

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['column', 'order', 'page', 'limit']]);

    $test_column = 1;
    $test_page = -1;
    $test_limit = -1;

    $response = $this->withoutMiddleware()
      ->get(
        '/api/partials?column=' . $test_column
          . '&page=' . $test_page
          . '&limit=' . $test_limit
      );

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['column', 'page', 'limit']]);
  }

  public function test_should_get_partial_successfully() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->get('/api/partials/' . $this->partial_uuid_1);

    $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
          'uuid',
          'title',
          'priority',
          'idPriority',
          'idCatalog',
        ],
      ]);

    $priority = Priority::where('priority', 'High')->first();

    $this->assertEquals($this->partial_uuid_1, $response['data']['uuid']);
    $this->assertEquals($this->partial_title_1, $response['data']['title']);
    $this->assertEquals($priority->priority, $response['data']['priority']);
    $this->assertEquals($priority->id, $response['data']['idPriority']);
    $this->assertEquals($this->catalog_uuid_1, $response['data']['idCatalog']);
  }

  public function test_should_not_get_partial_when_using_id_instead_of_uuid() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->get('/api/partials/' . $this->partial_id_1);

    $response->assertStatus(404);
  }

  public function test_should_not_get_non_existent_partial() {
    $invalid_id = 'aaaaaaaa-1234-1234-1234-aaaaaaaa1234';

    $response = $this->withoutMiddleware()->get('/api/partials/' . $invalid_id);

    $response->assertStatus(404);

    $invalid_id = -1;

    $response = $this->withoutMiddleware()->get('/api/partials/' . $invalid_id);

    $response->assertStatus(404);
  }

  public function test_should_add_partial_successfully() {
    $this->setup_config();

    $test_title = 'sample testing partial title';
    $test_id_catalog = $this->catalog_uuid_1;
    $test_id_priority = Priority::where('priority', 'High')->first()->id;

    $response = $this->withoutMiddleware()->post('/api/partials', [
      'title' => $test_title,
      'id_catalog' => $test_id_catalog,
      'id_priority' => $test_id_priority,
    ]);

    $response->assertStatus(200);

    $data = Partial::where('title', $test_title)
      ->where('id_catalog', $this->catalog_id_1)
      ->where('id_priority', $test_id_priority)
      ->first();

    $actual = $data->toArray();

    $this->assertNotNull($actual);
    $this->assertNotNull($actual['uuid']);
    $this->assertEquals($test_title, $actual['title']);
    $this->assertEquals($this->catalog_id_1, $actual['id_catalog']);
    $this->assertEquals($test_id_priority, $actual['id_priority']);
  }

  public function test_should_not_add_partial_on_form_errors() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->post('/api/partials');

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['title', 'id_catalog', 'id_priority']]);

    $test_title = rand_str(256 + 1);
    $test_id_catalog = 'invalid catalog';
    $test_id_priority = 'string';

    $response = $this->withoutMiddleware()->post('/api/partials', [
      'title' => $test_title,
      'id_catalog' => $test_id_catalog,
      'id_priority' => $test_id_priority,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['title', 'id_catalog', 'id_priority']]);

    $test_valid_title = rand_str(256);

    $test_id_catalog = 'd459e6c1-60a0-48dc-b025-4834bbfe66af'; // non-existent catalog
    $test_id_priority = 100; // invalid id

    $response = $this->withoutMiddleware()->post('/api/partials', [
      'title' => $test_valid_title,
      'id_catalog' => $test_id_catalog,
      'id_priority' => $test_id_priority,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['id_catalog', 'id_priority']]);
  }

  public function test_should_edit_partial_successfully() {
    $this->setup_config();

    $test_title = 'sample testing partial title';
    $test_id_catalog = $this->catalog_uuid_1;
    $test_id_priority = Priority::where('priority', 'Low')->first()->id;

    $response = $this->withoutMiddleware()->put('/api/partials/' . $this->partial_uuid_1, [
      'title' => $test_title,
      'id_catalog' => $test_id_catalog,
      'id_priority' => $test_id_priority,
    ]);

    $response->assertStatus(200);

    $data = Partial::where('title', $test_title)
      ->where('id_catalog', $this->catalog_id_1)
      ->where('id_priority', $test_id_priority)
      ->first();

    $actual = $data->toArray();

    $this->assertNotNull($actual);
    $this->assertNotNull($actual['uuid']);
    $this->assertEquals($test_title, $actual['title']);
    $this->assertEquals($this->catalog_id_1, $actual['id_catalog']);
    $this->assertEquals($test_id_priority, $actual['id_priority']);
  }

  public function test_should_not_edit_partial_when_id_is_used_instead_of_uuid() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->put('/api/partials/' . $this->partial_id_1);

    $response->assertStatus(404);
  }

  public function test_should_not_edit_partial_on_form_errors() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->put('/api/partials/' . $this->partial_uuid_1);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['title', 'id_catalog', 'id_priority']]);

    $test_title = rand_str(256 + 1);
    $test_id_catalog = 'invalid catalog';
    $test_id_priority = 'string';

    $response = $this->withoutMiddleware()->put('/api/partials/' . $this->partial_uuid_1, [
      'title' => $test_title,
      'id_catalog' => $test_id_catalog,
      'id_priority' => $test_id_priority,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['title', 'id_catalog', 'id_priority']]);

    $test_valid_title = rand_str(256);

    $test_id_catalog = 'd459e6c1-60a0-48dc-b025-4834bbfe66af'; // non-existent catalog
    $test_id_priority = 100; // invalid id

    $response = $this->withoutMiddleware()->put('/api/partials/' . $this->partial_uuid_1, [
      'title' => $test_valid_title,
      'id_catalog' => $test_id_catalog,
      'id_priority' => $test_id_priority,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['id_catalog', 'id_priority']]);
  }

  public function test_should_delete_partial_successfully() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->delete('/api/partials/' . $this->partial_uuid_1);

    $response->assertStatus(200);

    $actual = Partial::where('uuid', $this->partial_uuid_1)->first();

    $this->assertNull($actual);
  }

  public function test_should_not_delete_partial_when_id_is_used_instead_of_uuid() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->delete('/api/partials/' . $this->partial_id_1);

    $response->assertStatus(404);
  }

  public function test_should_not_delete_non_existent_partial() {
    $invalid_id = 'aaaaaaaa-1234-1234-1234-aaaaaaaa1234';

    $response = $this->withoutMiddleware()->delete('/api/partials/' . $invalid_id);

    $response->assertStatus(404);

    $invalid_id = -1;

    $response = $this->withoutMiddleware()->delete('/api/partials/' . $invalid_id);

    $response->assertStatus(404);
  }

  public function test_should_add_multiple_partials_successfully() {
    $test_data_raw = [
      'low' => ['Testing Title Low 1'],
      'normal' => ['Testing Title Normal 1', 'Testing Title Normal 2', 'Testing Title Normal 3'],
      'high' => ['Testing Title High 1', 'Testing Title High 2'],
    ];

    $test_data = $this->stringify_multi_data($test_data_raw);
    $test_season = 'Spring';
    $test_year = 2090;

    $response = $this->withoutMiddleware()->post('/api/partials/multi', [
      'data' => $test_data,
      'season' => $test_season,
      'year' => $test_year,
    ]);

    $response->assertStatus(200)
      ->assertJsonStructure(['data' => ['accepted', 'total']]);

    $catalog = Catalog::where('season', $test_season)
      ->where('year', $test_year)
      ->first();

    $priority = Priority::all();
    $priority_high_id = $priority->where('priority', 'High')->first()->id;
    $priority_normal_id = $priority->where('priority', 'Normal')->first()->id;
    $priority_low_id = $priority->where('priority', 'Low')->first()->id;

    $actual = Partial::where('id_catalog', $catalog->id)
      ->get();

    $actual_high = $actual->filter(function ($value) use ($priority_high_id) {
      return ($value['id_priority'] === $priority_high_id);
    })->toArray();

    $actual_normal = $actual->filter(function ($value) use ($priority_normal_id) {
      return ($value['id_priority'] === $priority_normal_id);
    })->toArray();

    $actual_low = $actual->filter(function ($value) use ($priority_low_id) {
      return ($value['id_priority'] === $priority_low_id);
    })->toArray();

    $this->assertNotNull($actual_high);
    $this->assertCount(count($test_data_raw['high']), $actual_high);

    foreach ($actual_high as $value) {
      $this->assertNotNull($value['uuid']);
      $this->assertContains($value['title'], $test_data_raw['high']);
    }

    $this->assertNotNull($actual_normal);
    $this->assertCount(count($test_data_raw['normal']), $actual_normal);

    foreach ($actual_normal as $value) {
      $this->assertNotNull($value['uuid']);
      $this->assertContains($value['title'], $test_data_raw['normal']);
    }

    $this->assertNotNull($actual_low);
    $this->assertCount(count($test_data_raw['low']), $actual_low);

    foreach ($actual_low as $value) {
      $this->assertNotNull($value['uuid']);
      $this->assertContains($value['title'], $test_data_raw['low']);
    }
  }

  public function test_should_not_add_multiple_partials_when_passing_invalid_data() {
    $test_data = 'invalid string';
    $test_valid_season = 'Spring';
    $test_valid_year = 2090;

    $response = $this->withoutMiddleware()->post('/api/partials/multi', [
      'data' => $test_data,
      'season' => $test_valid_season,
      'year' => $test_valid_year,
    ]);

    $response->assertStatus(400);
  }

  public function test_should_not_add_multiple_partials_on_form_errors() {
    $test_valid_data = '';
    $test_valid_season = 'Spring';

    $response = $this->withoutMiddleware()->post('/api/partials/multi');

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['data', 'season', 'year']]);

    $test_season = 'invalid season';
    $test_year = 3000;

    $response = $this->withoutMiddleware()->post('/api/partials/multi', [
      'data' => $test_valid_data,
      'season' => $test_season,
      'year' => $test_year,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['season', 'year']]);

    $test_year = 1899;

    $response = $this->withoutMiddleware()->post('/api/partials/multi', [
      'data' => $test_valid_data,
      'season' => $test_valid_season,
      'year' => $test_year,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['year']]);

    $test_year = 'string';

    $response = $this->withoutMiddleware()->post('/api/partials/multi', [
      'data' => $test_valid_data,
      'season' => $test_valid_season,
      'year' => $test_year,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['year']]);
  }

  public function test_should_edit_multiple_partials_successfully() {
    $this->setup_config();

    $test_data_raw = [
      'low' => ['Testing Title Low 1'],
      'normal' => ['Testing Title Normal 1', 'Testing Title Normal 2', 'Testing Title Normal 3'],
      'high' => ['Testing Title High 1', 'Testing Title High 2'],
    ];

    $test_data = $this->stringify_multi_data($test_data_raw);
    $test_season = 'Spring';
    $test_year = 2090;

    $response = $this->withoutMiddleware()->put('/api/partials/multi/' . $this->catalog_uuid_1, [
      'data' => $test_data,
      'season' => $test_season,
      'year' => $test_year,
    ]);

    $response->assertStatus(200)
      ->assertJsonStructure(['data' => ['accepted', 'total']]);

    $catalog = Catalog::where('season', $test_season)
      ->where('year', $test_year)
      ->first();

    $this->assertEquals($test_season, $catalog->season);
    $this->assertEquals($test_year, $catalog->year);

    $priority = Priority::all();
    $priority_high_id = $priority->where('priority', 'High')->first()->id;
    $priority_normal_id = $priority->where('priority', 'Normal')->first()->id;
    $priority_low_id = $priority->where('priority', 'Low')->first()->id;

    $actual = Partial::where('id_catalog', $catalog->id)
      ->get();

    $actual_high = $actual->filter(function ($value) use ($priority_high_id) {
      return ($value['id_priority'] === $priority_high_id);
    })->toArray();

    $actual_normal = $actual->filter(function ($value) use ($priority_normal_id) {
      return ($value['id_priority'] === $priority_normal_id);
    })->toArray();

    $actual_low = $actual->filter(function ($value) use ($priority_low_id) {
      return ($value['id_priority'] === $priority_low_id);
    })->toArray();

    $this->assertNotNull($actual_high);
    $this->assertCount(count($test_data_raw['high']), $actual_high);

    foreach ($actual_high as $value) {
      $this->assertNotNull($value['uuid']);
      $this->assertContains($value['title'], $test_data_raw['high']);
    }

    $this->assertNotNull($actual_normal);
    $this->assertCount(count($test_data_raw['normal']), $actual_normal);

    foreach ($actual_normal as $value) {
      $this->assertNotNull($value['uuid']);
      $this->assertContains($value['title'], $test_data_raw['normal']);
    }

    $this->assertNotNull($actual_low);
    $this->assertCount(count($test_data_raw['low']), $actual_low);

    foreach ($actual_low as $value) {
      $this->assertNotNull($value['uuid']);
      $this->assertContains($value['title'], $test_data_raw['low']);
    }
  }

  public function test_should_not_edit_multiple_partials_when_passing_invalid_data() {
    $this->setup_config();

    $test_data = 'invalid string';
    $test_valid_season = 'Spring';
    $test_valid_year = 2090;

    $response = $this->withoutMiddleware()->put('/api/partials/multi/' . $this->catalog_uuid_1, [
      'data' => $test_data,
      'season' => $test_valid_season,
      'year' => $test_valid_year,
    ]);

    $response->assertStatus(400);
  }

  public function test_should_not_edit_multiple_partials_when_catalog_id_is_used_instead_of_uuid() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->put('/api/partials/multi/' . $this->catalog_id_1);

    $response->assertStatus(404);
  }

  public function test_should_not_edit_multiple_partials_on_form_errors() {
    $this->setup_config();

    $test_valid_data = '';
    $test_valid_season = 'Spring';

    $response = $this->withoutMiddleware()->put('/api/partials/multi/' . $this->catalog_uuid_1);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['data', 'season', 'year']]);

    $test_season = 'invalid season';
    $test_year = 3000;

    $response = $this->withoutMiddleware()->put('/api/partials/multi/' . $this->catalog_uuid_1, [
      'data' => $test_valid_data,
      'season' => $test_season,
      'year' => $test_year,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['season', 'year']]);

    $test_year = 1899;

    $response = $this->withoutMiddleware()->put('/api/partials/multi/' . $this->catalog_uuid_1, [
      'data' => $test_valid_data,
      'season' => $test_valid_season,
      'year' => $test_year,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['year']]);

    $test_year = 'string';

    $response = $this->withoutMiddleware()->put('/api/partials/multi/' . $this->catalog_uuid_1, [
      'data' => $test_valid_data,
      'season' => $test_valid_season,
      'year' => $test_year,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['year']]);
  }
}
