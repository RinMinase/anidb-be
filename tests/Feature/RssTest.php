<?php

namespace Tests\Feature;

use Tests\BaseTestCase;

use App\Models\Rss;
use App\Models\RssItem;

class RssTest extends BaseTestCase {

  // Backup related variables
  private $rss_backup = null;
  private $rss_item_backup = null;

  // Class variables
  private $rss_id_1 = 99999;
  private $rss_uuid_1 = '6414846a-dfe7-4537-b94e-aac529b75c13';
  private $rss_title_1 = 'sample test title';
  private $rss_last_updated_at_1 = '2020-01-01 13:00:00';
  private $rss_update_speed_mins_1 = 120;
  private $rss_url_1 = 'https://example.com/feed.rss';
  private $rss_max_items_1 = 250;

  private $rss_id_2 = 99998;
  private $rss_uuid_2 = '47c58acc-6e9f-4d11-837c-b928b2269590';
  private $rss_title_2 = 'sample test title 2';
  private $rss_last_updated_at_2 = '2020-03-01 13:00:00';
  private $rss_update_speed_mins_2 = 250;
  private $rss_url_2 = 'https://another-example.com/feed.rss';
  private $rss_max_items_2 = 100;

  private $rss_item_id_1 = 99999;
  private $rss_item_uuid_1 = 'a5ebe347-b95e-474a-8688-a5c135f048cb';
  private $rss_item_title_1 = 'sample title';
  private $rss_item_link_1 = 'https://example.com';
  private $rss_item_guid_1 = 'https://example.com/sample-unique-guid';
  private $rss_item_date_1 = '2020-01-01 00:00:00';
  private $rss_item_is_read_1 = false;
  private $rss_item_is_bookmarked_1 = false;

  private $rss_item_id_2 = 99998;
  private $rss_item_uuid_2 = '6aa50e7d-37c6-4597-bb46-7cd8c19fdd90';
  private $rss_item_title_2 = 'sample title2';
  private $rss_item_link_2 = 'https://another-example.com';
  private $rss_item_guid_2 = 'https://another-example.com/sample-unique-guid';
  private $rss_item_date_2 = '2020-02-01 00:00:00';
  private $rss_item_is_read_2 = true;
  private $rss_item_is_bookmarked_2 = true;

  // Backup related tables
  private function setup_backup() {
    $hidden_columns = ['id', 'updated_at'];
    $this->rss_backup = Rss::all()->makeVisible($hidden_columns)->toArray();

    $hidden_columns = ['id', 'id_rss', 'updated_at'];
    $this->rss_item_backup = RssItem::all()->makeVisible($hidden_columns)->toArray();
  }

  // Restore related tables
  private function setup_restore() {
    Rss::truncate();
    Rss::insert($this->rss_backup);
    Rss::refreshAutoIncrements();

    RssItem::truncate();
    RssItem::insert($this->rss_item_backup);
    RssItem::refreshAutoIncrements();
  }

  // Setup data for testing
  private function setup_config() {
    Rss::truncate();
    RssItem::truncate();

    Rss::insert([
      [
        'id' => $this->rss_id_1,
        'uuid' => $this->rss_uuid_1,
        'title' => $this->rss_title_1,
        'last_updated_at' => $this->rss_last_updated_at_1,
        'update_speed_mins' => $this->rss_update_speed_mins_1,
        'url' => $this->rss_url_1,
        'max_items' => $this->rss_max_items_1,
        'created_at' => '2020-01-01 13:00:00',
        'updated_at' => '2020-01-01 13:00:00',
      ], [
        'id' => $this->rss_id_2,
        'uuid' => $this->rss_uuid_2,
        'title' => $this->rss_title_2,
        'last_updated_at' => $this->rss_last_updated_at_2,
        'update_speed_mins' => $this->rss_update_speed_mins_2,
        'url' => $this->rss_url_2,
        'max_items' => $this->rss_max_items_2,
        'created_at' => '2020-02-01 13:00:00',
        'updated_at' => '2020-02-01 13:00:00',
      ]
    ]);

    RssItem::insert([
      [
        'id' => $this->rss_item_id_1,
        'uuid' => $this->rss_item_uuid_1,
        'id_rss' => $this->rss_id_1,
        'title' => $this->rss_item_title_1,
        'link' => $this->rss_item_link_1,
        'guid' => $this->rss_item_guid_1,
        'date' => $this->rss_item_date_1,
        'is_read' => $this->rss_item_is_read_1,
        'is_bookmarked' => $this->rss_item_is_bookmarked_1,
        'created_at' => '2020-01-01 13:00:00',
        'updated_at' => '2020-01-01 13:00:00',
      ], [
        'id' => $this->rss_item_id_2,
        'uuid' => $this->rss_item_uuid_2,
        'id_rss' => $this->rss_id_1,
        'title' => $this->rss_item_title_2,
        'link' => $this->rss_item_link_2,
        'guid' => $this->rss_item_guid_2,
        'date' => $this->rss_item_date_2,
        'is_read' => $this->rss_item_is_read_2,
        'is_bookmarked' => $this->rss_item_is_bookmarked_2,
        'created_at' => '2020-02-01 13:00:00',
        'updated_at' => '2020-02-01 13:00:00',
      ]
    ]);
  }

