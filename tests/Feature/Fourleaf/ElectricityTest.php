<?php

namespace Tests\Feature\Fourleaf;

use Carbon\Carbon;
use Tests\BaseTestCase;

use App\Fourleaf\Models\Electricity;

class ElectricityTest extends BaseTestCase {

  private $electricity_id_1 = 99998;
  private $electricity_id_2 = 99999;

  private $year = 2090;
  private $month = 1;

  private function setup_config() {
    // Clearing possible duplicate data
    $this->setup_clear();

    Electricity::insert([[
      'id' => $this->electricity_id_1,
      'datetime' => $this->year . '-' . $this->month . '-1 13:00:00',
      'reading' => 100,
    ], [
      'id' => $this->electricity_id_2,
      'datetime' => $this->year . '-' . $this->month . '-2 13:00:00',
      'reading' => 120,
    ]]);
  }

  private function setup_clear() {
    Electricity::where('id', $this->electricity_id_1)
      ->orWhere('id', $this->electricity_id_2)
      ->forceDelete();
  }

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

    $this->setup_clear();
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
    $test_datetime = '1980-10-20 13:00';
    $test_reading_1 = 999;
    $test_reading_2 = 200_000;

    // Clearing possible duplicate data
    Electricity::where('datetime', $test_datetime)->delete();

    $response = $this->post('/api/fourleaf/electricity', [
      'datetime' => $test_datetime,
      'reading' => $test_reading_1,
    ]);

    $response->assertStatus(200);

    // Clearing test data
    Electricity::where('datetime', $test_datetime)->delete();

    $response = $this->post('/api/fourleaf/electricity', [
      'datetime' => $test_datetime,
      'reading' => $test_reading_2,
    ]);

    $response->assertStatus(200);

    // Clearing test data
    Electricity::where('datetime', $test_datetime)->delete();
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

    $this->assertSame($test_reading, $actual->reading);
    $this->assertSame(
      Carbon::parse($test_datetime)->toString(),
      Carbon::parse($actual->datetime)->toString(),
    );

    $this->setup_clear();
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

    $this->setup_clear();
  }

  public function test_should_delete_data_successfully() {
    $this->setup_config();

    $response = $this->withoutMiddleware()
      ->delete('/api/fourleaf/electricity/' . $this->electricity_id_1);

    $response->assertStatus(200);

    $actual = Electricity::where('id', $this->electricity_id_1)->first();

    $this->assertNull($actual);

    $this->setup_clear();
  }

  public function test_should_not_delete_non_existent_data() {
    $this->setup_config();

    $invalid_id = -1;

    $response = $this->withoutMiddleware()
      ->delete('/api/fourleaf/electricity/' . $invalid_id);

    $response->assertStatus(404);

    $this->setup_clear();
  }
}
