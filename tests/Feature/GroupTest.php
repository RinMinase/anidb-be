<?php

namespace Tests\Feature;

use Tests\BaseTestCase;

use App\Models\Group;

class GroupTest extends BaseTestCase {

  private $group_id = 99999;
  private $group_uuid = '8eab429d-edc7-4c11-a94c-30903dd65dc6';

  private function setup_config() {
    // Clearing possible duplicate data
    $this->setup_clear();

    Group::insert([
      'id' => $this->group_id,
      'uuid' => $this->group_uuid,
      'name' => 'test group',
      'created_at' => '2020-01-01 13:00:00',
      'updated_at' => '2020-01-01 13:00:00',
    ]);
  }

  private function setup_clear() {
    Group::where('id', $this->group_id)->forceDelete();
  }

  public function test_should_get_all_data() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->get('/api/groups');

    $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [[
          'uuid',
          'name',
        ]],
      ]);

    $this->setup_clear();
  }

  public function test_should_not_get_all_data_when_not_authorized() {
    $response = $this->get('/api/groups');

    $response->assertStatus(401)
      ->assertJson(['message' => 'Unauthorized']);
  }

  public function test_should_get_names_of_all_groups_successfully() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->get('/api/groups/names');

    $response->assertStatus(200)
      ->assertJsonStructure(['data' => [[]]]);

    $expected_count = 1;
    $this->assertGreaterThanOrEqual($expected_count, $response['data']);

    $this->setup_clear();
  }

  public function test_should_add_data_successfully() {
    $test_name = 'sample group name';

    // Clearing possible duplicate data
    Group::where('name', $test_name)
      ->delete();

    $response = $this->withoutMiddleware()->post('/api/groups', [
      'name' => $test_name,
    ]);

    $response->assertStatus(200);

    $data = Group::where('name', $test_name)
      ->first();

    $actual = $data->toArray();

    $this->assertNotNull($actual);
    $this->assertNotNull($actual['uuid']);
    $this->assertSame($test_name, $actual['name']);

    $data->delete();
  }

  public function test_should_not_add_data_on_form_errors() {
    $test_name = 'test name BIOEIZPMPHWSCUQBTFVOGKXVMGLLSDUUBIOEIZPMPHWSCUQBTFVOGKX';

    $response = $this->withoutMiddleware()->post('/api/groups', [
      'name' => $test_name,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['name']]);
  }

  public function test_should_edit_data_successfully() {
    $this->setup_config();

    $test_name = 'new group name';

    $response = $this->withoutMiddleware()
      ->put('/api/groups/' . $this->group_uuid, [
        'name' => $test_name,
      ]);

    $response->assertStatus(200);

    $actual = Group::where('uuid', $this->group_uuid)
      ->first()
      ->toArray();

    $this->assertSame($test_name, $actual['name']);

    $this->setup_clear();
  }

  public function test_should_not_edit_data_when_id_is_used_instead_of_uuid() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->put('/api/groups/' . $this->group_id);

    $response->assertStatus(404);

    $this->setup_clear();
  }

  public function test_should_not_edit_data_on_form_errors() {
    $this->setup_config();

    $test_name = 'test new name BIOEIZPMPHUBTFVOGKXVMGLLSDUUBIOEIZPMPHWSCUQBTFVOGKX';

    $response = $this->withoutMiddleware()->post('/api/groups', [
      'name' => $test_name,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['name']]);

    $response = $this->withoutMiddleware()->post('/api/groups');

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['name']]);

    $this->setup_clear();
  }

  public function test_should_delete_data_successfully() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->delete('/api/groups/' . $this->group_uuid);

    $response->assertStatus(200);

    $actual = Group::where('uuid', $this->group_uuid)->first();

    $this->assertNull($actual);

    $this->setup_clear();
  }

  public function test_should_not_delete_data_when_id_is_used_instead_of_uuid() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->delete('/api/groups/' . $this->group_id);

    $response->assertStatus(404);

    $this->setup_clear();
  }

  public function test_should_not_delete_non_existent_data() {
    $this->setup_config();

    $invalid_id = -1;

    $response = $this->withoutMiddleware()->delete('/api/groups/' . $invalid_id);

    $response->assertStatus(404);

    $this->setup_clear();
  }
}
