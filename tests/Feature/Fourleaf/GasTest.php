<?php

namespace Tests\Feature\Fourleaf;

use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Tests\BaseTestCase;

use App\Fourleaf\Models\Gas;
use App\Fourleaf\Models\Maintenance;
use App\Fourleaf\Models\MaintenancePart;

class GasTest extends BaseTestCase {

  // Backup related variables
  private $gas_backup = null;
  private $maintenance_backup = null;
  private $maintenance_part_backup = null;

  // Class variables
  private $gas_id_1 = 99998;
  private $gas_date_1 = '2090-04-29';
  private $gas_from_bars_1 = 8;
  private $gas_to_bars_1 = 8;
  private $gas_odometer_1 = 90_000;
  private $gas_price_1 = 213.12;
  private $gas_liters_1 = 12.34;

  private $gas_id_2 = 99999;
  private $gas_date_2 = '2090-04-30';
  private $gas_from_bars_2 = 8;
  private $gas_to_bars_2 = 8;
  private $gas_odometer_2 = 100_000;
  private $gas_price_2 = 123.45;
  private $gas_liters_2 = 12.34;

  private $maintenance_id_1 = 99999;
  private $maintenance_part_id_1 = 99999;

  // Backup related tables
  private function setup_backup() {
    $this->gas_backup = Gas::all()->toArray();
    $this->maintenance_backup = Maintenance::all()->toArray();
    $this->maintenance_part_backup = MaintenancePart::all()->toArray();
  }

  // Restore related tables
  private function setup_restore() {
    Gas::truncate();
    Gas::insert($this->gas_backup);
    Gas::refreshAutoIncrements();

    Maintenance::truncate(); // cascaded to maintenance parts

    Maintenance::insert($this->maintenance_backup);
    Maintenance::refreshAutoIncrements();

    MaintenancePart::insert($this->maintenance_part_backup);
    MaintenancePart::refreshAutoIncrements();
  }

  // Setup data for testing
  private function setup_config() {
    Gas::truncate();
    Maintenance::truncate();

    Gas::insert([
      [
        'id' => $this->gas_id_1,
        'date' => $this->gas_date_1,
        'from_bars' => $this->gas_from_bars_1,
        'to_bars' => $this->gas_to_bars_1,
        'odometer' => $this->gas_odometer_1,
        'price_per_liter' => $this->gas_price_1,
        'liters_filled' => $this->gas_liters_1,
      ],
      [
        'id' => $this->gas_id_2,
        'date' => $this->gas_date_2,
        'from_bars' => $this->gas_from_bars_2,
        'to_bars' => $this->gas_to_bars_2,
        'odometer' => $this->gas_odometer_2,
        'price_per_liter' => $this->gas_price_2,
        'liters_filled' => $this->gas_liters_2,
      ]
    ]);

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
            'odometer'
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
  }

  public function test_should_validate_calculated_data_from_get_all_data() {
    try {
      // Mock date values
      Carbon::setTestNow(Carbon::parse('2023-05-20'));
      Config::set('app.vehicle_start_date', '2023-04-25');

      Gas::truncate();
      Gas::refreshAutoIncrements();

      $test_data = [
        [
          'date' => '2023-04-29',
          'from_bars' => 8,
          'to_bars' => 8,
          'odometer' => 165,
          'price_per_liter' => null,
          'liters_filled' => null,
        ],
        [
          'date' => '2023-05-03',
          'from_bars' => 4,
          'to_bars' => 8,
          'odometer' => 239,
          'price_per_liter' => 62.85,
          'liters_filled' => 13.93,
        ],
        [
          'date' => '2023-05-10',
          'from_bars' => 1,
          'to_bars' => 8,
          'odometer' => 386,
          'price_per_liter' => 60.65,
          'liters_filled' => 18.928,
        ],
        [
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

      $expected_avg_eff = 6.878;
      $expected_last_eff = 7.118;
      $expected_mileage = 507;
      $expected_age = '25 days';
      $expected_km_per_month = 608.4;

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
        'odometer' => [0, 0, 0, 0, 268, 0, 0, 0, 0, 0, 0, 0]
      ];

      $actual_stats = $response['data']['stats'];

      $this->assertEquals($expected_mileage, $actual_stats['mileage']);
      $this->assertEquals($expected_age, $actual_stats['age']);

      $this->assertEqualsWithDelta($expected_avg_eff, $actual_stats['averageEfficiency'], 0.5);
      $this->assertEqualsWithDelta($expected_last_eff, $actual_stats['lastEfficiency'], 0.5);
      $this->assertEqualsWithDelta($expected_km_per_month, $actual_stats['kmPerMonth'], 0.5);

      $this->assertEquals($expected_graph, $response['data']['graph']);
    } finally {
      // Restore mocks
      Carbon::setTestNow();
      Config::set('app.vehicle_start_date', env('VEHICLE_START_DATE', '2023-01-01'));
    }
  }

  public function test_should_get_all_fuel_data() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->get('/api/fourleaf/gas/fuel');

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

    $expected = [
      [
        'id' => $this->gas_id_1,
        'date' => $this->gas_date_1,
        'fromBars' => $this->gas_from_bars_1,
        'toBars' => $this->gas_to_bars_1,
        'odometer' => $this->gas_odometer_1,
        'pricePerLiter' => $this->gas_price_1,
        'litersFilled' => $this->gas_liters_1,
      ],
      [
        'id' => $this->gas_id_2,
        'date' => $this->gas_date_2,
        'fromBars' => $this->gas_from_bars_2,
        'toBars' => $this->gas_to_bars_2,
        'odometer' => $this->gas_odometer_2,
        'pricePerLiter' => $this->gas_price_2,
        'litersFilled' => $this->gas_liters_2,
      ]
    ];

    $this->assertCount(count($expected), $response['data']);
    $this->assertEquals($expected, $response['data']);
  }

