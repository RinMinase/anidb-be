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

  // Place this outside the try-catch block
  private function setup_backup() {
    // Save current sequence list
    $this->sequence_backup = Sequence::all()
      ->makeVisible(['created_at', 'updated_at'])
      ->toArray();
  }

  // Place this in a try block
  private function setup_config() {
    Sequence::truncate();

    Sequence::insert([
      'id' => $this->sequence_id_1,
      'title' => 'test title',
      'date_from' => '2020-01-01 01:00:00',
      'date_to' => '2020-01-02 01:00:00',
      'created_at' => '2020-01-03 01:00:00',
      'updated_at' => '2020-01-03 01:00:00',
    ]);
  }

  // Place this in a finally block
  private function setup_restore() {
    Sequence::truncate();
    Sequence::insert($this->sequence_backup);
  }

  public function test_should_get_all_data() {
    $this->setup_backup();

    try {
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
    } finally {
      $this->setup_restore();
    }
  }

  public function test_should_not_get_all_data_no_auth() {
    $this->setup_backup();

    try {
      $this->setup_config();

      $response = $this->get('/api/sequences');

      $response->assertStatus(401)
        ->assertJson(['message' => 'Unauthorized']);
    } finally {
      $this->setup_restore();
    }
  }

  public function test_should_add_data_successfully() {
    $this->setup_backup();

    try {
      $this->setup_config();

      $test_title = 'test title ';
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

      $this->assertSame($test_title, $actual['title']);

      $this->assertSame(
        Carbon::parse($test_date_from)->toDateString(),
        Carbon::parse($actual['date_from'])->toDateString(),
      );

      $this->assertSame(
        Carbon::parse($test_date_to)->toDateString(),
        Carbon::parse($actual['date_to'])->toDateString(),
      );
    } finally {
      $this->setup_restore();
    }
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
    $this->setup_backup();

    try {
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
    } finally {
      $this->setup_restore();
    }
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
    $this->setup_backup();

    try {
      $this->setup_config();

      $response = $this->put('/api/sequences/' . $this->sequence_id_1, []);

      $response->assertStatus(401)
        ->assertJson(['message' => 'Unauthorized']);
    } finally {
      $this->setup_restore();
    }
  }

  public function test_should_delete_data_successfully() {
    $this->setup_backup();

    try {
      $this->setup_config();

      $response = $this->withoutMiddleware()->withoutMiddleware()
        ->delete('/api/sequences/' . $this->sequence_id_1);

      $response->assertStatus(200);

      $actual = Sequence::where('id', $this->sequence_id_1)->first();

      $this->assertNull($actual);
    } finally {
      $this->setup_restore();
    }
  }

  public function test_should_not_delete_non_existent_data() {
    $invalid_id = -1;

    $response = $this->withoutMiddleware()->delete('/api/sequence/' . $invalid_id);

    $response->assertStatus(404);
  }

  public function test_should_not_delete_on_no_auth() {
    $this->setup_backup();

    try {
      $this->setup_config();

      $response = $this->delete('/api/sequences/' . $this->sequence_id_1);

      $response->assertStatus(401)
        ->assertJson(['message' => 'Unauthorized']);
    } finally {
      $this->setup_restore();
    }
  }
}
