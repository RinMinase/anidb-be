<?php

namespace Tests\Feature;

use Tests\BaseTestCase;

use App\Models\Catalog;
use App\Models\Partial;
use App\Models\Priority;

class CatalogTest extends BaseTestCase {

  private $catalog_id = 99999;
  private $catalog_uuid = 'ee7163e8-0be6-4bdf-85a7-eba3af21dfbe';

  private $partial_id = 99999;
  private $partial_uuid = '0f98f5c0-f493-41a1-a184-d43782223d47';

  private function setup_config() {
    // Clearing possible duplicate data
    $this->setup_clear();

    $id_priority = Priority::where('priority', 'High')->first()->id;

    Catalog::insert([
      'id' => $this->catalog_id,
      'uuid' => $this->catalog_uuid,
      'year' => 2050,
      'season' => 'Spring',
      'created_at' => '2020-01-01 13:00:00',
      'updated_at' => '2020-01-01 13:00:00',
    ]);

    Partial::insert([
      'id' => $this->partial_id,
      'uuid' => $this->partial_uuid,
      'title' => 'sample test partial title',
      'id_catalog' => $this->catalog_id,
      'id_priority' => $id_priority,
      'created_at' => '2020-01-01 13:00:00',
      'updated_at' => '2020-01-01 13:00:00',
    ]);
  }

  private function setup_clear() {
    Catalog::where('id', $this->catalog_id)->forceDelete();
    Partial::where('id', $this->partial_id)->forceDelete();
  }

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

  public function test_should_get_all_catalogs() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->get('/api/catalogs');

