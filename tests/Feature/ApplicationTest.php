<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\BaseTestCase;

class ApplicationTest extends BaseTestCase {

  // On true, prints URLs being tested
  private $debugging = true;

  private $id = -1;
  private $integer = 1;
  private $uuid = 'b02f89f8-794d-4eba-a5f8-5d09fc3d741d';
  private $string = 'searchstring';

  public function test_application_loads_successfully() {
    $response = $this->get('/');

    $response->assertStatus(200);
  }

  public function test_api_documentation_page_should_be_accessible() {
    $response = $this->get('/docs');

    $response->assertStatus(200);
  }

  public function test_routes_should_not_be_accessible_when_not_authenticated() {
    $raw_routes = Route::getRoutes()->getRoutesByMethod();

    $get_raw_routes = $raw_routes['GET'];
    $post_raw_routes = $raw_routes['POST'];
    $put_raw_routes = $raw_routes['PUT'];
    $delete_raw_routes = $raw_routes['DELETE'];

    $get_routes = [];
    $post_routes = [];
    $put_routes = [];
    $delete_routes = [];

    foreach ($get_raw_routes as $route) {
      $uri = $route->uri();

      if (
        str_contains($uri, 'api/') &&
        !str_contains($uri, 'api/oauth') &&
        !str_contains($uri, 'api/fourleaf/')
      ) {
        $uri = str_replace('{uuid}', $this->uuid, $uri);
        $uri = str_replace('{id}', $this->id, $uri);
        $uri = str_replace('{integer}', $this->integer, $uri);
        $uri = str_replace('{string}', $this->string, $uri);

        array_push($get_routes, '/' . $uri);
      }
    }

    foreach ($post_raw_routes as $route) {
      $uri = $route->uri();

      if (
        str_contains($uri, 'api/') &&
        !str_contains($uri, 'api/fourleaf/') &&
        !str_contains($uri, 'api/auth')
      ) {
        $uri = str_replace('{uuid}', $this->uuid, $uri);
        $uri = str_replace('{id}', $this->id, $uri);
        $uri = str_replace('{integer}', $this->id, $uri);

        array_push($post_routes, '/' . $uri);
      }
    }

    foreach ($put_raw_routes as $route) {
      $uri = $route->uri();

      if (str_contains($uri, 'api/') && !str_contains($uri, 'api/fourleaf/')) {
        $uri = str_replace('{uuid}', $this->uuid, $uri);
        $uri = str_replace('{id}', $this->id, $uri);
        $uri = str_replace('{integer}', $this->id, $uri);

        array_push($put_routes, '/' . $uri);
      }
    }

    foreach ($delete_raw_routes as $route) {
      $uri = $route->uri();

      if (str_contains($uri, 'api/') && !str_contains($uri, 'api/fourleaf/')) {
        $uri = str_replace('{uuid}', $this->uuid, $uri);
        $uri = str_replace('{id}', $this->id, $uri);

        array_push($delete_routes, '/' . $uri);
      }
    }

    foreach ($get_routes as $route) {
      $response = $this->get($route);

      if ($this->debugging) echo 'GET::' . $route . PHP_EOL;

      $response->assertStatus(401)
        ->assertJson(['message' => 'Unauthorized']);
    }

    foreach ($post_routes as $route) {
      $response = $this->post($route);

      if ($this->debugging) echo 'POST::' . $route . PHP_EOL;

      $response->assertStatus(401)
        ->assertJson(['message' => 'Unauthorized']);
    }

    foreach ($put_routes as $route) {
      $response = $this->put($route);

      if ($this->debugging) echo 'PUT::' . $route . PHP_EOL;

      $response->assertStatus(401)
        ->assertJson(['message' => 'Unauthorized']);
    }

    foreach ($delete_routes as $route) {
      $response = $this->delete($route);

      if ($this->debugging) echo 'DELETE::' . $route . PHP_EOL;

      $response->assertStatus(401)
        ->assertJson(['message' => 'Unauthorized']);
    }
  }
}
