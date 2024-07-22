<?php

namespace Tests\Feature;

use Tests\BaseTestCase;

use App\Models\Rss;
use App\Models\RssItem;

class RssTest extends BaseTestCase {

  private $rss_id = 99999;
  private $rss_uuid = '6414846a-dfe7-4537-b94e-aac529b75c13';

  private $rss_item_id = 99999;
  private $rss_item_uuid = 'a5ebe347-b95e-474a-8688-a5c135f048cb';

  private function setup_config() {
    // Clearing possible duplicate data
    $this->setup_clear();

    Rss::insert([
      'id' => $this->rss_id,
      'uuid' => $this->rss_uuid,
      'title' => 'sample test title',
      'last_updated_at' => '2020-01-01 13:00:00',
      'update_speed_mins' => 120,
      'url' => 'https://example.com/feed.rss',
      'max_items' => 250,
      'created_at' => '2020-01-01 13:00:00',
      'updated_at' => '2020-01-01 13:00:00',
    ]);

    RssItem::insert([
      'id' => $this->rss_item_id,
      'uuid' => $this->rss_item_uuid,
      'id_rss' => $this->rss_id,
      'title' => 'sample test title',
      'link' => 'https://example.com',
      'guid' => 'https://example.com/sample-unique-guid',
      'date' => '2020-01-01 00:00:00',
      'is_read' => false,
      'is_bookmarked' => false,
      'created_at' => '2020-01-01 13:00:00',
      'updated_at' => '2020-01-01 13:00:00',
    ]);
  }

  private function setup_clear() {
    Rss::where('id', $this->rss_id)->forceDelete();
    RssItem::where('id', $this->rss_item_id)->forceDelete();
  }

  public function test_should_get_all_data() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->get('/api/rss');