  // Fixtures
  public function setUp(): void {
    parent::setUp();
    $this->setup_backup();
  }

  public function tearDown(): void {
    $this->setup_restore();
    parent::tearDown();
  }

  // Test Cases
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

    $expected = [
      [
        'uuid' => $this->rss_uuid_1,
        'title' => $this->rss_title_1,
        'lastUpdatedAt' => $this->rss_last_updated_at_1,
        'updateSpeedMins' => $this->rss_update_speed_mins_1,
        'url' => $this->rss_url_1,
        'maxItems' => $this->rss_max_items_1,
        'createdAt' => '2020-01-01 13:00:00',
      ], [
        'uuid' => $this->rss_uuid_2,
        'title' => $this->rss_title_2,
        'lastUpdatedAt' => $this->rss_last_updated_at_2,
        'updateSpeedMins' => $this->rss_update_speed_mins_2,
        'url' => $this->rss_url_2,
        'maxItems' => $this->rss_max_items_2,
        'createdAt' => '2020-02-01 13:00:00',
      ],
    ];

    $this->assertArrayIsEqualToArrayOnlyConsideringListOfKeys(
      $expected[0],
      $response['data'][0],
      ['uuid', 'title', 'lastUpdatedAt', 'url', 'maxItems', 'createdAt'],
    );

