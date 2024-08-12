<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\BaseTestCase;

use App\Models\Sequence;

class SequenceTest extends BaseTestCase {

  // Backup related variables
  private $sequence_backup = null;

  // Class variables
  private $sequence_id_1 = 99999;
  private $sequence_title_1 = 'test title';
  private $sequence_date_from_1 = '2020-01-01';
  private $sequence_date_to_1 = '2020-01-02';

  // Backup related tables
  private function setup_backup() {
    $hidden_columns = ['created_at', 'updated_at'];
    $this->sequence_backup = Sequence::all()->makeVisible($hidden_columns)->toArray();
  }

  // Restore related tables
  private function setup_restore() {
    Sequence::truncate();
    Sequence::insert($this->sequence_backup);
    Sequence::refreshAutoIncrements();
  }

  // Setup data for testing
  private function setup_config() {
    Sequence::truncate();

    Sequence::insert([
      'id' => $this->sequence_id_1,
      'title' => $this->sequence_title_1,
      'date_from' => $this->sequence_date_from_1,
      'date_to' => $this->sequence_date_to_1,
      'created_at' => '2020-01-03 01:00:00',
      'updated_at' => '2020-01-03 01:00:00',
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

    $response = $this->withoutMiddleware()->get('/api/sequences');

    $response->assertStatus(200)
      ->assertJsonCount(1, 'data')
      ->assertJsonStructure([
        'data' => [[
          'id',
          'title',
          'dateFrom',
          'dateTo',
        ]],
      ]);

    $expected = [[
      'id' => $this->sequence_id_1,
      'title' => $this->sequence_title_1,
      'dateFrom' => $this->sequence_date_from_1,
      'dateTo' => $this->sequence_date_to_1,
    ]];

    $this->assertEquals($expected, $response['data']);
  }

  public function test_should_add_data_successfully() {
    $test_title = 'test title';
    $test_date_from = '1980-10-20 13:00';
    $test_date_to = '1980-11-20 13:00';

    $response = $this->withoutMiddleware()->post('/api/sequences', [
      'title' => $test_title,
      'date_from' => $test_date_from,
      'date_to' => $test_date_to,
    ]);

    $response->assertStatus(200);

    $actual = Sequence::where('date_from', $test_date_from)
      ->where('date_to', $test_date_to)
      ->first()
      ->toArray();

    $this->assertEquals($test_title, $actual['title']);

    $this->assertEquals(
      Carbon::parse($test_date_from)->toDateString(),
      Carbon::parse($actual['date_from'])->toDateString(),
    );

    $this->assertEquals(
      Carbon::parse($test_date_to)->toDateString(),
      Carbon::parse($actual['date_to'])->toDateString(),
    );
  }

  public function test_should_not_add_data_on_form_errors() {
    $response = $this->withoutMiddleware()->post('/api/sequences');

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['title', 'date_to', 'date_from']]);


    $test_title = rand_str(256 + 1);
    $test_date_from = '2099-10-20 13:00';
    $test_date_to = '2099-10-19 13:00';

    $response = $this->withoutMiddleware()->post('/api/sequences', [
      'title' => $test_title,
      'date_from' => $test_date_from,
      'date_to' => $test_date_to,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['title', 'date_to']]);

    $test_valid_title = 'string';
    $test_date_from = 'string';
    $test_date_to = 'string';

    $response = $this->withoutMiddleware()->post('/api/sequences', [
      'title' => $test_valid_title,
      'date_from' => $test_date_from,
      'date_to' => $test_date_to,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['date_to', 'date_from']]);
  }

  public function test_should_edit_data_successfully() {
    $this->setup_config();

    $test_title = 'new test title';
    $test_date_from = '1980-10-20 13:00';
    $test_date_to = '1980-11-20 13:00';

    $response = $this->withoutMiddleware()->put('/api/sequences/' . $this->sequence_id_1, [
      'title' => $test_title,
      'date_from' => $test_date_from,
      'date_to' => $test_date_to,
    ]);

    $response->assertStatus(200);

    $actual = Sequence::where('date_from', $test_date_from)
      ->where('date_to', $test_date_to)
      ->first()
      ->toArray();

    $this->assertEquals($test_title, $actual['title']);

    $this->assertEquals(
      Carbon::parse($test_date_from)->toDateString(),
      Carbon::parse($actual['date_from'])->toDateString(),
    );

    $this->assertEquals(
      Carbon::parse($test_date_to)->toDateString(),
      Carbon::parse($actual['date_to'])->toDateString(),
    );
  }

  public function test_should_not_edit_data_on_form_errors() {
    $response = $this->withoutMiddleware()->put('/api/sequences/' . $this->sequence_id_1);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['title', 'date_to', 'date_from']]);


    $test_title = rand_str(256 + 1);
    $test_date_from = '2099-10-20 13:00';
    $test_date_to = '2099-10-19 13:00';

    $response = $this->withoutMiddleware()->put('/api/sequences/' . $this->sequence_id_1, [
      'title' => $test_title,
      'date_from' => $test_date_from,
      'date_to' => $test_date_to,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['title', 'date_to']]);

    $test_valid_title = rand_str(256);
    $test_date_from = 'string';
    $test_date_to = 'string';

    $response = $this->withoutMiddleware()->put('/api/sequences/' . $this->sequence_id_1, [
      'title' => $test_valid_title,
      'date_from' => $test_date_from,
      'date_to' => $test_date_to,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['date_to', 'date_from']]);
  }

  public function test_should_delete_data_successfully() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->withoutMiddleware()
      ->delete('/api/sequences/' . $this->sequence_id_1);

    $response->assertStatus(200);

    $actual = Sequence::where('id', $this->sequence_id_1)->first();

    $this->assertNull($actual);
  }

  public function test_should_not_delete_non_existent_data() {
    $invalid_id = -1;

    $response = $this->withoutMiddleware()->delete('/api/sequence/' . $invalid_id);

    $response->assertStatus(404);
  }
}
