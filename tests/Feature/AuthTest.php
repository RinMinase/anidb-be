<?php

namespace Tests\Feature;

use Tests\BaseTestCase;

use Carbon\Carbon;

use App\Models\User;

class AuthTest extends BaseTestCase {

  // Backup related variables
  private $user_backup = null;

  // Class variables
  private $user_id_1 = 99999;
  private $user_id_2 = 99998;
  private $user_id_3 = 99997;

  private $user_uuid_1 = '53e2087f-5441-4d63-9991-07ced5a4e0e4';
  private $user_uuid_2 = '0329e0ca-d567-43c9-b337-dc81003e1415';
  private $user_uuid_3 = '3be3273d-b97c-41ec-8bf6-b1b7a6c83950';

  private $user_username_1 = 'testingusername';
  private $user_password_1 = 'testingpassword';
  private $user_username_2 = 'testingadminusername';
  private $user_password_2 = 'testingadminpassword';
  private $user_username_3 = 'testingusername2';
  private $user_password_3 = 'testingpassword2';

  private $user_is_admin_1 = false;
  private $user_is_admin_2 = true;
  private $user_is_admin_3 = false;

  // Backup related tables
  private function setup_backup() {
    $hidden_columns = ['id', 'password'];
    $this->user_backup = User::all()->makeVisible($hidden_columns)->toArray();
  }

  // Restore related tables
  private function setup_restore() {
    User::truncate();
    User::insert($this->user_backup);
    User::refreshAutoIncrements();
  }

  // Setup data for testing
  private function setup_config() {
    User::truncate();

    $timestamp = Carbon::now();

    $test_users = [
      [
        'id' => $this->user_id_1,
        'uuid' => $this->user_uuid_1,
        'username' => $this->user_username_1,
        'password' => bcrypt($this->user_password_1),
        'is_admin' => $this->user_is_admin_1,
        'created_at' => $timestamp,
        'updated_at' => $timestamp,
      ],
      [
        'id' => $this->user_id_2,
        'uuid' => $this->user_uuid_2,
        'username' => $this->user_username_2,
        'password' => bcrypt($this->user_password_2),
        'is_admin' => $this->user_is_admin_2,
        'created_at' => $timestamp,
        'updated_at' => $timestamp,
      ],
      [
        'id' => $this->user_id_3,
        'uuid' => $this->user_uuid_3,
        'username' => $this->user_username_3,
        'password' => bcrypt($this->user_password_3),
        'is_admin' => $this->user_is_admin_3,
        'created_at' => $timestamp,
        'updated_at' => $timestamp,
      ],
    ];

    User::insert($test_users);
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

  // Test cases
  public function test_should_login_successfully() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->post('/api/auth/login', [
      'username' => $this->user_username_1,
      'password' => $this->user_password_1,
    ]);

    $response->assertStatus(200);

    $response = $this->withoutMiddleware()->post('/api/auth/login', [
      'username' => $this->user_username_2,
      'password' => $this->user_password_2,
    ]);

    $response->assertStatus(200);
  }

  public function test_should_not_login_on_invalid_credentials() {
    $this->setup_config();

    $test_username = 'nonexistinguser';
    $test_password = 'e9597119-8452-4f2b-96d8-f2b1b1d2f158';

    $response = $this->withoutMiddleware()->post('/api/auth/login', [
      'username' => $test_username,
      'password' => $test_password,
    ]);

    $response->assertStatus(401)
      ->assertJson(['message' => 'Credentials does not match.']);

    $response = $this->withoutMiddleware()->post('/api/auth/login', [
      'username' => $this->user_username_1,
      'password' => $test_password,
    ]);

    $response->assertStatus(401)
      ->assertJson(['message' => 'Credentials does not match.']);

    $response = $this->withoutMiddleware()->post('/api/auth/login', [
      'username' => $test_username,
      'password' => $this->user_password_1,
    ]);

    $response->assertStatus(401)
      ->assertJson(['message' => 'Credentials does not match.']);
  }

