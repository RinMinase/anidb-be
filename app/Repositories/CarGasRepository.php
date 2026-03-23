<?php

namespace App\Repositories;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use App\Exceptions\Car\InvalidYearException;

use App\Models\CarGas;
use App\Models\CarMaintenance;
use App\Models\CarMaintenancePart;
use App\Models\CarMaintenanceType;

class CarGasRepository {
  /**
   * Overview Function
   */

  public function get($avg_efficiency_type = null, $efficiency_graph_type = null) {
    $avg_efficiency_type = $avg_efficiency_type ?? 'all';
    $efficiency_graph_type = $efficiency_graph_type ?? 'last20data';

    $age = $this->calculateAge();

    $age_split = explode(',', $age, 2);
    $age_split_year = trim($age_split[0]);
    $age_split_months = trim($age_split[1] ?? '');

    $mileage = CarGas::select('odometer')
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
        'age_split_year' => $age_split_year,
        'age_split_months' => $age_split_months,
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

  public function getGuide() {
    $types = CarMaintenanceType::all()->toArray();

    $kms = [];
    $years = [];

    foreach ($types as $type) {
      foreach ($type as $key => $value) {
        if ($key === 'km' && $value !== null) {
          array_push($kms, [
            'type' => $type['type'],
            'typeCamel' => Str::camel($type['type']),
            'label' => $type['label'],
            'km' => $type['km'],
          ]);
        }

        if ($key === 'year' && $value !== null) {
          array_push($years, [
            'type' => $type['type'],
            'typeCamel' => Str::camel($type['type']),
            'label' => $type['label'],
            'year' => $type['year'],
          ]);
        }
      }
    }

    usort($kms, function ($a, $b) {
      $cmp = $a['km'] <=> $b['km'];
      if ($cmp === 0) return $a['type'] <=> $b['type'];
      return $cmp;
    });

    usort($years, function ($a, $b) {
      $cmp = $a['year'] <=> $b['year'];
      if ($cmp === 0) return $a['type'] <=> $b['type'];
      return $cmp;
    });

    return [
      'km' => $kms,
      'year' => $years,
    ];
  }

  /**
   * Graph Functions
   */

  public function getOdo($year) {
    return $this->calculateOdometerPerMonth(intval($year));
  }

  public function getEfficiency($type) {
    $data = $this->calculateEfficiencyList($type);

    if ($type === 'last12mos') {
      // additional post processing group by month
      $data = $this->postProcessEfficiencyListByMonth($data);
    }

    return $data;
  }

  public function getPrices() {
    return $this->calculateGasList();
  }

  /**
   * Gas Functions
   */

  public function getFuelList($params) {
    // Ordering Parameters
    $column = $params['column'] ?? 'odometer';
    $order = $params['order'] ?? 'asc';

    // Pagination Parameters
    $limit = isset($params['limit']) ? intval($params['limit']) : 100;
    $page = isset($params['page']) ? intval($params['page']) : 1;
    $skip = ($page > 1) ? ($page * $limit - $limit) : 0;

    $gas = CarGas::orderBy($column, $order)
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

  public function getFuel($id) {
    return CarGas::where('id', $id)->firstOrFail()->toArray();
  }

  public function addFuel(array $values) {
    return CarGas::create($values);
  }

  public function editFuel(array $values, $id) {
    return CarGas::where('id', $id)
      ->firstOrFail()
      ->update($values);
  }

  public function deleteFuel($id) {
    return CarGas::where('id', $id)
      ->firstOrFail()
      ->delete();
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

    CarGas::truncate();
    CarGas::refreshAutoIncrements();
    CarGas::insert($import_gas);
    CarGas::refreshAutoIncrements();

    $count_maintenance = 0;
    $count_maintenance_parts = 0;

    $maintenance_types = CarMaintenanceType::all();

    CarMaintenance::truncate();
    CarMaintenancePart::truncate();

    CarMaintenance::refreshAutoIncrements();

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

        $partial_data_id = CarMaintenance::insertGetId($data);
        $count_maintenance++;

        foreach ($data_types as $proper_part_id) {
          CarMaintenancePart::create([
            'id_car_maintenance' => $partial_data_id,
            'id_car_maintenance_type' => $proper_part_id,
          ]);

          $count_maintenance_parts++;
        }
      }
    }

    CarMaintenance::refreshAutoIncrements();

    return [
      'gas' => count($import_gas),
      'maintenance' => $count_maintenance,
      'parts' => $count_maintenance_parts,
    ];
  }

  public function export() {
    $gas_data = CarGas::select('date', 'from_bars', 'to_bars', 'odometer', 'price_per_liter', 'liters_filled')
      ->get()
      ->toArray();

    $maintenance_data = CarMaintenance::select('date', 'description', 'odometer')
      ->addSelect(DB::raw('\'[\' || string_agg(car_maintenance_types.type, \', \') || \']\' AS raw_parts'))
      ->leftJoin(
        'car_maintenance_parts',
        'car_maintenance_parts.id_car_maintenance',
        '=',
        'car_maintenance.id'
      )
      ->leftJoin(
        'car_maintenance_types',
        'car_maintenance_types.id',
        '=',
        'car_maintenance_parts.id_car_maintenance_type'
      )
      ->groupBy(
        'car_maintenance.id',
        'car_maintenance.date',
        'car_maintenance.description',
        'car_maintenance.odometer'
      )
      ->orderBy('car_maintenance.id')
      ->get()
      ->toArray();

    foreach ($maintenance_data as &$row) {
      $types = trim($row["raw_parts"], "[]"); // remove brackets
      $row["parts"] = array_map('trim', explode(',', $types)); // split into array
      unset($row["raw_parts"]); // remove original key
    }

    $for_export = [
      'gas' => $gas_data,
      'maintenance' => $maintenance_data,
    ];

    return [
      'data' => json_encode($for_export, JSON_PRETTY_PRINT),
      'filename' => 'gas_' . now()->timestamp . '.json',
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

    $maintenance_types = CarMaintenanceType::all()->toArray();
    $maintenance = ['km' => [], 'year' => []];
    $limits = ['km' => [], 'year' => []];

    foreach ($maintenance_types as $type) {
      foreach ($type as $key => $value) {
        if ($key === 'km' && $value !== null) {
          $maintenance['km'][$type['type']] = 'normal';
          $limits['km'][$type['type']] = $type['km'];
        }

        if ($key === 'year' && $value !== null) {
          $maintenance['year'][$type['type']] = 'normal';
          $limits['year'][$type['type']] = $type['year'];
        }
      }
    }

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

    $data = CarGas::select('date', 'from_bars', 'to_bars', 'odometer', 'liters_filled');

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
    $min_year = CarGas::select(DB::raw('min(date)'))->firstOrFail();
    $min_year = Carbon::parse($min_year['min'])->year;
    $max_year = Carbon::now()->year;

    if (!$year || $year < $min_year || $year > $max_year) {
      throw new InvalidYearException();
    }

    $start_date = Carbon::createFromDate($year)->startOfYear()->subMonths(3)->startOfMonth();
    $end_date = Carbon::createFromDate($year)->endOfYear();

    $odo_data = CarGas::select(DB::raw('date_trunc(\'month\', date) as txn_month'))
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

    $data = CarGas::select('date', 'price_per_liter')
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
}
