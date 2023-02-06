<?php

namespace Tests\Feature\User;

use Tests\BaseTestCase;
use App\Models\User;

class RegistrationTest extends BaseTestCase {

  public function test_user_registration() {
    $test_email = "unit_testing@mail.com";
    $test_password = "e9597119-8452-4f2b-96d8-f2b1b1d2f158";

    // Clearing possible duplicate data
    User::where('email', $test_email)->delete();

    $response = $this->post('/api/auth/register', [
      'email' => $test_email,
      'password' => $test_password,
      'password_confirmation' => $test_password,
    ]);

    $response->assertStatus(200);

    // Clearing test data
    User::where('email', $test_email)->delete();
  }

  public function test_user_registration_with_invalid_email() {
    $test_email = "invalid email";
    $test_password = "e9597119-8452-4f2b-96d8-f2b1b1d2f158";

    $response = $this->post('/api/auth/register', [
      'email' => $test_email,
      'password' => $test_password,
      'password_confirmation' => $test_password,
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

  public function test_user_registration_with_invalid_password() {
    $test_email = "unit_testing@mail.com";
    $test_password = "12345";

    $response = $this->post('/api/auth/register', [
      'email' => $test_email,
      'password' => $test_password,
      'password_confirmation' => $test_password,
    ]);

    $response->assertStatus(401)
      ->assertJson([
        'data' => [
          'password' => [
            'The password must be at least 6 characters.'
          ]
        ]
      ]);
  }

  public function test_user_registration_with_blank_email() {
    $test_password = "e9597119-8452-4f2b-96d8-f2b1b1d2f158";

    $response = $this->post('/api/auth/register', [
      'password' => $test_password,
      'password_confirmation' => $test_password,
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

  public function test_user_registration_with_blank_password() {
    $test_email = "unit_testing@mail.com";

    $response = $this->post('/api/auth/register', [
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

  public function test_user_registration_with_invalid_password_confirmation() {
    $test_email = "unit_testing@mail.com";
    $test_password = "e9597119-8452-4f2b-96d8-f2b1b1d2f158";

    $response = $this->post('/api/auth/register', [
      'email' => $test_email,
      'password' => $test_password,
    ]);

    $response->assertStatus(401)
      ->assertJson([
        'data' => [
          'password' => [
            'The password confirmation does not match.'
          ]
        ]
      ]);
  }
}
