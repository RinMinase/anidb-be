<?php

namespace App\Fourleaf\Repositories;

use Carbon\Carbon;

use App\Fourleaf\Models\Gas;
use App\Fourleaf\Models\Maintenance;
use App\Fourleaf\Models\MaintenancePart;
use Illuminate\Support\Facades\DB;

class GasRepository {
  /**
   * Overview Function
   */

  public function get($avg_efficiency_type, $efficiency_graph_type) {
    $avg_efficiency_type = $avg_efficiency_type ?? 'all';
    $efficiency_graph_type = $efficiency_graph_type ?? 'last20data';

    $age = $this->calculateAge();
    $mileage = Gas::select('odometer')
      ->orderBy('id', 'desc')
      ->first()
      ->odometer;

    $km_per_month = $this->calculateKMperMonth($mileage);
    $maintenance = $this->calculateMaintenanceStatus($mileage);
    $last_maintenance = $this->fetchLastMaintenanceDates();
    $avg_efficiency_list = $this->calculateEfficiencyList($avg_efficiency_type);
    $last_efficiency = $avg_efficiency_list[array_key_last($avg_efficiency_list)];

    $avg_efficiency_list_values = array_values($avg_efficiency_list);
    $avg_efficiency = array_sum($avg_efficiency_list_values) / count($avg_efficiency_list_values);
    $avg_efficiency = round($avg_efficiency, 3);

    $graph_efficiency_data = $this->calculateEfficiencyList($efficiency_graph_type);

    if ($efficiency_graph_type === 'last12mos') {
      // post process group by month
      $graph_efficiency_data = $this->postProcessEfficiencyListByMonth($graph_efficiency_data);
    }

    $graph_gas_data = $this->calculateGasList();

    /**
     * check kms by last odo
     * - also check if existing maintenance log is created for part
     *
     * check years by car years
     * - also check if existing maintenance log is created for part
     */

    return [
      'stats' => [
        'average_efficiency' => $avg_efficiency,
        'last_efficiency' => $last_efficiency,
        'mileage' => $mileage,
        'age' => $age,
        'km_per_month' => $km_per_month,
      ],
      'graph' => [
        'efficiency' => $graph_efficiency_data,
        'gas' => $graph_gas_data,
      ],
      'maintenance' => $maintenance,
      'last_maintenance' => $last_maintenance,
    ];
  }

  /**
   * Gas Functions
   */

  public function getFuel() {
    return Gas::all();
  }

  public function addFuel(array $values) {
    return Gas::create($values);
  }

  public function editFuel(array $values, $id) {
    return Gas::where('id', $id)
      ->firstOrFail()
      ->update($values);
  }

  public function deleteFuel($id) {
    return Gas::where('id', $id)
      ->firstOrFail()
      ->delete();
  }

  /**
   * Maintenance Functions
   */

  public function getMaintenance() {
    return Maintenance::select()->with('parts')->get();
  }

  public function addMaintenance(array $values) {
    $parts = $values['parts'];
    unset($values['parts']);

    $maintenance_id = Maintenance::insertGetId($values);

    $maintenance_parts = [];
    foreach ($parts as $key => $value) {
      if ($value) {
        array_push($maintenance_parts, $key);
      }
    }


    foreach ($maintenance_parts as $part) {
      MaintenancePart::create([
        'id_fourleaf_maintenance' => $maintenance_id,
        'part' => $part,
      ]);
    }
  }

  public function editMaintenance(array $values, $id) {
    return Maintenance::where('id', $id)
      ->firstOrFail()
      ->update($values);
  }

  public function deleteMaintenance($id) {
    return Maintenance::where('id', $id)
      ->firstOrFail()
      ->delete();
  }

  public function getMaintenanceParts() {
    return [
      'others',
      'ac_coolant',
      'battery',
      'brake_fluid',
      'engine_oil',
      'power_steering_fluid',
      'radiator_fluid',
      'spark_plugs',
      'tires',
      'transmission',
    ];
  }

  /**
   * Calculation Functions
   */

  private function calculateAge(): string {
    $vehicle_start_age = Carbon::parse(config('app.vehicle_start_date'));
    $date_now = Carbon::now();

    $age_difference = $vehicle_start_age->diff($date_now);
    $age = '';

    if ($age_difference->y) {
      if ($age_difference->y > 1) {
        $age .= $age_difference->y . ' years';
      } else {
        $age .= $age_difference->y . ' year';
      }
    }

    if ($age_difference->m) {
      if ($age_difference->y) {
        $age .= ', ';
      }

      if ($age_difference->m > 1) {
        $age .= $age_difference->m . ' months';
      } else {
        $age .= $age_difference->m . ' month';
      }
    }

    if ($age_difference->d) {
      if ($age_difference->y || $age_difference->m) {
        $age .= ', ';
      }

      if ($age_difference->d > 1) {
        $age .= $age_difference->d . ' days';
      } else {
        $age .= $age_difference->d . ' day';
      }
    }

    return $age;
  }