  public function test_should_not_login_on_blank_email_or_password() {
    $response = $this->withoutMiddleware()->post('/api/auth/login');

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['username', 'password']]);
  }

  public function test_should_register_admin_successfully() {
    $test_username = 'testingusername';
    $test_password = 'testingpassword';

    $response = $this->withoutMiddleware()->post('/api/auth/register', [
      'username' => $test_username,
      'password' => $test_password,
      'password_confirmation' => $test_password,
      'root_password' => config('app.registration_root_password'),
    ]);

    $response->assertStatus(200);
  }

  public function test_should_not_register_admin_on_invalid_root_password() {
    $test_username = 'unittestingusername';
    $test_password = 'unittestingpassword';

    $response = $this->withoutMiddleware()->post('/api/auth/register', [
      'username' => $test_username,
      'password' => $test_password,
      'password_confirmation' => $test_password,
    ]);

    $response->assertStatus(401)->assertJsonStructure(['data' => ['root_password']]);

    $invalid_root_password = '';
    $response = $this->withoutMiddleware()->post('/api/auth/register', [
      'username' => $test_username,
      'password' => $test_password,
      'password_confirmation' => $test_password,
      'root_password' => $invalid_root_password,
    ]);

    $response->assertStatus(401)->assertJsonStructure(['data' => ['root_password']]);

    $invalid_root_password = 'invalidpassword';
    $response = $this->withoutMiddleware()->post('/api/auth/register', [
      'username' => $test_username,
      'password' => $test_password,
      'password_confirmation' => $test_password,
      'root_password' => $invalid_root_password,
    ]);

    $response->assertStatus(401)->assertJson(['message' => 'Unauthorized']);
  }

  public function test_should_not_register_admin_on_invalid_form() {
    $response = $this->withoutMiddleware()->post('/api/auth/register', [
      'root_password' => config('app.registration_root_password'),
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['username', 'password']]);


    $valid_username = 'testingusername';
    $valid_password = 'testingpassword';

    $invalid_usernames = [
      null,
      '',
      'invalid username',
      'invalid_username',
      'invalid-username',
      '123',
      'str',
      rand_str(32 + 1),
      rand_str(32 + 1, true),
    ];

    foreach ($invalid_usernames as $key => $value) {
      $response = $this->withoutMiddleware()->post('/api/auth/register', [
        'username' => $value,
        'password' => $valid_password,
        'password_confirmation' => $valid_password,
        'root_password' => config('app.registration_root_password'),
      ]);

      $this->assertArrayHasKey('username', $response['data'], 'Error on $key = ' . $key);

      $response->assertStatus(401);
    }

    $invalid_passwords = [
      null,
      '',
      'invalid password',
      'invalid_password',
      'invalid-password',
      '123',
      'str',
      rand_str(32 + 1),
      rand_str(32 + 1, true),
    ];

    foreach ($invalid_passwords as $key => $value) {
      $response = $this->withoutMiddleware()->post('/api/auth/register', [
        'username' => $valid_username,
        'password' => $value,
        'password_confirmation' => $value,
        'root_password' => config('app.registration_root_password'),
      ]);

      $this->assertArrayHasKey('password', $response['data'], 'Error on $key = ' . $key);

      $response->assertStatus(401);
    }
  }

  public function test_should_not_register_admin_on_invalid_password_confirmation() {
    $test_username = 'testingusername';
    $test_password = 'testingpassword';

    $response = $this->withoutMiddleware()->post('/api/auth/register', [
      'username' => $test_username,
      'password' => $test_password,
      'root_password' => config('app.registration_root_password'),
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['password']]);
  }

  public function test_should_logout_successfully() {
    $this->setup_config();

    $this->withoutMiddleware()->post('/api/auth/login', [
      'username' => $this->user_username_1,
      'password' => $this->user_password_1,
    ]);

    $response = $this->withoutMiddleware()->post('/api/auth/logout');

    $response->assertStatus(200);

    $this->withoutMiddleware()->post('/api/auth/login', [
      'username' => $this->user_username_2,
      'password' => $this->user_password_2,
    ]);

    $response = $this->withoutMiddleware()->post('/api/auth/logout');

    $response->assertStatus(200);
  }

  public function test_should_not_logout_on_invalid_session() {
    $response = $this->withoutMiddleware()->post('/api/auth/logout');

    $response->assertStatus(401)
      ->assertJson(['message' => 'Unauthorized']);
  }

  public function test_should_get_user_information_successfully() {
    $this->setup_config();

    $this->withoutMiddleware()->post('/api/auth/login', [
      'username' => $this->user_username_1,
      'password' => $this->user_password_1,
    ]);

    $response = $this->get('/api/auth/user');

    $response->assertStatus(200);

    $expected = [
      'uuid' => $this->user_uuid_1,
      'username' => $this->user_username_1,
      'isAdmin' => $this->user_is_admin_1,
    ];

    $this->assertArrayIsEqualToArrayOnlyConsideringListOfKeys(
      $expected,
      $response['data'],
      ['uuid', 'username', 'isAdmin']
    );
  }

  public function test_should_get_admin_user_information_successfully() {
    $this->setup_config();

    $this->withoutMiddleware()->post('/api/auth/login', [
      'username' => $this->user_username_2,
      'password' => $this->user_password_2,
    ]);

    $response = $this->get('/api/auth/user');

    $response->assertStatus(200);

    $expected = [
      'uuid' => $this->user_uuid_2,
      'username' => $this->user_username_2,
      'isAdmin' => $this->user_is_admin_2,
    ];

    $this->assertArrayIsEqualToArrayOnlyConsideringListOfKeys(
      $expected,
      $response['data'],
      ['uuid', 'username', 'isAdmin']
    );
  }

  public function test_should_get_all_non_admin_users_successfully() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->get('/api/users');


    $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [['uuid', 'username', 'createdAt', 'updatedAt']]
      ]);

    $actual_user_ids = collect($response['data'])->pluck('uuid')->toArray();

    $expected_user_ids = [
      $this->user_uuid_1,
      $this->user_uuid_3,
    ];

    $this->assertEqualsCanonicalizing($expected_user_ids, $actual_user_ids);
  }

  public function test_should_register_regular_user_successfully() {
    $test_username = 'testingusername';
    $test_password = 'testingpassword';

    $response = $this->withoutMiddleware()->post('/api/users', [
      'username' => $test_username,
      'password' => $test_password,
      'password_confirmation' => $test_password,
    ]);

    $response->assertStatus(200);
  }

  public function test_should_not_register_regular_user_on_invalid_form() {
    $response = $this->withoutMiddleware()->post('/api/users', []);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['username', 'password']]);


    $valid_username = 'testingusername';
    $valid_password = 'testingpassword';

    $invalid_usernames = [
      null,
      '',
      'invalid username',
      'invalid_username',
      'invalid-username',
      '123',
      'str',
      rand_str(32 + 1),
      rand_str(32 + 1, true),
    ];

    foreach ($invalid_usernames as $key => $value) {
      $response = $this->withoutMiddleware()->post('/api/users', [
        'username' => $value,
        'password' => $valid_password,
        'password_confirmation' => $valid_password,
      ]);

      $this->assertArrayHasKey('username', $response['data'], 'Error on $key = ' . $key);

      $response->assertStatus(401);
    }

    $invalid_passwords = [
      null,
      '',
      'invalid password',
      'invalid_password',
      'invalid-password',
      '123',
      'str',
      rand_str(32 + 1),
      rand_str(32 + 1, true),
    ];

    foreach ($invalid_passwords as $key => $value) {
      $response = $this->withoutMiddleware()->post('/api/users', [
        'username' => $valid_username,
        'password' => $value,
        'password_confirmation' => $value,
      ]);

      $this->assertArrayHasKey('password', $response['data'], 'Error on $key = ' . $key);

      $response->assertStatus(401);
    }
  }

  public function test_should_not_register_regular_user_on_invalid_password_confirmation() {
    $test_username = 'testingusername';
    $test_password = 'testingpassword';

    $response = $this->withoutMiddleware()->post('/api/users', [
      'username' => $test_username,
      'password' => $test_password,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['password']]);
  }

  public function test_should_not_register_regular_user_on_duplicate_username() {
    $this->setup_config();

    $test_username = 'testingusername';
    $test_password = 'testingpassword';

    $response = $this->withoutMiddleware()->post('/api/users', [
      'username' => $test_username,
      'password' => $test_password,
      'password_confirmation' => $test_password,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['username']]);
  }

  public function test_should_edit_any_user_type_successfully() {
    $this->setup_config();

    $test_username = 'newusername';
    $valid_password = 'testingpassword';

    $response = $this->withoutMiddleware()->put('/api/users/' . $this->user_uuid_1, [
      'username' => $test_username,
      'password' => $valid_password,
      'password_confirmation' => $valid_password,
    ]);

    $response->assertStatus(200);

    $actual = User::select('username')->where('uuid', $this->user_uuid_1)->get()->first();

    $this->assertEquals($test_username, $actual->username);
  }

  public function test_should_not_edit_any_user_type_when_id_is_used_instead_of_uuid() {
    $this->setup_config();

    $test_username = 'newusername';
    $valid_password = 'testingpassword';

    $response = $this->withoutMiddleware()->put('/api/users/' . $this->user_id_1, [
      'username' => $test_username,
      'password' => $valid_password,
      'password_confirmation' => $valid_password,
    ]);

    $response->assertStatus(404);
  }

  public function test_should_not_edit_non_existent_user() {
    $this->setup_config();

    $invalid_id = -1;
    $test_username = 'newusername';
    $valid_password = 'testingpassword';

    $response = $this->withoutMiddleware()->put('/api/users/' . $invalid_id, [
      'username' => $test_username,
      'password' => $valid_password,
      'password_confirmation' => $valid_password,
    ]);

    $response->assertStatus(404);
  }

  public function test_should_not_edit_any_user_type_on_invalid_form() {
    $this->setup_config();


    // Normal User
    $response = $this->withoutMiddleware()->put('/api/users/' . $this->user_uuid_1, []);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['username', 'password']]);


    $valid_username = 'testingusername';
    $valid_password = 'testingpassword';

    $invalid_usernames = [
      null,
      '',
      'invalid username',
      'invalid_username',
      'invalid-username',
      '123',
      'str',
      rand_str(32 + 1),
      rand_str(32 + 1, true),
    ];

    foreach ($invalid_usernames as $key => $value) {
      $response = $this->withoutMiddleware()->put('/api/users/' . $this->user_uuid_1, [
        'username' => $value,
        'password' => $valid_password,
        'password_confirmation' => $valid_password,
      ]);

      $this->assertArrayHasKey('username', $response['data'], 'Error on $key = ' . $key);

      $response->assertStatus(401);
    }

    $invalid_passwords = [
      null,
      '',
      'invalid password',
      'invalid_password',
      'invalid-password',
      '123',
      'str',
      rand_str(32 + 1),
      rand_str(32 + 1, true),
    ];

    foreach ($invalid_passwords as $key => $value) {
      $response = $this->withoutMiddleware()->put('/api/users/' . $this->user_uuid_1, [
        'username' => $valid_username,
        'password' => $value,
        'password_confirmation' => $value,
      ]);

      $this->assertArrayHasKey('password', $response['data'], 'Error on $key = ' . $key);

      $response->assertStatus(401);
    }

    // Admin User
    $response = $this->withoutMiddleware()->put('/api/users/' . $this->user_uuid_2, []);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['username', 'password']]);


    $valid_username = 'testingusername';
    $valid_password = 'testingpassword';

    $invalid_usernames = [
      null,
      '',
      'invalid username',
      'invalid_username',
      'invalid-username',
      '123',
      'str',
      rand_str(32 + 1),
      rand_str(32 + 1, true),
    ];

    foreach ($invalid_usernames as $key => $value) {
      $response = $this->withoutMiddleware()->put('/api/users/' . $this->user_uuid_2, [
        'username' => $value,
        'password' => $valid_password,
        'password_confirmation' => $valid_password,
      ]);

      $this->assertArrayHasKey('username', $response['data'], 'Error on $key = ' . $key);

      $response->assertStatus(401);
    }

    $invalid_passwords = [
      null,
      '',
      'invalid password',
      'invalid_password',
      'invalid-password',
      '123',
      'str',
      rand_str(32 + 1),
      rand_str(32 + 1, true),
    ];

    foreach ($invalid_passwords as $key => $value) {
      $response = $this->withoutMiddleware()->put('/api/users/' . $this->user_uuid_2, [
        'username' => $valid_username,
        'password' => $value,
        'password_confirmation' => $value,
      ]);

      $this->assertArrayHasKey('password', $response['data'], 'Error on $key = ' . $key);

      $response->assertStatus(401);
    }
  }

  public function test_should_not_edit_any_user_type_on_invalid_password_confirmation() {
    $this->setup_config();

    $test_username = 'testingusername';
    $test_password = 'testingpassword';

    $response = $this->withoutMiddleware()->put('/api/users/' . $this->user_uuid_1, [
      'username' => $test_username,
      'password' => $test_password,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['password']]);

    $test_username = 'testingadminusername';
    $test_password = 'testingadminpassword';

    $response = $this->withoutMiddleware()->put('/api/users/' . $this->user_uuid_2, [
      'username' => $test_username,
      'password' => $test_password,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['password']]);
  }

  public function test_should_delete_regular_user_successfully() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->delete('/api/users/' . $this->user_uuid_1);

    $response->assertStatus(200);

    $actual = User::select()->where('uuid', $this->user_uuid_1)->get()->first();

    $this->assertNull($actual);
  }

  public function test_should_not_delete_regular_user_when_id_is_used_instead_of_uuid() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->delete('/api/users/' . $this->user_id_1);

    $response->assertStatus(404);
  }

  public function test_should_not_delete_non_existent_user() {
    $this->setup_config();

    $invalid_id = -1;

    $response = $this->withoutMiddleware()->delete('/api/users/' . $invalid_id);

    $response->assertStatus(404);
  }

  public function test_should_not_delete_admin_user() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->delete('/api/users/' . $this->user_uuid_2);

    $response->assertStatus(404);
  }
}
