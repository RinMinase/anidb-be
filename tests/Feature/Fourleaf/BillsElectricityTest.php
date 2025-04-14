<?php

namespace Tests\Feature\Fourleaf;

use Carbon\Carbon;
use Tests\BaseTestCase;

use App\Enums\IntegerSizesEnum;
use App\Enums\IntegerTypesEnum;

use App\Fourleaf\Models\BillsElectricity;

class BillsElectricityTest extends BaseTestCase {

  // Backup related variables
  private $bills_electricity_backup = null;

  // Class variables
  private $bills_electricity_id_1 = 99996;
  private $bills_electricity_uuid_1 = '010329a7-773b-49a2-8dd2-7362bd82a4d9';
  private $bills_electricity_uid_1 = 201912;
  private $bills_electricity_kwh_1 = 200;
  private $bills_electricity_cost_1 = 2000;

  private $bills_electricity_id_2 = 99997;
  private $bills_electricity_uuid_2 = 'fed9a5be-f076-4c07-8e2e-04978c09112e';
  private $bills_electricity_uid_2 = 202001;
  private $bills_electricity_kwh_2 = 210;
  private $bills_electricity_cost_2 = 2500;

  private $bills_electricity_id_3 = 99998;
  private $bills_electricity_uuid_3 = '05e7be3a-1d66-4838-86c6-11b3ddc1481d';
  private $bills_electricity_uid_3 = 202002;
  private $bills_electricity_kwh_3 = 300;
  private $bills_electricity_cost_3 = 2900;

  private $bills_electricity_id_4 = 99999;
  private $bills_electricity_uuid_4 = '31c79ea0-5624-4328-ba73-03d73cc26e6d';
  private $bills_electricity_uid_4 = 202101;
  private $bills_electricity_kwh_4 = 310;
  private $bills_electricity_cost_4 = 4000;

  // Backup related tables
  private function setup_backup() {
    $this->bills_electricity_backup = BillsElectricity::all()->makeVisible('id')->toArray();
  }

  // Restore related tables
  private function setup_restore() {
    BillsElectricity::truncate();
    BillsElectricity::insert($this->bills_electricity_backup);
    BillsElectricity::refreshAutoIncrements();
  }

