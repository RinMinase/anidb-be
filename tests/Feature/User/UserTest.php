<?php

namespace Tests\Feature\User;

use Tests\BaseTestCase;

use App\Models\User;

class UserTest extends BaseTestCase {

  public function test_should_logout_successfully() {
    $test_email = "unit_testing@mail.com";
    $test_password = "e9597119-8452-4f2b-96d8-f2b1b1d2f158";

    // Clearing possible duplicate data
    User::where('email', $test_email)->delete();
    User::create([
      'password' => bcrypt($test_password),
      'email' => $test_email
    ]);

    $this->post('/api/auth/login', [
      'email' => $test_email,
      'password' => $test_password,
    ]);

    $response = $this->post('/api/auth/logout');

    $response->assertStatus(200);

    // Clearing test data
    User::where('email', $test_email)->delete();
  }

  public function test_should_not_logout_on_invalid_session() {
    $response = $this->post('/api/auth/logout');

    $response->assertStatus(401)
      ->assertJson(['message' => 'Unauthorized']);
  }

  public function test_should_get_user_information_successfully() {
    $test_email = "unit_testing@mail.com";
    $test_password = "e9597119-8452-4f2b-96d8-f2b1b1d2f158";

    // Clearing possible duplicate data
    User::where('email', $test_email)->delete();
    User::create([
      'password' => bcrypt($test_password),
      'email' => $test_email
    ]);

    $this->post('/api/auth/login', [
      'email' => $test_email,
      'password' => $test_password,
    ]);

    $response = $this->get('/api/auth/user');

    $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
          'id',
          'email',
        ],
      ]);

    // Clearing test data
    User::where('email', $test_email)->delete();
  }

  public function test_should_not_get_information_when_not_authorized() {
    $response = $this->get('/api/auth/user');

    $response->assertStatus(401)
      ->assertJson(['message' => 'Unauthorized']);
  }
}
