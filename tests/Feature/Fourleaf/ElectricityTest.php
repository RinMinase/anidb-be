<?php

namespace Tests\Feature\Fourleaf;

use Carbon\Carbon;
use Tests\BaseTestCase;

use App\Fourleaf\Models\Electricity;

class ElectricityTest extends BaseTestCase {

  // Backup related variables
  private $electricity_backup = null;

  // Class variables
  private $electricity_id_1 = 99998;
  private $electricity_id_2 = 99999;

  private $year = 2090;
  private $month = 1;

  // Backup related tables
  private function setup_backup() {
    $this->electricity_backup = Electricity::all()->toArray();
  }

  // Restore related tables
  private function setup_restore() {
    Electricity::truncate();
    Electricity::insert($this->electricity_backup);
    Electricity::refreshAutoIncrements();
  }

  // Setup data for testing
  private function setup_config() {
    Electricity::truncate();

    Electricity::insert([
      [
        'id' => $this->electricity_id_1,
        'datetime' => $this->year . '-' . $this->month . '-1 13:00:00',
        'reading' => 100,
      ], [
        'id' => $this->electricity_id_2,
        'datetime' => $this->year . '-' . $this->month . '-2 13:00:00',
        'reading' => 120,
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

    $response = $this->withoutMiddleware()
      ->get(
        '/api/fourleaf/electricity?year=' . $this->year . '&month=' . $this->month
      );

    $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
          'settings' => [
            'kwhValue',
            'monthStartsAt',
          ],
          'weekly' => [[
            'id',
            'weekNo',
            'actualWeekYearNo',
            'daysWithRecord',
            'daysWithNoRecord',
            'daysInWeek',
            'totalRecordedKwh',
            'totalEstimatedKwh',
            'estTotalKwh',
            'estTotalPrice',
            'avgDailyKwh',
          ]],
          'daily' => [[
            'id',
            'dateNumber',
            'day',
            'date',
            'kwPerHour',
            'kwPerDay',
            'pricePerDay',
            'readingValue',
            'readingTime',
            'state',
            'allDaysAvg',
          ]],
        ],
      ]);
  }

  public function test_should_return_blank_entries_on_no_data_dates() {
    $invalid_month = 2;

    $response = $this->withoutMiddleware()
      ->get(
        '/api/fourleaf/electricity?year=' . $this->year . '&month=' . $invalid_month
      );

    $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
          'settings' => [],
          'daily' => [],
          'weekly' => [],
        ],
      ]);
  }

  public function test_should_add_data_successfully() {
    $test_datetime_1 = '1980-10-20 13:00';
    $test_reading_1 = 999;

    $response = $this->post('/api/fourleaf/electricity', [
      'datetime' => $test_datetime_1,
      'reading' => $test_reading_1,
    ]);

    $response->assertStatus(200);

    $test_datetime_2 = '1980-12-20 13:00';
    $test_reading_2 = 200_000;

    $response = $this->post('/api/fourleaf/electricity', [
      'datetime' => $test_datetime_2,
      'reading' => $test_reading_2,
    ]);

    $response->assertStatus(200);
  }

  public function test_should_not_add_data_on_form_errors() {
    $test_datetime = '2099-10-20 13:00';
    $test_reading = 200_001;

    $response = $this->post('/api/fourleaf/electricity', [
      'datetime' => $test_datetime,
      'reading' => $test_reading,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure([
        'data' => [
          'datetime',
          'reading',
        ],
      ]);
  }

  public function test_should_edit_data_successfully() {
    $this->setup_config();

    $test_datetime = '1990-01-01 13:00';
    $test_reading = 500;

    $response = $this->withoutMiddleware()
      ->put(
        '/api/fourleaf/electricity/' . $this->electricity_id_1,
        [
          'datetime' => $test_datetime,
          'reading' => $test_reading,
        ]
      );

    $response->assertStatus(200);

    $actual = Electricity::where('id', $this->electricity_id_1)->first();

    $this->assertEquals($test_reading, $actual->reading);
    $this->assertEquals(
      Carbon::parse($test_datetime)->toString(),
      Carbon::parse($actual->datetime)->toString(),
    );
  }

  public function test_should_not_edit_data_on_form_errors() {
    $this->setup_config();

    $test_datetime = '2099-01-01 13:00';
    $test_reading = 200_001;

    $response = $this->withoutMiddleware()
      ->put(
        '/api/fourleaf/electricity/' . $this->electricity_id_1,
        [
          'datetime' => $test_datetime,
          'reading' => $test_reading,
        ]
      );

    $response->assertStatus(401)
      ->assertJsonStructure([
        'data' => [
          'datetime',
          'reading',
        ],
      ]);
  }

  public function test_should_delete_data_successfully() {
    $this->setup_config();

    $response = $this->withoutMiddleware()
      ->delete('/api/fourleaf/electricity/' . $this->electricity_id_1);

    $response->assertStatus(200);

    $actual = Electricity::where('id', $this->electricity_id_1)->first();

    $this->assertNull($actual);
  }

  public function test_should_not_delete_non_existent_data() {
    $invalid_id = -1;

    $response = $this->withoutMiddleware()
      ->delete('/api/fourleaf/electricity/' . $invalid_id);

    $response->assertStatus(404);
  }
}