  // Setup data for testing
  private function setup_config() {
    BillsElectricity::truncate();

    BillsElectricity::insert([
      [
        'id' => $this->bills_electricity_id_1,
        'uuid' => $this->bills_electricity_uuid_1,
        'uid' => $this->bills_electricity_uid_1,
        'kwh' => $this->bills_electricity_kwh_1,
        'cost' => $this->bills_electricity_cost_1,
      ],
      [
        'id' => $this->bills_electricity_id_2,
        'uuid' => $this->bills_electricity_uuid_2,
        'uid' => $this->bills_electricity_uid_2,
        'kwh' => $this->bills_electricity_kwh_2,
        'cost' => $this->bills_electricity_cost_2,
      ],
      [
        'id' => $this->bills_electricity_id_3,
        'uuid' => $this->bills_electricity_uuid_3,
        'uid' => $this->bills_electricity_uid_3,
        'kwh' => $this->bills_electricity_kwh_3,
        'cost' => $this->bills_electricity_cost_3,
      ],
      [
        'id' => $this->bills_electricity_id_4,
        'uuid' => $this->bills_electricity_uuid_4,
        'uid' => $this->bills_electricity_uid_4,
        'kwh' => $this->bills_electricity_kwh_4,
        'cost' => $this->bills_electricity_cost_4,
      ],
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
  public function test_should_get_all_data_successfully() {
    try {
      $this->setup_config();

      // Mock date values
      Carbon::setTestNow(Carbon::parse('2020-05-01'));

      $response = $this->withoutMiddleware()
        ->get('/api/fourleaf/bills/electricity');

      $response->assertStatus(200)
        ->assertJsonCount(2, 'data')
        ->assertJsonStructure([
          'data' => [[
            'uuid',
            'kwh',
            'cost',
            'date',
            'costPerKwh',
          ]]
        ]);
    } finally {
      // Restore mocks
      Carbon::setTestNow();
    }
  }

  public function test_should_get_all_data_with_year_param_successfully() {
    $this->setup_config();

    $test_params = ['year' => 2019];
    $response = $this->withoutMiddleware()
      ->get('/api/fourleaf/bills/electricity?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(1, 'data')
      ->assertJsonStructure([
        'data' => [[
          'uuid',
          'kwh',
          'cost',
          'date',
          'costPerKwh',
        ]]
      ]);

    $test_params = ['year' => 2020];
    $response = $this->withoutMiddleware()
      ->get('/api/fourleaf/bills/electricity?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(2, 'data')
      ->assertJsonStructure([
        'data' => [[
          'uuid',
          'kwh',
          'cost',
          'date',
          'costPerKwh',
        ]]
      ]);

    $test_params = ['year' => 2021];
    $response = $this->withoutMiddleware()
      ->get('/api/fourleaf/bills/electricity?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(1, 'data')
      ->assertJsonStructure([
        'data' => [[
          'uuid',
          'kwh',
          'cost',
          'date',
          'costPerKwh',
        ]]
      ]);

    $test_params = ['year' => 2022];
    $response = $this->withoutMiddleware()
      ->get('/api/fourleaf/bills/electricity?' . http_build_query($test_params));

    $response->assertStatus(200)
      ->assertJsonCount(0, 'data');
  }

  public function test_should_not_get_all_data_on_form_errors() {
    $this->setup_config();

    $year = [1800, 1899, 3000, 3050];

    foreach ($year as $key => $value) {
      $response = $this->withoutMiddleware()
        ->get('/api/fourleaf/bills/electricity?' . http_build_query(['year' => $value]));

      $this->assertEquals(401, $response['status'], 'Error in $key=' . $key);
    }
  }

  public function test_should_add_data_successfully() {
    $test_date = '2020-10-01';
    $test_kwh = 5000;
    $test_cost = 1234.12;

    $response = $this->withoutMiddleware()->post('/api/fourleaf/bills/electricity', [
      'date' => $test_date,
      'kwh' => $test_kwh,
      'cost' => $test_cost,
    ]);

    $response->assertStatus(200);

    $actual = BillsElectricity::where('kwh', $test_kwh)
      ->where('cost', $test_cost)
      ->first();

    $this->assertNotNull($actual);
  }

  public function test_should_not_add_data_on_form_errors() {
    $response = $this->withoutMiddleware()->post('/api/fourleaf/bills/electricity');

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['date', 'kwh', 'cost']]);

    $test_date = '2100-01-01';
    $test_kwh = max_int(IntegerTypesEnum::SIGNED, IntegerSizesEnum::SMALL) + 1;
    $test_cost = 'string';

    $response = $this->withoutMiddleware()->post('/api/fourleaf/bills/electricity', [
      'date' => $test_date,
      'kwh' => $test_kwh,
      'cost' => $test_cost,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['date', 'kwh', 'cost']]);

    $test_date = Carbon::now()->addYear()->format('Y-m-d');
    $test_kwh = -1;
    $test_cost = -1;

    $response = $this->withoutMiddleware()->post('/api/fourleaf/bills/electricity', [
      'date' => $test_date,
      'kwh' => $test_kwh,
      'cost' => $test_cost,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['date', 'kwh', 'cost']]);

    $test_valid_date = '2000-01-01';
    $test_valid_cost = 123.12;
    $test_kwh = 'string';

    $response = $this->withoutMiddleware()->post('/api/fourleaf/bills/electricity', [
      'date' => $test_valid_date,
      'kwh' => $test_kwh,
      'cost' => $test_valid_cost,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['kwh']]);
  }

  public function test_should_edit_data_successfully() {
    $this->setup_config();

    $test_date = '2020-10-01';
    $test_kwh = 5000;
    $test_cost = 1234.12;

    $response = $this->withoutMiddleware()
      ->put('/api/fourleaf/bills/electricity/' . $this->bills_electricity_uuid_2, [
        'date' => $test_date,
        'kwh' => $test_kwh,
        'cost' => $test_cost,
      ]);

    $response->assertStatus(200);

    $actual = BillsElectricity::where('uuid', $this->bills_electricity_uuid_2)
      ->first()
      ->toArray();

    $expected = [
      'uid' => intval(Carbon::parse($test_date)->startOfMonth()->format('Ym')),
      'kwh' => $test_kwh,
      'cost' => '' . $test_cost,
    ];

    $this->assertArrayIsEqualToArrayOnlyConsideringListOfKeys(
      $expected,
      $actual,
      ['uid, kwh', 'cost']
    );
  }

  public function test_should_not_edit_data_on_form_errors() {
    $this->setup_config();

    $response = $this->withoutMiddleware()
      ->put('/api/fourleaf/bills/electricity/' . $this->bills_electricity_uuid_1);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['date', 'kwh', 'cost']]);

    $test_date = '2100-01-01';
    $test_kwh = max_int(IntegerTypesEnum::SIGNED, IntegerSizesEnum::SMALL) + 1;
    $test_cost = 'string';

    $response = $this->withoutMiddleware()
      ->put('/api/fourleaf/bills/electricity/' . $this->bills_electricity_uuid_1, [
        'date' => $test_date,
        'kwh' => $test_kwh,
        'cost' => $test_cost,
      ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['date', 'kwh', 'cost']]);

    $test_date = Carbon::now()->addYear()->format('Y-m-d');
    $test_kwh = -1;
    $test_cost = -1;

    $response = $this->withoutMiddleware()
      ->put('/api/fourleaf/bills/electricity/' . $this->bills_electricity_uuid_1, [
        'date' => $test_date,
        'kwh' => $test_kwh,
        'cost' => $test_cost,
      ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['date', 'kwh', 'cost']]);

    $test_valid_date = '2000-01-01';
    $test_valid_cost = 123.12;
    $test_kwh = 'string';

    $response = $this->withoutMiddleware()
      ->put('/api/fourleaf/bills/electricity/' . $this->bills_electricity_uuid_1, [
        'date' => $test_valid_date,
        'kwh' => $test_kwh,
        'cost' => $test_valid_cost,
      ]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['kwh']]);
  }

  public function test_should_not_edit_data_when_id_is_used_instead_of_uuid() {
    $this->setup_config();

    $response = $this->withoutMiddleware()
      ->put('/api/fourleaf/bills/electricity/' . $this->bills_electricity_id_1);

    $response->assertStatus(404);
  }

  public function test_should_not_edit_non_existent_data() {
    $invalid_id = 'aaaaaaaa-1234-1234-1234-aaaaaaaa1234';
    $test_date = '2020-01-01';
    $test_kwh = 123;
    $test_cost = 123;

    $response = $this->withoutMiddleware()
      ->put('/api/fourleaf/bills/electricity/' . $invalid_id, [
        'date' => $test_date,
        'kwh' => $test_kwh,
        'cost' => $test_cost,
      ]);

    $response->assertStatus(404);

    $invalid_id = -1;

    $response = $this->withoutMiddleware()
      ->put('/api/fourleaf/bills/electricity/' . $invalid_id);

    $response->assertStatus(404);
  }

  public function test_should_delete_data_successfully() {
    $this->setup_config();

    $response = $this->withoutMiddleware()
      ->delete('/api/fourleaf/bills/electricity/' . $this->bills_electricity_uuid_1);

    $response->assertStatus(200);

    $actual = BillsElectricity::where('uuid', $this->bills_electricity_uuid_1)->first();

    $this->assertNull($actual);
  }

  public function test_should_not_delete_data_when_id_is_used_instead_of_uuid() {
    $this->setup_config();

    $response = $this->withoutMiddleware()
      ->delete('/api/fourleaf/bills/electricity/' . $this->bills_electricity_id_1);

    $response->assertStatus(404);
  }

  public function test_should_not_delete_non_existent_data() {
    $invalid_id = 'aaaaaaaa-1234-1234-1234-aaaaaaaa1234';

    $response = $this->withoutMiddleware()
      ->delete('/api/fourleaf/bills/electricity/' . $invalid_id);

    $response->assertStatus(404);

    $invalid_id = -1;

    $response = $this->withoutMiddleware()
      ->delete('/api/fourleaf/bills/electricity/' . $invalid_id);

    $response->assertStatus(404);
  }
}
