<?php

namespace Tests\Feature;

use Tests\BaseTestCase;
// use Illuminate\Foundation\Testing\RefreshDatabase;

class ApplicationTest extends BaseTestCase {

  public function test_application_loads_successfully() {
    $response = $this->get('/');

    $response->assertStatus(200);
  }
}
