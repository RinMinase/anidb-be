<?php

namespace Tests\Feature\Fourleaf;

use Exception;
use Carbon\Carbon;
use Tests\BaseTestCase;

use App\Fourleaf\Models\Gas;
use App\Fourleaf\Models\Maintenance;
use App\Fourleaf\Models\MaintenancePart;

class GasTest extends BaseTestCase {

  private $gas_id_1 = 99998;
  private $gas_id_2 = 99999;
  private $maintenance_id_1 = 99999;
  private $maintenance_part_id_1 = 99999;

  private function setup_config() {
    // Clearing possible duplicate data
    $this->setup_clear();

    Gas::insert([[
      'id' => $this->gas_id_1,
      'date' => '2090-04-29',
      'from_bars' => 8,
      'to_bars' => 8,
      'odometer' => 90_000,
      'price_per_liter' => 123.45,
      'liters_filled' => 12.34,
    ], [
      'id' => $this->gas_id_2,
      'date' => '2090-04-30',
      'from_bars' => 8,
      'to_bars' => 8,
      'odometer' => 90_000,
      'price_per_liter' => 123.45,
      'liters_filled' => 12.34,
    ]]);

    Maintenance::insert([
      'id' => $this->maintenance_id_1,
      'date' => '2090-04-29',
      'description' => 'PMS Labor',
      'odometer' => 90_000,
    ]);

    MaintenancePart::insert([
      'id' => $this->maintenance_part_id_1,
      'id_fourleaf_maintenance' => $this->maintenance_id_1,
      'part' => 'engine_oil',
    ]);
  }

  private function setup_clear() {
    Gas::where('id', $this->gas_id_1)
      ->orWhere('id', $this->gas_id_2)
      ->forceDelete();

    Maintenance::where('id', $this->maintenance_id_1)
      ->forceDelete();

    MaintenancePart::where('id', $this->maintenance_part_id_1)
      ->forceDelete();
  }

