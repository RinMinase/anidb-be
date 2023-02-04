<?php

namespace Tests\Feature;

use Tests\BaseTestCase;
// use Illuminate\Foundation\Testing\RefreshDatabase;

class ExampleTest extends BaseTestCase {

  public function test_the_application_returns_a_successful_response() {
    $response = $this->get('/');

    $response->assertStatus(200);
  }
}