    $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [[
          'id',
          'year',
          'season',
        ]],
      ]);

    $this->setup_clear();
  }

  public function test_should_not_get_all_catalogs_when_not_authorized() {
    $response = $this->get('/api/catalogs');

    $response->assertStatus(401)
      ->assertJson(['message' => 'Unauthorized']);
  }

  public function test_should_add_catalog_successfully() {
    $test_year = 2051;
    $test_season = 'Spring';

    // Clearing possible duplicate data
    Catalog::where('year', $test_year)
      ->where('season', $test_season)
      ->delete();

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
    $this->assertSame($test_year, $actual['year']);
    $this->assertSame($test_season, $actual['season']);

    $data->delete();
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
    $test_season = 'Spring';

    $response = $this->withoutMiddleware()->put('/api/catalogs/' . $this->catalog_uuid, [
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
    $this->assertSame($test_year, $actual['year']);
    $this->assertSame($test_season, $actual['season']);

    $data->delete();

    $this->setup_clear();
  }

  public function test_should_not_edit_catalog_on_form_errors() {
    $response = $this->withoutMiddleware()->put('/api/catalogs/' . $this->catalog_uuid);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['year', 'season']]);

    $test_year = 3000;
    $test_season = 'Invalid';

    $response = $this->withoutMiddleware()->put('/api/catalogs/' . $this->catalog_uuid, [
      'year' => $test_year,
      'season' => $test_season,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['year', 'season']]);

    $test_valid_season = 'Spring';

    $test_year = 3000;

    $response = $this->withoutMiddleware()->put('/api/catalogs/' . $this->catalog_uuid, [
      'year' => $test_year,
      'season' => $test_valid_season,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['year']]);

    $test_year = 1899;

    $response = $this->withoutMiddleware()->put('/api/catalogs/' . $this->catalog_uuid, [
      'year' => $test_year,
      'season' => $test_valid_season,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['year']]);

    $test_year = 'string';

    $response = $this->withoutMiddleware()->put('/api/catalogs/' . $this->catalog_uuid, [
      'year' => $test_year,
      'season' => $test_valid_season,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['year']]);
  }

  public function test_should_not_edit_catalog_when_id_is_used_instead_of_uuid() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->put('/api/catalogs/' . $this->catalog_id);

    $response->assertStatus(404);

    $this->setup_clear();
  }

  public function test_should_delete_catalog_successfully() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->delete('/api/catalogs/' . $this->catalog_uuid);

    $response->assertStatus(200);

    $actualCatalog = Catalog::where('uuid', $this->catalog_uuid)->first();
    $actualPartials = Partial::where('id_catalog', $this->catalog_id)->get();

    $this->assertNull($actualCatalog);
    $this->assertCount(0, $actualPartials);

    $this->setup_clear();
  }

  public function test_should_not_delete_catalog_when_id_is_used_instead_of_uuid() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->delete('/api/catalogs/' . $this->catalog_id);

    $response->assertStatus(404);

    $this->setup_clear();
  }

  public function test_should_not_delete_non_existent_catalog() {
    $invalid_id = -1;

    $response = $this->withoutMiddleware()->delete('/api/catalogs/' . $invalid_id);

    $response->assertStatus(404);
  }

  public function test_should_get_partials_in_catalog_successfully() {
    $this->setup_config();

    $response = $this->withoutMiddleware()
      ->get('/api/catalogs/' . $this->catalog_uuid . '/partials');

    $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [[
          'id',
          'title',
          'priority',
        ]],
        'stats' => [
          'id',
          'year',
          'season',
        ],
      ]);

    $this->setup_clear();
  }

  public function test_should_not_get_partials_when_catalog_id_is_used_instead_of_uuid() {
    $this->setup_config();

    $response = $this->withoutMiddleware()
      ->get('/api/catalogs/' . $this->catalog_id . '/partials');

    $response->assertStatus(404);

    $this->setup_clear();
  }

  public function test_should_not_get_partials_in_invalid_catalog() {
    $invalid_id = -1;

    $response = $this->withoutMiddleware()->get('/api/catalogs/' . $invalid_id . '/partials');

    $response->assertStatus(404);
  }

  public function test_should_get_partial_successfully() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->get('/api/partials/' . $this->partial_uuid);

    $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
          'uuid',
          'title',
          'priority',
          'idPriority',
          'idCatalog',
          'title',
        ],
      ]);

    $this->setup_clear();
  }

  public function test_should_not_get_partial_when_using_id_instead_of_uuid() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->get('/api/partials/' . $this->partial_id);

    $response->assertStatus(404);

    $this->setup_clear();
  }

  public function test_should_not_get_non_existent_partial() {
    $invalid_id = -1;

    $response = $this->withoutMiddleware()->get('/api/partials/' . $invalid_id);

    $response->assertStatus(404);
  }

  public function test_should_add_partial_successfully() {
    // Catalog needs to be setup
    $this->setup_config();

    $test_title = 'sample testing partial title';
    $test_id_catalog = $this->catalog_uuid;
    $test_id_priority = Priority::where('priority', 'High')->first()->id;

    // Clearing possible duplicate data
    Partial::where('title', $test_title)
      ->where('id_catalog', $this->catalog_id)
      ->where('id_priority', $test_id_priority)
      ->delete();

    $response = $this->withoutMiddleware()->post('/api/partials', [
      'title' => $test_title,
      'id_catalog' => $test_id_catalog,
      'id_priority' => $test_id_priority,
    ]);

    $response->assertStatus(200);

    $data = Partial::where('title', $test_title)
      ->where('id_catalog', $this->catalog_id)
      ->where('id_priority', $test_id_priority)
      ->first();

    $actual = $data->toArray();

    $this->assertNotNull($actual);
    $this->assertNotNull($actual['uuid']);
    $this->assertSame($test_title, $actual['title']);
    $this->assertSame($this->catalog_id, $actual['id_catalog']);
    $this->assertSame($test_id_priority, $actual['id_priority']);

    $data->delete();

    $this->setup_clear();
  }

  public function test_should_not_add_partial_on_form_errors() {
    // Catalog needs to be setup
    $this->setup_config();

    $response = $this->withoutMiddleware()->post('/api/partials');

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['title', 'id_catalog', 'id_priority']]);

    $test_title = 'sample testing partial title ' . rand_str(256);
    $test_id_catalog = 'invalid catalog';
    $test_id_priority = 'string';

    $response = $this->withoutMiddleware()->post('/api/partials', [
      'title' => $test_title,
      'id_catalog' => $test_id_catalog,
      'id_priority' => $test_id_priority,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['title', 'id_catalog', 'id_priority']]);

    $test_valid_title = 'sample testing partial title';

    $test_id_catalog = 'd459e6c1-60a0-48dc-b025-4834bbfe66af'; // non-existent catalog
    $test_id_priority = 100; // invalid id

    $response = $this->withoutMiddleware()->post('/api/partials', [
      'title' => $test_valid_title,
      'id_catalog' => $test_id_catalog,
      'id_priority' => $test_id_priority,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['id_catalog', 'id_priority']]);

    $this->setup_clear();
  }

  public function test_should_edit_partial_successfully() {
    $this->setup_config();

    $test_title = 'sample testing partial title';
    $test_id_catalog = $this->catalog_uuid;
    $test_id_priority = Priority::where('priority', 'Low')->first()->id;

    // Clearing possible duplicate data
    Partial::where('title', $test_title)
      ->where('id_catalog', $this->catalog_id)
      ->where('id_priority', $test_id_priority)
      ->delete();

    $response = $this->withoutMiddleware()->put('/api/partials/' . $this->partial_uuid, [
      'title' => $test_title,
      'id_catalog' => $test_id_catalog,
      'id_priority' => $test_id_priority,
    ]);

    $response->assertStatus(200);

    $data = Partial::where('title', $test_title)
      ->where('id_catalog', $this->catalog_id)
      ->where('id_priority', $test_id_priority)
      ->first();

    $actual = $data->toArray();

    $this->assertNotNull($actual);
    $this->assertNotNull($actual['uuid']);
    $this->assertSame($test_title, $actual['title']);
    $this->assertSame($this->catalog_id, $actual['id_catalog']);
    $this->assertSame($test_id_priority, $actual['id_priority']);

    $data->delete();

    $this->setup_clear();
  }

  public function test_should_not_edit_partial_when_id_is_used_instead_of_uuid() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->put('/api/partials/' . $this->partial_id);

    $response->assertStatus(404);

    $this->setup_clear();
  }

  public function test_should_not_edit_partial_on_form_errors() {
    // Catalog needs to be setup
    $this->setup_config();

    $response = $this->withoutMiddleware()->put('/api/partials/' . $this->partial_uuid);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['title', 'id_catalog', 'id_priority']]);

    $test_title = 'sample testing partial title ' . rand_str(256);
    $test_id_catalog = 'invalid catalog';
    $test_id_priority = 'string';

    $response = $this->withoutMiddleware()->put('/api/partials/' . $this->partial_uuid, [
      'title' => $test_title,
      'id_catalog' => $test_id_catalog,
      'id_priority' => $test_id_priority,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['title', 'id_catalog', 'id_priority']]);

    $test_valid_title = 'sample testing partial title';

    $test_id_catalog = 'd459e6c1-60a0-48dc-b025-4834bbfe66af'; // non-existent catalog
    $test_id_priority = 100; // invalid id

    $response = $this->withoutMiddleware()->put('/api/partials/' . $this->partial_uuid, [
      'title' => $test_valid_title,
      'id_catalog' => $test_id_catalog,
      'id_priority' => $test_id_priority,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['id_catalog', 'id_priority']]);

    $this->setup_clear();
  }

  public function test_should_delete_partial_successfully() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->delete('/api/partials/' . $this->partial_uuid);

    $response->assertStatus(200);

    $actual = Partial::where('uuid', $this->partial_uuid)->first();

    $this->assertNull($actual);

    $this->setup_clear();
  }

  public function test_should_not_delete_partial_when_id_is_used_instead_of_uuid() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->delete('/api/partials/' . $this->partial_id);

    $response->assertStatus(404);

    $this->setup_clear();
  }

  public function test_should_not_delete_non_existent_partial() {
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

    // Delete existing
    Catalog::where('season', $test_season)
      ->where('year', $test_year)
      ->forceDelete();

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

    $catalog->delete();
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

    $response = $this->withoutMiddleware()->put('/api/partials/multi/' . $this->catalog_uuid, [
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

    $catalog->delete();

    $this->setup_clear();
  }

  public function test_should_not_edit_multiple_partials_when_passing_invalid_data() {
    $this->setup_config();

    $test_data = 'invalid string';
    $test_valid_season = 'Spring';
    $test_valid_year = 2090;

    $response = $this->withoutMiddleware()->put('/api/partials/multi/' . $this->catalog_uuid, [
      'data' => $test_data,
      'season' => $test_valid_season,
      'year' => $test_valid_year,
    ]);

    $response->assertStatus(400);

    $this->setup_clear();
  }

  public function test_should_not_edit_multiple_partials_when_catalog_id_is_used_instead_of_uuid() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->put('/api/partials/multi/' . $this->catalog_id);

    $response->assertStatus(404);

    $this->setup_clear();
  }

  public function test_should_not_edit_multiple_partials_on_form_errors() {
    $this->setup_config();

    $test_valid_data = '';
    $test_valid_season = 'Spring';

    $response = $this->withoutMiddleware()->put('/api/partials/multi/' . $this->catalog_uuid);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['data', 'season', 'year']]);

    $test_season = 'invalid season';
    $test_year = 3000;

    $response = $this->withoutMiddleware()->put('/api/partials/multi/' . $this->catalog_uuid, [
      'data' => $test_valid_data,
      'season' => $test_season,
      'year' => $test_year,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['season', 'year']]);

    $test_year = 1899;

    $response = $this->withoutMiddleware()->put('/api/partials/multi/' . $this->catalog_uuid, [
      'data' => $test_valid_data,
      'season' => $test_valid_season,
      'year' => $test_year,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['year']]);

    $test_year = 'string';

    $response = $this->withoutMiddleware()->put('/api/partials/multi/' . $this->catalog_uuid, [
      'data' => $test_valid_data,
      'season' => $test_valid_season,
      'year' => $test_year,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['year']]);

    $this->setup_clear();
  }
}
