<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\BaseTestCase;

use App\Models\Sequence;

class SequenceTest extends BaseTestCase {

  private $sequence_id_1 = 99999;

  private function setup_config() {
    // Clearing possible duplicate data
    $this->setup_clear();

    Sequence::insert([
      'id' => $this->sequence_id_1,
      'title' => 'test title',
      'date_from' => '2020-01-01 01:00:00',
      'date_to' => '2020-01-02 01:00:00',
      'created_at' => '2020-01-03 01:00:00',
      'updated_at' => '2020-01-03 01:00:00',
    ]);
  }

  private function setup_clear() {
    Sequence::where('id', $this->sequence_id_1)
      ->forceDelete();
  }

  public function test_should_get_all_data() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->get('/api/sequences');

    $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [[
          'id',
          'title',
          'dateFrom',
          'dateTo',
        ]],
      ]);

    $this->setup_clear();
  }

  public function test_should_not_get_all_data_no_auth() {
    $this->setup_config();

    $response = $this->get('/api/sequences');

    $response->assertStatus(401)
      ->assertJson(['message' => 'Unauthorized']);

    $this->setup_clear();
  }

  public function test_should_add_data_successfully() {
    $test_title = 'test title ';
    $test_date_from = '1980-10-20 13:00';
    $test_date_to = '1980-11-20 13:00';

    // Clearing possible duplicate data
    Sequence::where('date_from', $test_date_from)
      ->where('date_to', $test_date_to)
      ->delete();

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

    $this->assertSame($test_title, $actual['title']);

    $this->assertSame(
      Carbon::parse($test_date_from)->toDateString(),
      Carbon::parse($actual['date_from'])->toDateString(),
    );

    $this->assertSame(
      Carbon::parse($test_date_to)->toDateString(),
      Carbon::parse($actual['date_to'])->toDateString(),
    );

    // Clearing test data
    Sequence::where('date_from', $test_date_from)
      ->where('date_to', $test_date_to)
      ->delete();
  }

  public function test_should_not_add_data_on_form_errors() {
    $test_title = 'test title BIOEIZPMPHWSCUQBTFVOGKXVMGLLSDUUBIOEIZPMPHWSCUQBTFVOGKXVMGLLSDUU BIOEIZPMPHWSCUQBTFVOGKXVMGLLSDUUBIOEIZPMPHWSCUQBTFVOGKXVMGLLSDUU BIOEIZPMPHWSCUQBTFVOGKXVMGLLSDUUBIOEIZPMPHWSCUQBTFVOGKXVMGLLSDUU BIOEIZPMPHWSCUQBTFVOGKXVMGLLSDUUBIOEIZPMPHWSCUQBTFVOGKXVMGLLSDUU';
    $test_date_from = '2099-10-20 13:00';
    $test_date_to = '2099-10-19 13:00';

    $response = $this->withoutMiddleware()->post('/api/sequences', [
      'title' => $test_title,
      'date_from' => $test_date_from,
      'date_to' => $test_date_to,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure([
        'data' => [
          'title',
          'date_to',
        ],
      ]);
  }

  public function test_should_not_add_on_no_auth() {
    $response = $this->post('/api/sequences/', []);

    $response->assertStatus(401)
      ->assertJson(['message' => 'Unauthorized']);
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

    $this->assertSame($test_title, $actual['title']);

    $this->assertSame(
      Carbon::parse($test_date_from)->toDateString(),
      Carbon::parse($actual['date_from'])->toDateString(),
    );

    $this->assertSame(
      Carbon::parse($test_date_to)->toDateString(),
      Carbon::parse($actual['date_to'])->toDateString(),
    );

    // Clearing test data
    Sequence::where('date_from', $test_date_from)
      ->where('date_to', $test_date_to)
      ->delete();
  }

  public function test_should_not_edit_data_on_form_errors() {
    $test_title = 'test title BIOEIZPMPHWSCUQBTFVOGKXVMGLLSDUUBIOEIZPMPHWSCUQBTFVOGKXVMGLLSDUU BIOEIZPMPHWSCUQBTFVOGKXVMGLLSDUUBIOEIZPMPHWSCUQBTFVOGKXVMGLLSDUU BIOEIZPMPHWSCUQBTFVOGKXVMGLLSDUUBIOEIZPMPHWSCUQBTFVOGKXVMGLLSDUU BIOEIZPMPHWSCUQBTFVOGKXVMGLLSDUUBIOEIZPMPHWSCUQBTFVOGKXVMGLLSDUU';
    $test_date_from = '2099-10-20 13:00';
    $test_date_to = '2099-10-19 13:00';

    $response = $this->withoutMiddleware()->put('/api/sequences/' . $this->sequence_id_1, [
      'title' => $test_title,
      'date_from' => $test_date_from,
      'date_to' => $test_date_to,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure([
        'data' => [
          'title',
          'date_to',
        ],
      ]);
  }

  public function test_should_not_edit_on_no_auth() {
    $this->setup_config();

    $response = $this->put('/api/sequences/' . $this->sequence_id_1, []);

    $response->assertStatus(401)
      ->assertJson(['message' => 'Unauthorized']);

    $this->setup_clear();
  }

  public function test_should_delete_data_successfully() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->withoutMiddleware()
      ->delete('/api/sequences/' . $this->sequence_id_1);

    $response->assertStatus(200);

    $actual = Sequence::where('id', $this->sequence_id_1)->first();

    $this->assertNull($actual);

    $this->setup_clear();
  }

  public function test_should_not_delete_non_existent_data() {
    $this->setup_config();

    $invalid_id = -1;

    $response = $this->withoutMiddleware()->delete('/api/sequence/' . $invalid_id);

    $response->assertStatus(404);

    $this->setup_clear();
  }

  public function test_should_not_delete_on_no_auth() {
    $this->setup_config();

    $response = $this->delete('/api/sequences/' . $this->sequence_id_1);

    $response->assertStatus(401)
      ->assertJson(['message' => 'Unauthorized']);

    $this->setup_clear();
  }
}
