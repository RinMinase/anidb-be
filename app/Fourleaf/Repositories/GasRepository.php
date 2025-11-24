<?php

namespace App\Fourleaf\Repositories;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use App\Fourleaf\Exceptions\Gas\InvalidYearException;

use App\Fourleaf\Models\Gas;
use App\Fourleaf\Models\Maintenance;
use App\Fourleaf\Models\MaintenancePart;
use App\Fourleaf\Models\MaintenanceType;

class GasRepository {
  /**
   * Overview Function
   */

  public function get($avg_efficiency_type, $efficiency_graph_type) {
    $avg_efficiency_type = $avg_efficiency_type ?? 'all';
    $efficiency_graph_type = $efficiency_graph_type ?? 'last20data';

    $age = $this->calculateAge();
    $mileage = Gas::select('odometer')
      ->orderBy('date', 'desc')
      ->first()
      ->odometer;

    $km_per_month = $this->calculateKMperMonth($mileage);
    $maintenance = $this->calculateMaintenanceStatus($mileage);

    // TODO: Temporarily blanked array due to changes in maintenance types
    // $last_maintenance = $this->fetchLastMaintenanceDates();
    $last_maintenance = [];

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
   * Odometer Functions
   */

  public function getOdo($year) {
    return $this->calculateOdometerPerMonth(intval($year));
  }

  /**
   * Gas Functions
   */

  public function getFuel($params) {
    // Ordering Parameters
    $column = $params['column'] ?? 'odometer';
    $order = $params['order'] ?? 'asc';

    // Pagination Parameters
    $limit = isset($params['limit']) ? intval($params['limit']) : 30;
    $page = isset($params['page']) ? intval($params['page']) : 1;
    $skip = ($page > 1) ? ($page * $limit - $limit) : 0;

    $gas = Gas::orderBy($column, $order)
      ->orderBy('id', 'asc');

    $total = $gas->count();
    $total_pages = ceil($total / $limit);
    $has_next = $page < $total_pages;

    $gas = $gas->skip($skip)
      ->paginate($limit);

    return [
      'data' => JsonResource::collection($gas),
      'meta' => [
        'page' => $page,
        'limit' => intval($limit),
        'results' => count($gas),
        'total_results' => $total,
        'total_pages' => $total_pages,
        'has_next' => $has_next,
      ],
    ];
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
   * Import / Export functions
   */

  public function import(array $gas, array $maintenance) {
    $import_gas = [];

    foreach ($gas as $item) {
      $is_valid_entry = !empty($item->date) &&
        isset($item->from_bars) &&
        isset($item->to_bars) &&
        !empty($item->odometer);

      if (!empty($item) && $is_valid_entry) {
        $data = [
          'date' => $item->date,
          'from_bars' => $item->from_bars,
          'to_bars' => $item->to_bars,
          'odometer' => $item->odometer,
          'price_per_liter' => $item->price_per_liter ?? null,
          'liters_filled' => $item->liters_filled ?? null,
        ];

        array_push($import_gas, $data);
      }
    }

    Gas::truncate();
    Gas::refreshAutoIncrements();
    Gas::insert($import_gas);
    Gas::refreshAutoIncrements();

    $count_maintenance = 0;
    $count_maintenance_parts = 0;

    $maintenance_types = MaintenanceType::all();

    Maintenance::truncate();
    MaintenancePart::truncate();

    Maintenance::refreshAutoIncrements();

    foreach ($maintenance as $item) {
      $is_valid_entry = !empty($item->date) &&
        !empty($item->description) &&
        !empty($item->odometer) &&
        !empty($item->parts) &&
        is_array($item->parts);

      if (!empty($item) && $is_valid_entry) {
        $data = [
          'date' => $item->date,
          'description' => $item->description,
          'odometer' => $item->odometer,
        ];

        $data_types = [];

        foreach ($item->parts as $tentative_part) {
          $partial_type = $maintenance_types->where('type', $tentative_part)->first();

          if ($partial_type) array_push($data_types, $partial_type->id);
        }

        $partial_data_id = Maintenance::insertGetId($data);
        $count_maintenance++;

        foreach ($data_types as $proper_part_id) {
          MaintenancePart::create([
            'id_fourleaf_maintenance' => $partial_data_id,
            'id_fourleaf_maintenance_type' => $proper_part_id,
          ]);

          $count_maintenance_parts++;
        }
      }
    }

    Maintenance::refreshAutoIncrements();

    return [
      'gas' => count($import_gas),
      'maintenance' => $count_maintenance,
      'parts' => $count_maintenance_parts,
    ];
  }

  public function export() {
    $gas_data = Gas::select('date', 'from_bars', 'to_bars', 'odometer', 'price_per_liter', 'liters_filled')
      ->get()
      ->toArray();

    $maintenance_data = Maintenance::select('date', 'description', 'odometer')
      ->addSelect(DB::raw('\'[\' || string_agg(fourleaf_maintenance_types.type, \', \') || \']\' AS raw_parts'))
      ->leftJoin(
        'fourleaf_maintenance_parts',
        'fourleaf_maintenance_parts.id_fourleaf_maintenance',
        '=',
        'fourleaf_maintenance.id'
      )
      ->leftJoin(
        'fourleaf_maintenance_types',
        'fourleaf_maintenance_types.id',
        '=',
        'fourleaf_maintenance_parts.id_fourleaf_maintenance_type'
      )
      ->groupBy(
        'fourleaf_maintenance.id',
        'fourleaf_maintenance.date',
        'fourleaf_maintenance.description',
        'fourleaf_maintenance.odometer'
      )
      ->orderBy('fourleaf_maintenance.id')
      ->get()
      ->toArray();

    foreach ($maintenance_data as &$row) {
      $types = trim($row["raw_parts"], "[]"); // remove brackets
      $row["parts"] = array_map('trim', explode(',', $types)); // split into array
      unset($row["raw_parts"]); // remove original key
    }

    $object_for_export = [
      'gas' => $gas_data,
      'maintenance' => $maintenance_data,
    ];

    $data = json_encode($object_for_export, JSON_PRETTY_PRINT);

    // Create the json file
    $filename = 'gas_' . now()->timestamp . '.json';

    Storage::disk('local')->put("db-dumps/{$filename}", $data);

    return [
      'file' => Storage::disk('local')->path("db-dumps/{$filename}"),
      'filename' => $filename,
      'headers' => ['Content-Type' => 'application/json'],
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
      $data = $data->orderBy('date', 'desc')
        ->orderBy('odometer', 'desc')
        ->limit(21)
        ->get()
        ->reverse()
        ->values();
    } else if ($avg_efficiency_type === 'last10') {
      $data = $data->orderBy('date', 'desc')
        ->orderBy('odometer', 'desc')
        ->limit(11)
        ->get()
        ->reverse()
        ->values();
    } else if ($avg_efficiency_type === 'last5') {
      $data = $data->orderBy('date', 'desc')
        ->orderBy('odometer', 'desc')
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
        ->orderBy('date', 'asc')
        ->orderBy('odometer', 'asc')
        ->get();
    } else {
      $data = $data->orderBy('date', 'asc')
        ->orderBy('odometer', 'asc')
        ->get();
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
      $last_liters_by_bars = $tank_table[$value['to_bars']] - $tank_table[$value['from_bars']];
      $last_liters_filled = abs($last_liters_by_bars - $data[$index]['liters_filled']);

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

  private function calculateOdometerPerMonth(int $year) {
    $min_year = Gas::select(DB::raw('min(date)'))->firstOrFail();
    $min_year = Carbon::parse($min_year['min'])->year;
    $max_year = Carbon::now()->year;

    if (!$year || $year < $min_year || $year > $max_year) {
      throw new InvalidYearException();
    }

    $start_date = Carbon::createFromDate($year)->startOfYear()->subMonths(3)->startOfMonth();
    $end_date = Carbon::createFromDate($year)->endOfYear();

    $odo_data = Gas::select(DB::raw('date_trunc(\'month\', date) as txn_month'))
      ->addselect(DB::raw('min(odometer) as min_odo'))
      ->addselect(DB::raw('max(odometer) as max_odo'))
      ->where('date', '>=', $start_date)
      ->where('date', '<=', $end_date)
      ->groupBy('txn_month')
      ->orderByRaw('txn_month')
      ->get()
      ->toArray();

    $odo_last_month = 0;
    $odo_per_month = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

    foreach ($odo_data as $value) {
      $parsed_date = Carbon::parse($value['txn_month']);
      $year = $parsed_date->year;
      $month = $parsed_date->month;

      // save last odo of previous year, then skip loop
      if ($end_date->year !== $year) {
        $odo_last_month = $value['max_odo'];
        continue;
      }

      // process values for this year only
      if ($value['max_odo'] === $value['min_odo']) {
        if ($odo_last_month) {
          $odo_this_month = $value['max_odo'] - $odo_last_month;
        } else {
          $odo_this_month = 0;
        }
      } else {
        $odo_this_month = $value['max_odo'] - $value['min_odo'];
      }

      $odo_per_month[$month - 1] = $odo_this_month;

      // prepare value for next loop
      $odo_last_month = $value['max_odo'];
    }

    return $odo_per_month;
  }

  private function calculateGasList(): array {
    $gas_list = [];

    $data = Gas::select('date', 'price_per_liter')
      ->whereNotNull('price_per_liter')
      ->orderBy('date', 'desc')
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
