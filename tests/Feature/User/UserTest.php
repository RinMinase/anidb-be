<?php

namespace Tests\Feature\User;

use Illuminate\Testing\Fluent\AssertableJson;
use Tests\BaseTestCase;

use App\Models\User;

class UserTest extends BaseTestCase {

  public function test_user_logout() {
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

  public function test_user_logout_with_invalid_session() {
    $response = $this->post('/api/auth/logout');

    $response->assertStatus(401)
      ->assertJson(['message' => 'Unauthorized']);
  }

  public function test_user_information() {
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
      ->assertJson(
        fn (AssertableJson $json) => $json
          ->where('id', fn ($id) => is_numeric($id))
          ->where('email', fn ($email) => str($email)->is($test_email))
      );

    // Clearing test data
    User::where('email', $test_email)->delete();
  }

  public function test_user_information_when_unauthenticated() {
    $response = $this->get('/api/auth/user');

    $response->assertStatus(401)
      ->assertJson(['message' => 'Unauthorized']);
  }
}
