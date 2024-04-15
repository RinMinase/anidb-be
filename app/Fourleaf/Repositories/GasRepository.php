<?php

namespace App\Fourleaf\Repositories;

use Carbon\Carbon;

use App\Fourleaf\Models\Gas;
use App\Fourleaf\Models\Maintenance;

class GasRepository {
  public function get($avg_efficiency_type, $efficiency_graph_type) {
    $avg_efficiency_type = $avg_efficiency_type ?? 'all';
    $efficiency_graph_type = $efficiency_graph_type ?? 'last20data';

    $age = $this->calculateAge();
    $mileage = Gas::select('odometer')
      ->orderBy('id', 'desc')
      ->first()
      ->odometer;

    $km_per_month = round($this->calculateKMperMonth($mileage));

    $graph_efficiency_data = [];
    $graph_gas_data = [];


    $maintenance = $this->calculateMaintenanceStatus($mileage);

    /**
     * expected response
     *
     * data: {
     *    stats: {
     *      averageEfficiency: 0.0,
     *      lastEfficiency: 0.0,
     *    },
     *    graph: {
     *      efficiency: {
     *        "2024-01-01": 12.34,
     *        "2024-01-02": 12.34,
     *      },
     *      gas: {
     *        "2024-01-01": 50.12,
     *        "2024-01-02": 60.34,
     *      },
     *    },
     * }
     */

    /**
     * check kms by last odo
     * - also check if existing maintenance log is created for part
     *
     * check years by car years
     * - also check if existing maintenance log is created for part
     */

    /**
     * Average Efficiency Types: $avgEfficiencyType
     * - "all" (default) - all data points are averaged
     * - "last5" - last 5 data points are averaged
     * - "last10" - last 10 data points are averaged
     *
     * Efficiency Graph Types: $efficiencyGraphType
     * - "last20data" (default) - last 20 data points
     * - "last12mos" - last 12 months (per month efficiency, averaged)
     */

    // $data = Gas::select()->get()->toArray();
    // $data_count = count($data);

    // $total_efficiency = 0;
    // $average_efficiency = 0;

    // if ($avg_efficiency_type === 'last10') {
    // } else if ($avg_efficiency_type === 'last5') {
    // } else {
    //   foreach ($data as $item) {
    //     $total_efficiency += $item[''];
    //   }
    // }

    // if ($efficiency_graph_type === 'last12mos') {
    // } else {
    // }



    return [
      'stats' => [
        'average_efficiency' => 0.0,
        'last_efficiency' => 0.0,
        'mileage' => $mileage,
        'age' => $age,
        'km_per_month' => $km_per_month,
      ],
      'graph' => [
        'efficiency' => $graph_efficiency_data,
        'gas' => $graph_gas_data,
      ],
      'maintenance' => $maintenance,
    ];
  }

  public function getFuel() {
    return Gas::all();
  }

  public function addFuel(array $values) {
    return Gas::create($values);
  }

  public function editFuel(array $values, $id) {
    return Gas::where('id', $id)
      ->firstOrFail()
      ->udpate($values);
  }

  public function deleteFuel($id) {
    return Gas::where('id', $id)
      ->firstOrFail()
      ->delete();
  }

  public function getMaintenance() {
    return Maintenance::all();
  }

  public function addMaintenance(array $values) {
    return Maintenance::create($values);
  }

  public function editMaintenance(array $values, $id) {
    return Maintenance::where('id', $id)
      ->firstOrFail()
      ->udpate($values);
  }

  public function deleteMaintenance($id) {
    return Maintenance::where('id', $id)
      ->firstOrFail()
      ->delete();
  }

  private function calculateAge(): string {
    $vehicle_start_age = Carbon::parse(config('app.vehicle_start_date'));
    $date_now = Carbon::now();

    $age_difference = $date_now->diff($vehicle_start_age);
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

    return $date_now->floatDiffInYears($vehicle_start_age);
  }

  private function calculateKMperMonth(int $mileage): float {
    $vehicle_start_age = Carbon::parse(config('app.vehicle_start_date'));
    $date_now = Carbon::now();
    $months = $date_now->floatDiffInMonths($vehicle_start_age);

    return $mileage / $months;
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
        } elseif ($target_distance > 95 && $target_distance < 99.5) {
          $maintenance[$type][$key] = 'nearing';
        }
      }
    }

    return $maintenance;
  }
}