  public function test_should_get_all_data() {
    $this->setup_config();

    $avg_efficiency_type = 'all';
    $efficiency_graph_type = 'last20data';

    $response = $this->withoutMiddleware()
      ->get(
        '/api/fourleaf/gas?avg_efficiency_type=' .
          $avg_efficiency_type .
          '&efficiency_graph_type=' .
          $efficiency_graph_type
      );

    $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
          'stats' => [
            'averageEfficiency',
            'lastEfficiency',
            'mileage',
            'age',
            'kmPerMonth',
          ],
          'graph' => [
            'efficiency',
            'gas',
          ],
          'maintenance' => [
            'km' => [
              'engineOil',
              'tires',
              'transmissionFluid',
              'brakeFluid',
              'radiatorFluid',
              'sparkPlugs',
              'powerSteeringFluid',
            ],
            'year' => [
              'engineOil',
              'transmissionFluid',
              'brakeFluid',
              'battery',
              'radiatorFluid',
              'acCoolant',
              'powerSteeringFluid',
              'tires',
            ],
          ],
          'lastMaintenance' => [
            'acCoolant',
            'battery',
            'brakeFluid',
            'engineOil',
            'powerSteeringFluid',
            'radiatorFluid',
            'sparkPlugs',
            'tires',
            'transmissionFluid',
          ],
        ],
      ]);

    $this->setup_clear();
  }

  public function test_should_validate_calculated_data_from_get_all_data() {
    // Save existing data
    $backup_data = Gas::all()->toArray();

    try {
      Gas::truncate();

      $test_data = [
        [
          'date' => '2023-04-29',
          'from_bars' => 8,
          'to_bars' => 8,
          'odometer' => 165,
          'price_per_liter' => null,
          'liters_filled' => null,
        ], [
          'date' => '2023-05-03',
          'from_bars' => 4,
          'to_bars' => 8,
          'odometer' => 239,
          'price_per_liter' => 62.85,
          'liters_filled' => 13.93,
        ], [
          'date' => '2023-05-10',
          'from_bars' => 1,
          'to_bars' => 8,
          'odometer' => 386,
          'price_per_liter' => 60.65,
          'liters_filled' => 18.928,
        ], [
          'date' => '2023-05-19',
          'from_bars' => 2,
          'to_bars' => 8,
          'odometer' => 507,
          'price_per_liter' => 61,
          'liters_filled' => 18.739,
        ],
      ];

      foreach ($test_data as $item) {
        Gas::create($item);
      }

      $avg_efficiency_type = 'all';
      $efficiency_graph_type = 'last20data';

      $response = $this->withoutMiddleware()
        ->get(
          '/api/fourleaf/gas?avg_efficiency_type=' .
            $avg_efficiency_type .
            '&efficiency_graph_type=' .
            $efficiency_graph_type
        );

      $expected_stats = [
        'averageEfficiency' => 6.878,
        'lastEfficiency' => 7.118,
        'mileage' => 507,
        'age' => "1 year, 3 months, 14 days",
        'kmPerMonth' => 32.75,
      ];

      $expected_graph = [
        'efficiency' => [
          '2023-05-03' => 6.167,
          '2023-05-10' => 7.35,
          '2023-05-19' => 7.118,
        ],
        'gas' => [
          '2023-05-03' => 62.85,
          '2023-05-10' => 60.65,
          '2023-05-19' => 61,
        ],
      ];

      $this->assertEquals($expected_stats, $response['data']['stats']);
      $this->assertEquals($expected_graph, $response['data']['graph']);
    } catch (Exception $e) {
      throw $e;
    } finally {
      // Restore backup data
      Gas::truncate();

      foreach ($backup_data as $item) {
        Gas::create($item);
      }
    }
  }

  public function test_should_get_all_fuel_data() {
    $this->setup_config();

    $response = $this->withoutMiddleware()
      ->get('/api/fourleaf/gas/fuel');

    $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [[
          'date',
          'fromBars',
          'toBars',
          'odometer',
          'pricePerLiter',
          'litersFilled',
        ]],
      ]);

    $this->setup_clear();
  }

  public function test_should_add_a_fuel_data_successfully() {
    $test_date = '1980-01-01';
    $test_from_bars = 4;
    $test_to_bars = 4;
    $test_odometer = 100_000;
    $test_price_per_liter = 123.45;
    $test_liters_filled = 12.34;

    // Clearing possible duplicate data
    Gas::where('date', $test_date)->delete();

    $response = $this->post('/api/fourleaf/gas/fuel', [
      'date' => $test_date,
      'from_bars' => $test_from_bars,
      'to_bars' => $test_to_bars,
      'odometer' => $test_odometer,
      'price_per_liter' => $test_price_per_liter,
      'liters_filled' => $test_liters_filled,
    ]);

    $response->assertStatus(200);

    $actual = Gas::where('date', $test_date)->first();

    $this->assertEquals($test_date, $actual->date);
    $this->assertEquals($test_from_bars, $actual->from_bars);
    $this->assertEquals($test_to_bars, $actual->to_bars);
    $this->assertEquals($test_odometer, $actual->odometer);
    $this->assertEquals($test_price_per_liter, $actual->price_per_liter);
    $this->assertEquals($test_liters_filled, $actual->liters_filled);

    // Clearing test data
    Gas::where('date', $test_date)->delete();
  }

  public function test_should_not_add_a_fuel_data_on_form_errors() {
    $test_date = '2090-01-01';
    $test_from_bars = 4;
    $test_to_bars = 2;
    $test_odometer = 100_001;
    $test_price_per_liter = 'string';
    $test_liters_filled = 'string';

    $response = $this->post('/api/fourleaf/gas/fuel', [
      'date' => $test_date,
      'from_bars' => $test_from_bars,
      'to_bars' => $test_to_bars,
      'odometer' => $test_odometer,
      'price_per_liter' => $test_price_per_liter,
      'liters_filled' => $test_liters_filled,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure([
        'data' => [
          'date',
          'to_bars',
          'odometer',
          'price_per_liter',
          'liters_filled',
        ],
      ]);

    $test_from_bars = 1;
    $test_to_bars = 999;

    $response = $this->post('/api/fourleaf/gas/fuel', [
      'date' => $test_date,
      'from_bars' => $test_from_bars,
      'to_bars' => $test_to_bars,
      'odometer' => $test_odometer,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure([
        'data' => [
          'to_bars',
        ],
      ]);
  }

  public function test_should_edit_a_fuel_data_successfully() {
    $this->setup_config();

    $test_date = '1980-01-01';
    $test_from_bars = 4;
    $test_to_bars = 5;
    $test_odometer = 100_000;
    $test_price_per_liter = 123.45;
    $test_liters_filled = 12.34;

    $response = $this->put('/api/fourleaf/gas/fuel/' . $this->gas_id_1, [
      'date' => $test_date,
      'from_bars' => $test_from_bars,
      'to_bars' => $test_to_bars,
      'odometer' => $test_odometer,
      'price_per_liter' => $test_price_per_liter,
      'liters_filled' => $test_liters_filled,
    ]);

    $response->assertStatus(200);

    $actual = Gas::where('date', $test_date)
      ->where('odometer', $test_odometer)
      ->first();

    $this->assertEquals($test_date, $actual->date);
    $this->assertEquals($test_from_bars, $actual->from_bars);
    $this->assertEquals($test_to_bars, $actual->to_bars);
    $this->assertEquals($test_odometer, $actual->odometer);
    $this->assertEquals($test_price_per_liter, $actual->price_per_liter);
    $this->assertEquals($test_liters_filled, $actual->liters_filled);

    $this->setup_clear();
  }

  public function test_should_not_edit_a_fuel_data_on_form_errors() {
    $test_date = '2090-01-01';
    $test_from_bars = 4;
    $test_to_bars = 2;
    $test_odometer = 100_001;
    $test_price_per_liter = 'string';
    $test_liters_filled = 'string';

    $response = $this->put('/api/fourleaf/gas/fuel/' . $this->gas_id_1, [
      'date' => $test_date,
      'from_bars' => $test_from_bars,
      'to_bars' => $test_to_bars,
      'odometer' => $test_odometer,
      'price_per_liter' => $test_price_per_liter,
      'liters_filled' => $test_liters_filled,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure([
        'data' => [
          'date',
          'to_bars',
          'odometer',
          'price_per_liter',
          'liters_filled',
        ],
      ]);

    $test_from_bars = 1;
    $test_to_bars = 999;

    $response = $this->put('/api/fourleaf/gas/fuel/' . $this->gas_id_1, [
      'date' => $test_date,
      'from_bars' => $test_from_bars,
      'to_bars' => $test_to_bars,
      'odometer' => $test_odometer,
    ]);

    $response->assertStatus(401)
      ->assertJsonStructure([
        'data' => [
          'to_bars',
        ],
      ]);
  }

  public function test_should_delete_a_fuel_data_successfully() {
    $this->setup_config();

    $response = $this->delete('/api/fourleaf/gas/fuel/' . $this->gas_id_1);

    $response->assertStatus(200);

    $actual = Gas::where('id', $this->gas_id_1)
      ->first();

    $this->assertNull($actual);

    $this->setup_clear();
  }

  public function test_should_not_delete_a_fuel_data_on_invalid_id() {
    $invalid_id = 100_000;

    $response = $this->delete('/api/fourleaf/gas/fuel/' . $invalid_id);

    $response->assertStatus(404);
  }

  public function test_should_get_all_maintenance_data() {
    $this->setup_config();

    $response = $this->withoutMiddleware()
      ->get('/api/fourleaf/gas/maintenance');

    $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [[
          'date',
          'description',
          'parts' => [[]],
          'odometer',
        ]],
      ]);

    $this->setup_clear();
  }

  public function test_should_get_all_maintenance_part_list() {
    $this->setup_config();

    $response = $this->withoutMiddleware()
      ->get('/api/fourleaf/gas/maintenance/parts');

    $response->assertStatus(200)
      ->assertJsonStructure(['data' => [[]]]);

    $this->setup_clear();
  }
}
