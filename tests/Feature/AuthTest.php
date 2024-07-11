<?php

namespace Tests\Feature;

use Tests\BaseTestCase;

use App\Models\User;

class AuthTest extends BaseTestCase {

  public function test_should_login_successfully() {
    $test_email = "unit_testing@mail.com";
    $test_password = "e9597119-8452-4f2b-96d8-f2b1b1d2f158";

    // Clearing possible duplicate data
    User::where('email', $test_email)->delete();
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

  public function test_should_not_login_on_invalid_credentials() {
    $test_email = "unit_testing@mail.com";
    $test_password = "e9597119-8452-4f2b-96d8-f2b1b1d2f158";

    $response = $this->post('/api/auth/login', [
      'email' => $test_email,
      'password' => $test_password,
    ]);

    $response->assertStatus(401)
      ->assertJson(['message' => 'Credentials does not match.']);
  }

  public function test_should_not_login_on_invalid_email() {
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

  public function test_should_not_login_on_blank_email_or_password() {
    $response = $this->post('/api/auth/login', []);

    $response->assertStatus(401)
      ->assertJson([
        'data' => [
          'email' => [
            'The email field is required.'
          ],
          'password' => [
            'The password field is required.'
          ]
        ]
      ]);
  }

  public function test_should_register_successfully() {
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

  public function test_should_not_register_on_invalid_email_or_passwords() {
    $test_email = "invalid email";
    $test_password = "12345";

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
          ],
          'password' => [
            'The password must be at least 6 characters.'
          ]
        ]
      ]);
  }

  public function test_should_not_register_on_blank_email_or_password() {
    $response = $this->post('/api/auth/register', []);

    $response->assertStatus(401)
      ->assertJson([
        'data' => [
          'email' => [
            'The email field is required.'
          ],
          'password' => [
            'The password field is required.'
          ]
        ]
      ]);
  }

  public function test_should_not_register_on_invalid_password_confirmation() {
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
