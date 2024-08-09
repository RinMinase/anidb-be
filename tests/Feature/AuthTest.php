<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\BaseTestCase;

use App\Models\User;

class AuthTest extends BaseTestCase {

  // Backup related variables
  private $user_backup = null;

  // Class variables
  private $user_id_1 = 99999;

  private $user_email = 'testing-mail@testmail.com';
  private $user_password = 'sample_test_password';

  // Backup related tables
  private function setup_backup() {
    $hidden_columns = ['password', 'created_at', 'updated_at'];
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
        'email' => $this->user_email,
        'password' => bcrypt($this->user_password),
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

    $response = $this->post('/api/auth/login', [
      'email' => $this->user_email,
      'password' => $this->user_password,
    ]);

    $response->assertStatus(200);
  }

  public function test_should_not_login_on_invalid_credentials() {
    $this->setup_config();

    $test_email = 'unit_testing@mail.com';
    $test_password = 'e9597119-8452-4f2b-96d8-f2b1b1d2f158';

    $response = $this->post('/api/auth/login', [
      'email' => $test_email,
      'password' => $test_password,
    ]);

    $response->assertStatus(401)
      ->assertJson(['message' => 'Credentials does not match.']);

    $response = $this->post('/api/auth/login', [
      'email' => $this->user_email,
      'password' => $test_password,
    ]);

    $response->assertStatus(401)
      ->assertJson(['message' => 'Credentials does not match.']);
  }

  public function test_should_not_login_on_invalid_email() {
    $this->setup_config();

    $test_email = 'invalid email';
    $test_password = 'e9597119-8452-4f2b-96d8-f2b1b1d2f158';

    $response = $this->post('/api/auth/login', [
      'email' => $test_email,
      'password' => $test_password,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['email']]);
  }

  public function test_should_not_login_on_blank_email_or_password() {
    $response = $this->post('/api/auth/login');

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['email', 'password']]);
  }

  public function test_should_register_successfully() {
    $test_email = 'unit_testing@mail.com';
    $test_password = 'e9597119-8452-4f2b-96d8-f2b1b1d2f158';

    $response = $this->post('/api/auth/register', [
      'email' => $test_email,
      'password' => $test_password,
      'password_confirmation' => $test_password,
    ]);

    $response->assertStatus(200);
  }

  public function test_should_not_register_on_invalid_email_or_passwords() {
    $test_email = 'invalid email';
    $test_password = '12345';

    $response = $this->post('/api/auth/register', [
      'email' => $test_email,
      'password' => $test_password,
      'password_confirmation' => $test_password,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['email', 'password']]);
  }

  public function test_should_not_register_on_blank_email_or_password() {
    $response = $this->post('/api/auth/register', []);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['email', 'password']]);
  }

  public function test_should_not_register_on_invalid_password_confirmation() {
    $test_email = 'unit_testing@mail.com';
    $test_password = 'e9597119-8452-4f2b-96d8-f2b1b1d2f158';

    $response = $this->post('/api/auth/register', [
      'email' => $test_email,
      'password' => $test_password,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['password']]);
  }

  public function test_should_logout_successfully() {
    $this->setup_config();

    $this->post('/api/auth/login', [
      'email' => $this->user_email,
      'password' => $this->user_password,
    ]);

    $response = $this->post('/api/auth/logout');

    $response->assertStatus(200);
  }

  public function test_should_not_logout_on_invalid_session() {
    $response = $this->post('/api/auth/logout');

    $response->assertStatus(401)
      ->assertJson(['message' => 'Unauthorized']);
  }

  public function test_should_get_user_information_successfully() {
    $this->setup_config();

    $this->post('/api/auth/login', [
      'email' => $this->user_email,
      'password' => $this->user_password,
    ]);

    $response = $this->get('/api/auth/user');

    $response->assertStatus(200);

    $expected = [
      'id' => $this->user_id_1,
      'email' => $this->user_email,
    ];

    $this->assertEquals($expected, $response['data']);
  }

  public function test_should_not_get_information_when_not_authorized() {
    $response = $this->get('/api/auth/user');

    $response->assertStatus(401)
      ->assertJson(['message' => 'Unauthorized']);
  }
}