  private function calculateAgeYears(): float {
    $vehicle_start_age = Carbon::parse(config('app.vehicle_start_date'));
    $date_now = Carbon::now();

    return $vehicle_start_age->floatDiffInYears($date_now);
  }

  private function calculateKMperMonth(int $mileage): float {
    $vehicle_start_age = Carbon::parse(config('app.vehicle_start_date'));
    $date_now = Carbon::now();
    $months = $vehicle_start_age->floatDiffInMonths($date_now);

    return round($mileage / $months, 2);
  }

  private function calculateMaintenanceStatus(int $mileage) {
    $ageYears = $this->calculateAgeYears();

    $maintenance = [
      'km' => [
        'engine_oil' => 'normal',
        'tires' => 'normal',
        'transmission_fluid' => 'normal',
        'brake_fluid' => 'normal',
        'radiator_fluid' => 'normal',
        'spark_plugs' => 'normal',
        'power_steering_fluid' => 'normal',
      ],
      'year' => [
        'engine_oil' => 'normal',
        'transmission_fluid' => 'normal',
        'brake_fluid' => 'normal',
        'battery' => 'normal',
        'radiator_fluid' => 'normal',
        'ac_coolant' => 'normal',
        'power_steering_fluid' => 'normal',
        'tires' => 'normal',
      ],
    ];

    $limits = [
      'km' => [
        'engine_oil' => 8000,
        'tires' => 20000,
        'transmission_fluid' => 50000,
        'brake_fluid' => 50000,
        'radiator_fluid' => 50000,
        'spark_plugs' => 50000,
        'power_steering_fluid' => 100000,
      ],
      'year' => [
        'engine_oil' => 1,
        'transmission_fluid' => 2,
        'brake_fluid' => 2,
        'battery' => 3,
        'radiator_fluid' => 3,
        'ac_coolant' => 3,
        'power_steering_fluid' => 5,
        'tires' => 5,
      ],
    ];

    foreach (array_keys($maintenance) as $type) {
      foreach (array_keys($maintenance[$type]) as $key) {
        $target_distance = 0.0;

        // percentage of mileage / age
        if ($type === 'km') {
          $target_distance = $mileage / $limits[$type][$key];
        } else {
          $target_distance = $ageYears / $limits[$type][$key];
        }

        // get decimal places
        $target_distance = $target_distance - (int)$target_distance;

        // percentage of decimals
        $target_distance = round($target_distance * 100, 1);

        /**
         * Percentage guide:
         *
         * NORMAL === > 3 to <=95
         * NEARING === > 95 to < 100
         * LIMIT === >=100 to <= 103 (3)
         */

        if (($target_distance >= 99.5 && $target_distance <= 100) || $target_distance <= 3) {
          $maintenance[$type][$key] = 'limit';
        } else if ($target_distance > 95 && $target_distance < 99.5) {
          $maintenance[$type][$key] = 'nearing';
        }
      }
    }

    return $maintenance;
  }

  private function calculateEfficiencyList(string $avg_efficiency_type): array {
    $data = [];

    $data = Gas::select('date', 'from_bars', 'to_bars', 'odometer', 'liters_filled');

    if ($avg_efficiency_type === 'last20data') {
      $data = $data->orderBy('id', 'desc')
        ->limit(21)
        ->get()
        ->reverse()
        ->values();
    } else if ($avg_efficiency_type === 'last10') {
      $data = $data->orderBy('id', 'desc')
        ->limit(11)
        ->get()
        ->reverse()
        ->values();
    } else if ($avg_efficiency_type === 'last5') {
      $data = $data->orderBy('id', 'desc')
        ->limit(6)
        ->get()
        ->reverse()
        ->values();
    } else if ($avg_efficiency_type === 'last12mos') {
      $end_date = Carbon::now()
        ->subMonths(11)
        ->startOfMonth()
        ->format('Y-m-d');

      $data = $data->where('date', '>=', $end_date)
        ->orderBy('id', 'asc')
        ->get();
    } else {
      $data = $data->get();
    }

    $data = $data->toArray();

    $tank_table = [3, 6, 9, 11, 14, 17, 20, 23, 26, 28];
    $efficiency_list = [];

    foreach ($data as $index => $value) {
      if ($index === 0) {
        continue;
      }

      $past_liters = $tank_table[$data[$index - 1]['to_bars']];
      $curr_liters = $tank_table[$value['from_bars']];
      $past_odo = $data[$index - 1]['odometer'];
      $curr_odo = $value['odometer'];

      // if bars are the same for two consequent data points
      $last_liters_filled = $data[$index]['liters_filled'];

      $date = Carbon::parse($value['date'])->format('Y-m-d');

      $liters_consumed = ($past_liters - $curr_liters) ?: $last_liters_filled;
      $efficiency_single = ($curr_odo - $past_odo) / $liters_consumed;
      $efficiency_single = round($efficiency_single, 3);

      $efficiency_list[$date] = $efficiency_single;
    }

    return $efficiency_list;
  }

