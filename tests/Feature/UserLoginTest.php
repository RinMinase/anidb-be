<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\BaseTestCase;

class UserLoginTest extends BaseTestCase {

  public function test_user_login() {
    $test_email = "unit_testing@mail.com";
    $test_password = "e9597119-8452-4f2b-96d8-f2b1b1d2f158";

    User::create([
      'password' => bcrypt($test_password),
      'email' => $test_email
    ]);

    $response = $this->post('/api/auth/login', [
      'email' => $test_email,
      'password' => $test_password,
    ]);

    $response->assertStatus(200);

    // Clearing test data
    User::where('email', $test_email)->delete();
  }

  public function test_user_login_with_invalid_credentials() {
    $test_email = "unit_testing@mail.com";
    $test_password = "e9597119-8452-4f2b-96d8-f2b1b1d2f158";

    $response = $this->post('/api/auth/login', [
      'email' => $test_email,
      'password' => $test_password,
    ]);

    $response->assertStatus(401)
      ->assertJson([
        'message' => 'Credentials does not match'
      ]);
  }

  public function test_user_login_with_invalid_email() {
    $test_email = "invalid email";
    $test_password = "e9597119-8452-4f2b-96d8-f2b1b1d2f158";

    $response = $this->post('/api/auth/login', [
      'email' => $test_email,
      'password' => $test_password,
    ]);

    $response->assertStatus(401)
      ->assertJson([
        'data' => [
          'email' => [
            'The email must be a valid email address.'
          ]
        ]
      ]);
  }

  public function test_user_login_with_blank_email() {
    $test_password = "e9597119-8452-4f2b-96d8-f2b1b1d2f158";

    $response = $this->post('/api/auth/login', [
      'password' => $test_password,
    ]);

    $response->assertStatus(401)
      ->assertJson([
        'data' => [
          'email' => [
            'The email field is required.'
          ]
        ]
      ]);
  }

  public function test_user_login_with_blank_password() {
    $test_email = "unit_testing@mail.com";

    $response = $this->post('/api/auth/login', [
      'email' => $test_email,
    ]);

    $response->assertStatus(401)
      ->assertJson([
        'data' => [
          'password' => [
            'The password field is required.'
          ]
        ]
      ]);
  }
}