    $this->assertArrayIsEqualToArrayOnlyConsideringListOfKeys(
      $expected[1],
      $response['data'][1],
      ['uuid', 'title', 'lastUpdatedAt', 'url', 'maxItems', 'createdAt'],
    );
  }

  public function test_should_add_rss_feed_successfully() {
    $test_title = 'sample test rss title';
    $test_update_speed_mins = 120;
    $test_url = 'https://example.com/test-data/rss';
    $test_max_items = 250;

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
    $this->assertEquals($test_title, $actual['title']);
    $this->assertEquals($test_update_speed_mins, $actual['update_speed_mins']);
    $this->assertEquals($test_url, $actual['url']);
    $this->assertEquals($test_max_items, $actual['max_items']);
  }

  public function test_should_not_add_rss_feed_on_form_errors() {
    $response = $this->withoutMiddleware()->post('/api/rss');

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => [
        'title',
        'url',
      ]]);

    $test_title = rand_str(64 + 1);
    $test_update_speed_mins = 'string';
    $test_url = 'http://google.com/' . rand_str(512 + 1 - 18);
    $test_max_items = 'string';

    $response = $this->withoutMiddleware()->post('/api/rss', [
      'title' => $test_title,
      'update_speed_mins' => $test_update_speed_mins,
      'url' => $test_url,
      'max_items' => $test_max_items,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['title', 'update_speed_mins', 'url', 'max_items']]);

    $test_valid_title = 'sample valid test rss title';

    $test_update_speed_mins = 20; // should be divisible by 15
    $test_url = 'invalid-url';
    $test_max_items = -1;

    $response = $this->withoutMiddleware()->post('/api/rss', [
      'title' => $test_valid_title,
      'url' => $test_url,
      'update_speed_mins' => $test_update_speed_mins,
      'max_items' => $test_max_items,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['update_speed_mins', 'url', 'max_items']]);

    $test_valid_url = 'https://example.com/rss';

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

    $response = $this->withoutMiddleware()->put('/api/rss/' . $this->rss_uuid_1, [
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
    $this->assertEquals($test_title, $actual['title']);
    $this->assertEquals($test_update_speed_mins, $actual['update_speed_mins']);
    $this->assertEquals($test_url, $actual['url']);
    $this->assertEquals($test_max_items, $actual['max_items']);
  }

  public function test_should_not_edit_rss_feed_on_form_errors() {
    $response = $this->withoutMiddleware()->put('/api/rss/' . $this->rss_uuid_1);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => [
        'title',
        'url',
      ]]);

    $test_title = rand_str(64 + 1);
    $test_update_speed_mins = 'string';
    $test_url = 'http://google.com/' . rand_str(512 + 1 - 18);
    $test_max_items = 'string';

    $response = $this->withoutMiddleware()->put('/api/rss/' . $this->rss_uuid_1, [
      'title' => $test_title,
      'update_speed_mins' => $test_update_speed_mins,
      'url' => $test_url,
      'max_items' => $test_max_items,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['title', 'update_speed_mins', 'url', 'max_items']]);

    $test_valid_title = 'sample valid test rss title';

    $test_update_speed_mins = 20; // should be divisible by 15
    $test_url = 'invalid-url';
    $test_max_items = -1;

    $response = $this->withoutMiddleware()->put('/api/rss/' . $this->rss_uuid_1, [
      'title' => $test_valid_title,
      'url' => $test_url,
      'update_speed_mins' => $test_update_speed_mins,
      'max_items' => $test_max_items,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['update_speed_mins', 'url', 'max_items']]);

    $test_valid_url = 'https://example.com/rss';

    $test_update_speed_mins = -1;
    $test_max_items = 32768;

    $response = $this->withoutMiddleware()->put('/api/rss/' . $this->rss_uuid_1, [
      'title' => $test_valid_title,
      'url' => $test_valid_url,
      'update_speed_mins' => $test_update_speed_mins,
      'max_items' => $test_max_items,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['update_speed_mins', 'max_items']]);

    $test_update_speed_mins = 32768;

    $response = $this->withoutMiddleware()->put('/api/rss/' . $this->rss_uuid_1, [
      'title' => $test_valid_title,
      'url' => $test_valid_url,
      'update_speed_mins' => $test_update_speed_mins,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['update_speed_mins']]);

    $test_update_speed_mins = 14;

    $response = $this->withoutMiddleware()->put('/api/rss/' . $this->rss_uuid_1, [
      'title' => $test_valid_title,
      'url' => $test_valid_url,
      'update_speed_mins' => $test_update_speed_mins,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['update_speed_mins']]);
  }

  public function test_should_not_edit_rss_feed_when_id_is_used_instead_of_uuid() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->put('/api/rss/' . $this->rss_id_1);

    $response->assertStatus(404);
  }

  public function test_should_delete_rss_feed_with_rss_items_successfully() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->delete('/api/rss/' . $this->rss_uuid_1);

    $response->assertStatus(200);

    $actualFeed = Rss::where('uuid', $this->rss_uuid_1)->first();
    $actualItems = RssItem::where('id_rss', $this->rss_id_1)->get();

    $this->assertNull($actualFeed);
    $this->assertCount(0, $actualItems);
  }

  public function test_should_not_delete_rss_feed_when_id_is_used_instead_of_uuid() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->delete('/api/rss/' . $this->rss_id_1);

    $response->assertStatus(404);
  }

  public function test_should_not_delete_non_existent_rss_feed() {
    $invalid_id = -1;

    $response = $this->withoutMiddleware()->delete('/api/rss/' . $invalid_id);

    $response->assertStatus(404);
  }

  public function test_should_get_rss_items_from_rss_feed_successfully() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->get('/api/rss/' . $this->rss_uuid_1);

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

    $expected = [
      [
        'uuid' => $this->rss_item_uuid_2,
        'title' => $this->rss_item_title_2,
        'link' => $this->rss_item_link_2,
        'guid' => $this->rss_item_guid_2,
        'date' => $this->rss_item_date_2,
        'isRead' => $this->rss_item_is_read_2,
        'isBookmarked' => $this->rss_item_is_bookmarked_2,
      ], [
        'uuid' => $this->rss_item_uuid_1,
        'title' => $this->rss_item_title_1,
        'link' => $this->rss_item_link_1,
        'guid' => $this->rss_item_guid_1,
        'date' => $this->rss_item_date_1,
        'isRead' => $this->rss_item_is_read_1,
        'isBookmarked' => $this->rss_item_is_bookmarked_1,
      ],
    ];

    $this->assertArrayIsEqualToArrayOnlyConsideringListOfKeys(
      $expected[0],
      $response['data'][0],
      ['uuid', 'title', 'link', 'guid', 'date', 'isRead', 'isBookmarked'],
    );

    $this->assertArrayIsEqualToArrayOnlyConsideringListOfKeys(
      $expected[1],
      $response['data'][1],
      ['uuid', 'title', 'link', 'guid', 'date', 'isRead', 'isBookmarked'],
    );
  }

  public function test_should_not_get_rss_items_from_rss_feed_when_id_is_used_instead_of_uuid() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->get('/api/rss/' . $this->rss_id_1);

    $response->assertStatus(404);
  }

  public function test_should_not_get_rss_items_from_invalid_rss_feed() {
    $invalid_id = -1;

    $response = $this->withoutMiddleware()->get('/api/rss/' . $invalid_id);

    $response->assertStatus(404);
  }

  public function test_should_mark_rss_item_as_read_successfully() {
    $this->setup_config();

    $actual = RssItem::where('uuid', $this->rss_item_uuid_1)->first()->toArray();

    $this->assertNotNull($actual);
    $this->assertFalse($actual['is_read']);

    $response = $this->withoutMiddleware()->post('/api/rss/read/' . $this->rss_item_uuid_1);

    $response->assertStatus(200);

    $actual = RssItem::where('uuid', $this->rss_item_uuid_1)->first()->toArray();

    $this->assertNotNull($actual);
    $this->assertTrue($actual['is_read']);
  }

  public function test_should_mark_rss_item_as_unread_successfully() {
    $this->setup_config();

    $actual = RssItem::where('uuid', $this->rss_item_uuid_2)->first()->toArray();

    $this->assertNotNull($actual);
    $this->assertTrue($actual['is_read']);

    $response = $this->withoutMiddleware()->delete('/api/rss/read/' . $this->rss_item_uuid_2);

    $response->assertStatus(200);

    $actual = RssItem::where('uuid', $this->rss_item_uuid_2)->first()->toArray();

    $this->assertNotNull($actual);
    $this->assertFalse($actual['is_read']);
  }

  public function test_should_not_mark_rss_item_as_read_or_unread_when_id_is_used_instead_of_uuid() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->post('/api/rss/read/' . $this->rss_item_id_1);

    $response->assertStatus(404);
  }

  public function test_should_not_toggle_read_or_unread_on_invalid_rss_item() {
    $invalid_id = -1;

    $response = $this->withoutMiddleware()->post('/api/rss/read/' . $invalid_id);

    $response->assertStatus(404);
  }

  public function test_should_bookmark_rss_item_successfully() {
    $this->setup_config();

    $actual = RssItem::where('uuid', $this->rss_item_uuid_1)->first()->toArray();

    $this->assertNotNull($actual);
    $this->assertFalse($actual['is_bookmarked']);

    $response = $this->withoutMiddleware()->post('/api/rss/bookmark/' . $this->rss_item_uuid_1);

    $response->assertStatus(200);

    $actual = RssItem::where('uuid', $this->rss_item_uuid_1)->first()->toArray();

    $this->assertNotNull($actual);
    $this->assertTrue($actual['is_bookmarked']);
  }

  public function test_should_remove_rss_item_from_bookmarks_successfully() {
    $this->setup_config();

    $actual = RssItem::where('uuid', $this->rss_item_uuid_2)->first()->toArray();

    $this->assertNotNull($actual);
    $this->assertTrue($actual['is_bookmarked']);

    $response = $this->withoutMiddleware()->delete('/api/rss/bookmark/' . $this->rss_item_uuid_2);

    $response->assertStatus(200);

    $actual = RssItem::where('uuid', $this->rss_item_uuid_2)->first()->toArray();

    $this->assertNotNull($actual);
    $this->assertFalse($actual['is_bookmarked']);
  }

  public function test_should_not_toggle_bookmark_on_rss_item_when_id_is_used_instead_of_uuid() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->post('/api/rss/bookmark/' . $this->rss_item_id_1);

    $response->assertStatus(404);
  }

  public function test_should_not_toggle_bookmark_on_invalid_rss_item() {
    $invalid_id = -1;

    $response = $this->withoutMiddleware()->post('/api/rss/bookmark/' . $invalid_id);

    $response->assertStatus(404);
  }
}