    $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [[
          'uuid',
          'title',
          'lastUpdatedAt',
          'updateSpeedMins',
          'url',
          'maxItems',
          'unread',
          'createdAt',
        ]],
      ]);

    $this->setup_clear();
  }

  public function test_should_not_get_all_data_when_not_authorized() {
    $response = $this->get('/api/rss');

    $response->assertStatus(401)
      ->assertJson(['message' => 'Unauthorized']);
  }

  public function test_should_add_rss_feed_successfully() {
    $test_title = 'sample test rss title';
    $test_update_speed_mins = 120;
    $test_url = 'https://example.com/test-data/rss';
    $test_max_items = 250;

    // Clearing possible duplicate data
    Rss::where('title', $test_title)
      ->where('url', $test_url)
      ->delete();

    $response = $this->withoutMiddleware()->post('/api/rss', [
      'title' => $test_title,
      'update_speed_mins' => $test_update_speed_mins,
      'url' => $test_url,
      'max_items' => $test_max_items,
    ]);

    $response->assertStatus(200);

    $data = Rss::where('title', $test_title)
      ->where('url', $test_url)
      ->first();

    $actual = $data->toArray();

    $this->assertNotNull($actual);
    $this->assertNotNull($actual['uuid']);
    $this->assertSame($test_title, $actual['title']);
    $this->assertSame($test_update_speed_mins, $actual['update_speed_mins']);
    $this->assertSame($test_url, $actual['url']);
    $this->assertSame($test_max_items, $actual['max_items']);

    $data->delete();
  }

  public function test_should_not_add_rss_feed_on_form_errors() {
    $response = $this->withoutMiddleware()->post('/api/rss');

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => [
        'title',
        'url',
      ]]);

    $test_title = 'sample invalid test rss title BIOEIZPMPHWSCUQBTFVOGKXVMGLLSDUUBIO';
    $test_update_speed_mins = 'string';
    $test_url = 'invalid-url';
    $test_max_items = 'string';

    $response = $this->withoutMiddleware()->post('/api/rss', [
      'title' => $test_title,
      'update_speed_mins' => $test_update_speed_mins,
      'url' => $test_url,
      'max_items' => $test_max_items,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => [
        'title',
        'update_speed_mins',
        'url',
        'max_items',
      ]]);

    $test_valid_title = 'sample valid test rss title';
    $test_valid_url = 'https://example.com/rss';

    $test_update_speed_mins = 20; // should be divisible by 15
    $test_max_items = -1;

    $response = $this->withoutMiddleware()->post('/api/rss', [
      'title' => $test_valid_title,
      'url' => $test_valid_url,
      'update_speed_mins' => $test_update_speed_mins,
      'max_items' => $test_max_items,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['update_speed_mins', 'max_items']]);

    $test_update_speed_mins = -1;
    $test_max_items = 32768;

    $response = $this->withoutMiddleware()->post('/api/rss', [
      'title' => $test_valid_title,
      'url' => $test_valid_url,
      'update_speed_mins' => $test_update_speed_mins,
      'max_items' => $test_max_items,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['update_speed_mins', 'max_items']]);

    $test_update_speed_mins = 32768;

    $response = $this->withoutMiddleware()->post('/api/rss', [
      'title' => $test_valid_title,
      'url' => $test_valid_url,
      'update_speed_mins' => $test_update_speed_mins,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['update_speed_mins']]);

    $test_update_speed_mins = 14;

    $response = $this->withoutMiddleware()->post('/api/rss', [
      'title' => $test_valid_title,
      'url' => $test_valid_url,
      'update_speed_mins' => $test_update_speed_mins,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['update_speed_mins']]);
  }

  public function test_should_edit_rss_feed_successfully() {
    $this->setup_config();

    $test_title = 'sample test rss title';
    $test_update_speed_mins = 300;
    $test_url = 'https://example.com/test-data/rss';
    $test_max_items = 2000;

    $response = $this->withoutMiddleware()->put('/api/rss/' . $this->rss_uuid, [
      'title' => $test_title,
      'update_speed_mins' => $test_update_speed_mins,
      'url' => $test_url,
      'max_items' => $test_max_items,
    ]);

    $response->assertStatus(200);

    $actual = Rss::where('title', $test_title)
      ->where('url', $test_url)
      ->first()
      ->toArray();

    $this->assertNotNull($actual['uuid']);
    $this->assertSame($test_title, $actual['title']);
    $this->assertSame($test_update_speed_mins, $actual['update_speed_mins']);
    $this->assertSame($test_url, $actual['url']);
    $this->assertSame($test_max_items, $actual['max_items']);

    $this->setup_clear();
  }

  public function test_should_not_edit_rss_feed_on_form_errors() {
    $response = $this->withoutMiddleware()->put('/api/rss/' . $this->rss_uuid);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => [
        'title',
        'url',
      ]]);

    $test_title = 'sample invalid test rss title BIOEIZPMPHWSCUQBTFVOGKXVMGLLSDUUBIO';
    $test_update_speed_mins = 'string';
    $test_url = 'invalid-url';
    $test_max_items = 'string';

    $response = $this->withoutMiddleware()->put('/api/rss/' . $this->rss_uuid, [
      'title' => $test_title,
      'update_speed_mins' => $test_update_speed_mins,
      'url' => $test_url,
      'max_items' => $test_max_items,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => [
        'title',
        'update_speed_mins',
        'url',
        'max_items',
      ]]);

    $test_valid_title = 'sample valid test rss title';
    $test_valid_url = 'https://example.com/rss';

    $test_update_speed_mins = 20; // should be divisible by 15
    $test_max_items = -1;

    $response = $this->withoutMiddleware()->put('/api/rss/' . $this->rss_uuid, [
      'title' => $test_valid_title,
      'url' => $test_valid_url,
      'update_speed_mins' => $test_update_speed_mins,
      'max_items' => $test_max_items,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['update_speed_mins', 'max_items']]);

    $test_update_speed_mins = -1;
    $test_max_items = 32768;

    $response = $this->withoutMiddleware()->put('/api/rss/' . $this->rss_uuid, [
      'title' => $test_valid_title,
      'url' => $test_valid_url,
      'update_speed_mins' => $test_update_speed_mins,
      'max_items' => $test_max_items,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['update_speed_mins', 'max_items']]);

    $test_update_speed_mins = 32768;

    $response = $this->withoutMiddleware()->put('/api/rss/' . $this->rss_uuid, [
      'title' => $test_valid_title,
      'url' => $test_valid_url,
      'update_speed_mins' => $test_update_speed_mins,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['update_speed_mins']]);

    $test_update_speed_mins = 14;

    $response = $this->withoutMiddleware()->put('/api/rss/' . $this->rss_uuid, [
      'title' => $test_valid_title,
      'url' => $test_valid_url,
      'update_speed_mins' => $test_update_speed_mins,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['update_speed_mins']]);
  }

  public function test_should_not_edit_rss_feed_when_id_is_used_instead_of_uuid() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->put('/api/rss/' . $this->rss_id);

    $response->assertStatus(404);

    $this->setup_clear();
  }

  public function test_should_delete_rss_feed_with_rss_items_successfully() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->delete('/api/rss/' . $this->rss_uuid);

    $response->assertStatus(200);

    $actualFeed = Rss::where('uuid', $this->rss_uuid)->first();
    $actualItems = RssItem::where('id_rss', $this->rss_id)->get();

    $this->assertNull($actualFeed);
    $this->assertCount(0, $actualItems);

    $this->setup_clear();
  }

  public function test_should_not_delete_rss_feed_when_id_is_used_instead_of_uuid() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->delete('/api/rss/' . $this->rss_id);

    $response->assertStatus(404);

    $this->setup_clear();
  }

  public function test_should_not_delete_non_existent_rss_feed() {
    $invalid_id = -1;

    $response = $this->withoutMiddleware()->delete('/api/rss/' . $invalid_id);

    $response->assertStatus(404);
  }

  public function test_should_get_rss_items_from_rss_feed_successfully() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->get('/api/rss/' . $this->rss_uuid);

    $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [[
          'uuid',
          'title',
          'link',
          'guid',
          'date',
          'isRead',
          'isBookmarked',
          'createdAt',
        ]],
      ]);

    $this->setup_clear();
  }

  public function test_should_not_get_rss_items_from_rss_feed_when_id_is_used_instead_of_uuid() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->get('/api/rss/' . $this->rss_id);

    $response->assertStatus(404);

    $this->setup_clear();
  }

  public function test_should_not_get_rss_items_from_invalid_rss_feed() {
    $invalid_id = -1;

    $response = $this->withoutMiddleware()->get('/api/rss/' . $invalid_id);

    $response->assertStatus(404);
  }

  public function test_should_mark_rss_item_as_read_successfully() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->post('/api/rss/read/' . $this->rss_item_uuid);

    $response->assertStatus(200);

    $actual = RssItem::where('uuid', $this->rss_item_uuid)->first()->toArray();

    $this->assertNotNull($actual);
    $this->assertSame(true, $actual['is_read']);

    $this->setup_clear();
  }

  public function test_should_mark_rss_item_as_unread_successfully() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->delete('/api/rss/read/' . $this->rss_item_uuid);

    $response->assertStatus(200);

    $actual = RssItem::where('uuid', $this->rss_item_uuid)->first()->toArray();

    $this->assertNotNull($actual);
    $this->assertSame(false, $actual['is_read']);

    $this->setup_clear();
  }

  public function test_should_not_mark_invalid_rss_item_as_read() {
    $invalid_id = -1;

    $response = $this->withoutMiddleware()->post('/api/rss/read/' . $invalid_id);

    $response->assertStatus(404);
  }

  public function test_should_not_mark_invalid_rss_item_as_unread() {
    $invalid_id = -1;

    $response = $this->withoutMiddleware()->delete('/api/rss/read/' . $invalid_id);

    $response->assertStatus(404);
  }

  public function test_should_bookmark_rss_item_successfully() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->post('/api/rss/bookmark/' . $this->rss_item_uuid);

    $response->assertStatus(200);

    $actual = RssItem::where('uuid', $this->rss_item_uuid)->first()->toArray();

    $this->assertNotNull($actual);
    $this->assertSame(true, $actual['is_bookmarked']);

    $this->setup_clear();
  }

  public function test_should_remove_rss_item_from_bookmarks_successfully() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->delete('/api/rss/bookmark/' . $this->rss_item_uuid);

    $response->assertStatus(200);

    $actual = RssItem::where('uuid', $this->rss_item_uuid)->first()->toArray();

    $this->assertNotNull($actual);
    $this->assertSame(false, $actual['is_bookmarked']);

    $this->setup_clear();
  }

  public function test_should_not_bookmark_invalid_rss_item() {
    $invalid_id = -1;

    $response = $this->withoutMiddleware()->post('/api/rss/bookmark/' . $invalid_id);

    $response->assertStatus(404);
  }

  public function test_should_not_remove_invalid_rss_item_from_bookmarks() {
    $invalid_id = -1;

    $response = $this->withoutMiddleware()->delete('/api/rss/bookmark/' . $invalid_id);

    $response->assertStatus(404);
  }
}