  public function test_should_get_all_fuel_data_sorted_by_column() {
    $this->setup_config();

    $test_column = 'price_per_liter';

    $response = $this->withoutMiddleware()->get('/api/fourleaf/gas/fuel?column=' . $test_column);

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

    $expected = [
      [
        'id' => $this->gas_id_2,
        'date' => $this->gas_date_2,
        'fromBars' => $this->gas_from_bars_2,
        'toBars' => $this->gas_to_bars_2,
        'odometer' => $this->gas_odometer_2,
        'pricePerLiter' => $this->gas_price_2,
        'litersFilled' => $this->gas_liters_2,
      ],
      [
        'id' => $this->gas_id_1,
        'date' => $this->gas_date_1,
        'fromBars' => $this->gas_from_bars_1,
        'toBars' => $this->gas_to_bars_1,
        'odometer' => $this->gas_odometer_1,
        'pricePerLiter' => $this->gas_price_1,
        'litersFilled' => $this->gas_liters_1,
      ],
    ];

    $this->assertCount(count($expected), $response['data']);
    $this->assertEquals($expected, $response['data']);
  }

  public function test_should_get_all_fuel_data_ordered() {
    $this->setup_config();

    $test_column = 'odometer';
    $test_order = 'desc';

    $response = $this->withoutMiddleware()
      ->get(
        '/api/fourleaf/gas/fuel?column=' . $test_column .
          '&order=' . $test_order
      );

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

    $expected = [
      [
        'id' => $this->gas_id_2,
        'date' => $this->gas_date_2,
        'fromBars' => $this->gas_from_bars_2,
        'toBars' => $this->gas_to_bars_2,
        'odometer' => $this->gas_odometer_2,
        'pricePerLiter' => $this->gas_price_2,
        'litersFilled' => $this->gas_liters_2,
      ],
      [
        'id' => $this->gas_id_1,
        'date' => $this->gas_date_1,
        'fromBars' => $this->gas_from_bars_1,
        'toBars' => $this->gas_to_bars_1,
        'odometer' => $this->gas_odometer_1,
        'pricePerLiter' => $this->gas_price_1,
        'litersFilled' => $this->gas_liters_1,
      ],
    ];

    $this->assertCount(count($expected), $response['data']);
    $this->assertEquals($expected, $response['data']);
  }

  public function test_should_get_all_fuel_data_by_page() {
    $this->setup_config();

    $test_column = 'odometer';
    $test_order = 'desc';
    $test_page = 1;
    $test_limit = 1;

    $response = $this->withoutMiddleware()
      ->get(
        '/api/fourleaf/gas/fuel?column=' . $test_column .
          '&order=' . $test_order .
          '&page=' . $test_page .
          '&limit=' . $test_limit
      );

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

    $expected = [
      [
        'id' => $this->gas_id_2,
        'date' => $this->gas_date_2,
        'fromBars' => $this->gas_from_bars_2,
        'toBars' => $this->gas_to_bars_2,
        'odometer' => $this->gas_odometer_2,
        'pricePerLiter' => $this->gas_price_2,
        'litersFilled' => $this->gas_liters_2,
      ],
    ];

    $this->assertCount(count($expected), $response['data']);
    $this->assertEquals($expected, $response['data']);
  }

  public function test_should_get_all_fuel_data_with_limit() {
    $this->setup_config();

    $test_column = 'odometer';
    $test_order = 'desc';
    $test_page = 2;
    $test_limit = 1;

    $response = $this->withoutMiddleware()
      ->get(
        '/api/fourleaf/gas/fuel?column=' . $test_column .
          '&order=' . $test_order .
          '&page=' . $test_page .
          '&limit=' . $test_limit
      );

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

    $expected = [
      [
        'id' => $this->gas_id_1,
        'date' => $this->gas_date_1,
        'fromBars' => $this->gas_from_bars_1,
        'toBars' => $this->gas_to_bars_1,
        'odometer' => $this->gas_odometer_1,
        'pricePerLiter' => $this->gas_price_1,
        'litersFilled' => $this->gas_liters_1,
      ],
    ];

    $this->assertCount(count($expected), $response['data']);
    $this->assertEquals($expected, $response['data']);
  }

  public function test_should_add_fuel_data_successfully() {
    $test_date = '1980-01-01';
    $test_from_bars = 4;
    $test_to_bars = 4;
    $test_odometer = 100_000;
    $test_price_per_liter = 123.45;
    $test_liters_filled = 12.34;

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
  }

  public function test_should_not_add_fuel_data_on_form_errors() {
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

  public function test_should_edit_fuel_data_successfully() {
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
  }

  public function test_should_not_edit_fuel_data_on_form_errors() {
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

  public function test_should_delete_fuel_data_successfully() {
    $this->setup_config();

    $response = $this->delete('/api/fourleaf/gas/fuel/' . $this->gas_id_1);

    $response->assertStatus(200);

    $actual = Gas::where('id', $this->gas_id_1)->first();

    $this->assertNull($actual);
  }

  public function test_should_not_delete_fuel_data_on_invalid_id() {
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
  }

  public function test_should_get_all_maintenance_part_list() {
    $this->setup_config();

    $response = $this->withoutMiddleware()
      ->get('/api/fourleaf/gas/maintenance/parts');

    $response->assertStatus(200)
      ->assertJsonStructure(['data' => [[]]]);
  }
}
