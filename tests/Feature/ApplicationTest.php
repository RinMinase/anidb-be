<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\BaseTestCase;

class ApplicationTest extends BaseTestCase {

  // On true, prints URLs being tested
  private $curr_method = '';
  private $curr_url = '';

  // Fixtures
  public function onNotSuccessfulTest($err): never {
    echo PHP_EOL . 'Failed URL: ' . $this->curr_method . '::' . $this->curr_url . PHP_EOL;

    parent::onNotSuccessfulTest($err);
  }

  // Test Cases
  public function test_application_loads_successfully() {
    $response = $this->get('/');

    $response->assertStatus(200);
  }

  public function test_api_documentation_page_should_be_accessible() {
    $response = $this->get('/docs');

    $response->assertStatus(200);
  }

  public function test_routes_should_not_be_accessible_when_not_authenticated() {
    $id = -1;
    $integer = 1;
    $uuid = 'b02f89f8-794d-4eba-a5f8-5d09fc3d741d';
    $string = 'searchstring';
    $letter = 'A';
    $year = '2020';

    $raw_routes = Route::getRoutes()->getRoutesByMethod();

    $get_routes = [];
    $post_routes = [];
    $put_routes = [];
    $delete_routes = [];

    foreach ($raw_routes['GET'] as $route) {
      $uri = $route->uri();

      if (
        str_contains($uri, 'api/') &&
        !str_contains($uri, 'api/oauth') &&
        !str_contains($uri, 'api/fourleaf/')
      ) {
        $uri = str_replace('{uuid}', $uuid, $uri);
        $uri = str_replace('{id}', $id, $uri);
        $uri = str_replace('{letter}', $letter, $uri);
        $uri = str_replace('{year}', $year, $uri);

        // Deprecated routes
        $uri = str_replace('{integer}', $integer, $uri);
        $uri = str_replace('{string}', $string, $uri);

        array_push($get_routes, '/' . $uri);
      }
    }

    foreach ($raw_routes['POST'] as $route) {
      $uri = $route->uri();

      if (
        str_contains($uri, 'api/') &&
        !str_contains($uri, 'api/fourleaf/') &&
        !str_contains($uri, 'api/auth')
      ) {
        $uri = str_replace('{uuid}', $uuid, $uri);
        $uri = str_replace('{id}', $id, $uri);
        $uri = str_replace('{integer}', $id, $uri);

        array_push($post_routes, '/' . $uri);
      }
    }

    foreach ($raw_routes['PUT'] as $route) {
      $uri = $route->uri();

      if (str_contains($uri, 'api/') && !str_contains($uri, 'api/fourleaf/')) {
        $uri = str_replace('{uuid}', $uuid, $uri);
        $uri = str_replace('{id}', $id, $uri);
        $uri = str_replace('{integer}', $id, $uri);

        array_push($put_routes, '/' . $uri);
      }
    }

    foreach ($raw_routes['DELETE'] as $route) {
      $uri = $route->uri();

      if (str_contains($uri, 'api/') && !str_contains($uri, 'api/fourleaf/')) {
        $uri = str_replace('{uuid}', $uuid, $uri);
        $uri = str_replace('{id}', $id, $uri);

        array_push($delete_routes, '/' . $uri);
      }
    }

    foreach ($get_routes as $route) {
      $response = $this->get($route);
      $this->curr_method = 'GET';
      $this->curr_url = $route;

      $response->assertStatus(401)
        ->assertJson(['message' => 'Unauthorized']);
    }

    foreach ($post_routes as $route) {
      $response = $this->post($route);
      $this->curr_method = 'POST';
      $this->curr_url = $route;

      $response->assertStatus(401)
        ->assertJson(['message' => 'Unauthorized']);
    }

    foreach ($put_routes as $route) {
      $response = $this->put($route);
      $this->curr_method = 'PUT';
      $this->curr_url = $route;

      $response->assertStatus(401)
        ->assertJson(['message' => 'Unauthorized']);
    }

    foreach ($delete_routes as $route) {
      $response = $this->delete($route);
      $this->curr_method = 'DELETE';
      $this->curr_url = $route;

      $response->assertStatus(401)
        ->assertJson(['message' => 'Unauthorized']);
    }
  }

  public function test_fourleaf_routes_should_be_accessible() {
    $id = -1;
    $uuid = 'b02f89f8-794d-4eba-a5f8-5d09fc3d741d';

    $raw_routes = Route::getRoutes()->getRoutesByMethod();

    $get_routes = [];
    $post_routes = [];
    $put_routes = [];
    $delete_routes = [];

    foreach ($raw_routes['GET'] as $route) {
      $uri = $route->uri();

      if (str_contains($uri, 'api/fourleaf')) {
        if (str_contains($uri, 'gas/odo')) {
          // skip odo route due to parameters
          continue;
        }

        array_push($get_routes, '/' . $uri);
      }
    }

    foreach ($raw_routes['POST'] as $route) {
      $uri = $route->uri();

      if (str_contains($uri, 'api/fourleaf')) {
        array_push($post_routes, '/' . $uri);
      }
    }

    foreach ($raw_routes['PUT'] as $route) {
      $uri = $route->uri();

      if (str_contains($uri, 'api/fourleaf')) {
        $uri = str_replace('{uuid}', $uuid, $uri);
        $uri = str_replace('{id}', $id, $uri);

        array_push($put_routes, '/' . $uri);
      }
    }

    foreach ($raw_routes['DELETE'] as $route) {
      $uri = $route->uri();

      if (str_contains($uri, 'api/fourleaf')) {
        $uri = str_replace('{uuid}', $uuid, $uri);
        $uri = str_replace('{id}', $id, $uri);

        array_push($delete_routes, '/' . $uri);
      }
    }

    $not_expected = ['message' => 'Unauthorized'];

    foreach ($get_routes as $route) {
      $response = $this->get($route);
      $this->curr_method = 'GET';
      $this->curr_url = $route;

      $this->assertArrayNotHasKey('message', $response['data']);
      $this->assertNotEquals($not_expected, $response['data']);
    }

    foreach ($post_routes as $route) {
      $response = $this->post($route);
      $this->curr_method = 'POST';
      $this->curr_url = $route;

      $this->assertArrayNotHasKey('message', $response['data']);
      $this->assertNotEquals($not_expected, $response['data']);
    }

    foreach ($put_routes as $route) {
      $response = $this->put($route);
      $this->curr_method = 'PUT';
      $this->curr_url = $route;

      $this->assertArrayNotHasKey('message', $response['data']);
      $this->assertNotEquals($not_expected, $response['data']);
    }

    foreach ($delete_routes as $route) {
      $response = $this->delete($route);
      $this->curr_method = 'DELETE';
      $this->curr_url = $route;

      if (property_exists($response, 'data')) {
        $this->assertArrayNotHasKey('message', $response['data']);
        $this->assertNotEquals($not_expected, $response['data']);
      } else {
        $response->assertStatus(404);
      }
    }
  }
}
