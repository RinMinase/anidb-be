<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\BaseTestCase;
// use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends BaseTestCase {

  public function test_user_registration() {
    $test_email = "unit_testing@mail.com";
    $test_password = "e9597119-8452-4f2b-96d8-f2b1b1d2f158";

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
}
