<?php

namespace Tests\Feature;

use Tests\BaseTestCase;

use App\Models\Group;

class GroupTest extends BaseTestCase {

  // Backup related variables
  private $group_backup = null;

  // Class variables
  private $group_id = 99999;
  private $group_uuid = '8eab429d-edc7-4c11-a94c-30903dd65dc6';
  private $group_name = 'test group';

  // Backup related tables
  private function setup_backup() {
    $hidden_columns = ['id', 'created_at', 'updated_at'];
    $this->group_backup = Group::all()->makeVisible($hidden_columns)->toArray();
  }

  // Restore related tables
  private function setup_restore() {
    Group::truncate();
    Group::insert($this->group_backup);
    Group::refreshAutoIncrements();
  }

  // Setup data for testing
  private function setup_config() {
    Group::truncate();

    Group::insert([
      'id' => $this->group_id,
      'uuid' => $this->group_uuid,
      'name' => $this->group_name,
      'created_at' => '2020-01-01 13:00:00',
      'updated_at' => '2020-01-01 13:00:00',
    ]);
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

    $response = $this->withoutMiddleware()->get('/api/groups');

    $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [[
          'uuid',
          'name',
        ]],
      ]);

    $expected = [[
      'uuid' => $this->group_uuid,
      'name' => $this->group_name,
    ]];

    $this->assertEquals($expected, $response['data']);
  }

  public function test_should_get_names_of_all_groups_successfully() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->get('/api/groups/names');


    $response->assertStatus(200)
      ->assertJsonStructure(['data' => [[]]]);

    $expected_count = 1;
    $expected = [
      $this->group_name,
    ];

    $this->assertCount($expected_count, $response['data']);
    $this->assertEquals($expected, $response['data']);
  }

  public function test_should_add_data_successfully() {
    $test_name = 'sample group name';

    $response = $this->withoutMiddleware()->post('/api/groups', [
      'name' => $test_name,
    ]);

    $response->assertStatus(200);

    $data = Group::where('name', $test_name)
      ->first();

    $actual = $data->toArray();

    $this->assertModelExists($data);
    $this->assertNotNull($actual['uuid']);
    $this->assertEquals($test_name, $actual['name']);
  }

  public function test_should_not_add_data_on_form_errors() {
    $response = $this->withoutMiddleware()->post('/api/groups');

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['name']]);

    $test_name = rand_str(64 + 1);

    $response = $this->withoutMiddleware()->post('/api/groups', [
      'name' => $test_name,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['name']]);

    $test_name = -1;

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

    $this->assertEquals($test_name, $actual['name']);
  }

  public function test_should_not_edit_data_on_form_errors() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->put('/api/groups/' . $this->group_uuid);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['name']]);

    $test_name = rand_str(64 + 1);

    $response = $this->withoutMiddleware()->put('/api/groups/' . $this->group_uuid, [
      'name' => $test_name,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['name']]);

    $test_name = -1;

    $response = $this->withoutMiddleware()->put('/api/groups/' . $this->group_uuid, [
      'name' => $test_name,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['name']]);
  }

  public function test_should_not_edit_data_when_id_is_used_instead_of_uuid() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->put('/api/groups/' . $this->group_id);

    $response->assertStatus(404);
  }

  public function test_should_delete_data_successfully() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->delete('/api/groups/' . $this->group_uuid);

    $response->assertStatus(200);

    $actual = Group::where('uuid', $this->group_uuid)->first();

    $this->assertNull($actual);
  }

  public function test_should_not_delete_data_when_id_is_used_instead_of_uuid() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->delete('/api/groups/' . $this->group_id);

    $response->assertStatus(404);
  }

  public function test_should_not_delete_non_existent_data() {
    $invalid_id = 'aaaaaaaa-1234-1234-1234-aaaaaaaa1234';

    $response = $this->withoutMiddleware()->delete('/api/groups/' . $invalid_id);

    $response->assertStatus(404);

    $invalid_id = -1;

    $response = $this->withoutMiddleware()->delete('/api/groups/' . $invalid_id);

    $response->assertStatus(404);
  }
}