  private function postProcessEfficiencyListByMonth(array $efficiency_list): array {
    $months = [
      'jan' => [],
      'feb' => [],
      'mar' => [],
      'apr' => [],
      'may' => [],
      'jun' => [],
      'jul' => [],
      'aug' => [],
      'sep' => [],
      'oct' => [],
      'nov' => [],
      'dec' => [],
    ];

    $efficiency_months = [
      'jan' => 0.0,
      'feb' => 0.0,
      'mar' => 0.0,
      'apr' => 0.0,
      'may' => 0.0,
      'jun' => 0.0,
      'jul' => 0.0,
      'aug' => 0.0,
      'sep' => 0.0,
      'oct' => 0.0,
      'nov' => 0.0,
      'dec' => 0.0,
    ];

    // Categorize list per month
    foreach ($efficiency_list as $date => $efficiency) {
      $item_month = Carbon::parse($date)->shortMonthName;
      $item_month = strtolower($item_month);

      array_push($months[$item_month], $efficiency);
    }

    // Calculate avg efficiency per month
    foreach ($months as $month => $values) {
      if (!count($values)) {
        continue;
      }

      $average = array_sum($values) / count($values);
      $efficiency_months[$month] = round($average, 3);
    }

    return $efficiency_months;
  }

  private function calculateGasList(): array {
    $gas_list = [];

    $data = Gas::select('date', 'price_per_liter')
      ->whereNotNull('price_per_liter')
      ->orderBy('id', 'desc')
      ->limit(20)
      ->get()
      ->reverse()
      ->values();

    foreach ($data as $item) {
      $gas_list[$item->date] = (float) $item->price_per_liter;
    }

    return $gas_list;
  }

  private function fetchLastMaintenanceDates(): array {
    $last_maintenance = MaintenancePart::select('part')
      ->addselect(DB::raw('max(odometer) as odometer'))
      ->addselect(DB::raw('max(date) as date'))
      ->leftJoin('fourleaf_maintenance', function ($join) {
        $join->on(
          'fourleaf_maintenance_parts.id_fourleaf_maintenance',
          '=',
          'fourleaf_maintenance.id',
        );
      })
      ->groupBy('part')
      ->orderBy('part', 'asc')
      ->get()
      ->keyBy('part');

    return [
      'ac_coolant' => [
        'date' => $last_maintenance->get('ac_coolant') ? $last_maintenance->get('ac_coolant')->date : null,
        'odometer' => $last_maintenance->get('ac_coolant') ? $last_maintenance->get('ac_coolant')->odometer : null,
      ],
      'battery' => [
        'date' => $last_maintenance->get('battery') ? $last_maintenance->get('battery')->date : null,
        'odometer' => $last_maintenance->get('battery') ? $last_maintenance->get('battery')->odometer : null,
      ],
      'brake_fluid' => [
        'date' => $last_maintenance->get('brake_fluid') ? $last_maintenance->get('brake_fluid')->date : null,
        'odometer' => $last_maintenance->get('brake_fluid') ? $last_maintenance->get('brake_fluid')->odometer : null,
      ],
      'engine_oil' => [
        'date' => $last_maintenance->get('engine_oil') ? $last_maintenance->get('engine_oil')->date : null,
        'odometer' => $last_maintenance->get('engine_oil') ? $last_maintenance->get('engine_oil')->odometer : null,
      ],
      'power_steering_fluid' => [
        'date' => $last_maintenance->get('power_steering_fluid') ? $last_maintenance->get('power_steering_fluid')->date : null,
        'odometer' => $last_maintenance->get('power_steering_fluid') ? $last_maintenance->get('power_steering_fluid')->odometer : null,
      ],
      'radiator_fluid' => [
        'date' => $last_maintenance->get('radiator_fluid') ? $last_maintenance->get('radiator_fluid')->date : null,
        'odometer' => $last_maintenance->get('radiator_fluid') ? $last_maintenance->get('radiator_fluid')->odometer : null,
      ],
      'spark_plugs' => [
        'date' => $last_maintenance->get('spark_plugs') ? $last_maintenance->get('spark_plugs')->date : null,
        'odometer' => $last_maintenance->get('spark_plugs') ? $last_maintenance->get('spark_plugs')->odometer : null,
      ],
      'tires' => [
        'date' => $last_maintenance->get('tires') ? $last_maintenance->get('tires')->date : null,
        'odometer' => $last_maintenance->get('tires') ? $last_maintenance->get('tires')->odometer : null,
      ],
      'transmission_fluid' => [
        'date' => $last_maintenance->get('transmission_fluid') ? $last_maintenance->get('transmission_fluid')->date : null,
        'odometer' => $last_maintenance->get('transmission_fluid') ? $last_maintenance->get('transmission_fluid')->odometer : null,
      ],
    ];
  }
}
