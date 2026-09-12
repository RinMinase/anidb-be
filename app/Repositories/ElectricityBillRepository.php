<?php

namespace App\Repositories;

use Carbon\Carbon;

use App\Models\ElectricityBill;

class ElectricityBillRepository {

  public function getAll() {
    return ElectricityBill::all();
  }

  public function get(string $id) {
    return ElectricityBill::where('id', $id)->firstOrFail();
  }

  public function add(array $values) {
    return ElectricityBill::create($values);
  }

  public function edit(array $values, string $id) {
    return ElectricityBill::where('id', $id)
      ->firstOrFail()
      ->update($values);
  }

  public function delete(string $id) {
    return ElectricityBill::where('id', $id)
      ->firstOrFail()
      ->delete();
  }

  public function getStats() {
    $limit = 30;

    $bills = ElectricityBill::query()
      ->orderBy('bill_date', 'desc')
      ->take($limit)
      ->get()
      ->reverse();

    $data = [
      'avg_kwh' => [],
      'price_breakdown' => [],
      'rate_breakdown' => [],
      'usage' => [],
    ];

    foreach ($bills as $bill) {
      if ($bill->avg_kwh) {
        $data['avg_kwh'][] = [
          'date' => $bill->bill_date->format('Y-m'),
          'date_month' => $bill->bill_date->month,
          'date_year' => $bill->bill_date->year,
          'avg_kwh' => $bill->avg_kwh,
          'calculated_kwh_cost' => $bill->calculated_kwh_cost ?? 0,
        ];
      }

      $has_any_price_data = $bill->price_generation || $bill->price_distribution || $bill->price_govt;
      if ($has_any_price_data) {
        $total_cost = $bill->total_cost;
        $generation = $bill->price_generation ?? 0;
        $distribution = $bill->price_distribution ?? 0;
        $govt = $bill->price_govt ?? 0;
        $others = round($total_cost - ($generation + $distribution + $govt), 2);

        $data['price_breakdown'][] = [
          'date' => $bill->bill_date->format('Y-m'),
          'date_month' => $bill->bill_date->month,
          'date_year' => $bill->bill_date->year,
          'total_cost' => $bill->total_cost,

          'price_generation' => $generation,
          'price_distribution' => $distribution,
          'price_govt' => $govt,
          'price_others' => $others,

          'percent_generation' => round(($generation / $total_cost) * 100, 2),
          'percent_distribution' => round(($distribution / $total_cost) * 100, 2),
          'percent_govt' => round(($govt / $total_cost) * 100, 2),
          'percent_others' => round(($others / $total_cost) * 100, 2),
        ];
      }

      $has_any_rate_data = $bill->kwh_price_generation_charge || $bill->kwh_price_distribution_charge || $bill->kwh_price_transmission_charge;
      if ($has_any_rate_data) {
        $data['rate_breakdown'][] = [
          'date' => $bill->bill_date->format('Y-m'),
          'date_month' => $bill->bill_date->month,
          'date_year' => $bill->bill_date->year,
          'kwh_price_generation' => $bill->kwh_price_generation_charge ?? 0,
          'kwh_price_distribution' => $bill->kwh_price_distribution_charge ?? 0,
          'kwh_price_transmission' => $bill->kwh_price_transmission_charge ?? 0,
        ];
      }

      $has_any_usage_data = $bill->total_cost || $bill->reading_total;
      if ($has_any_usage_data) {
        $data['usage'][] = [
          'date' => $bill->bill_date->format('Y-m'),
          'date_month' => $bill->bill_date->month,
          'date_year' => $bill->bill_date->year,
          'total_cost' => $bill->total_cost ?? 0,
          'reading_total' => $bill->reading_total ?? 0,
          'calculated_kwh_cost' => $bill->calculated_kwh_cost ?? 0,
        ];
      }
    }

    return $data;
  }

  public function import(array $values) {
    if (!is_array($values)) {
      return;
    }

    $import = [];

    foreach ($values as $item) {
      if (!empty($item)) {
        $curr_date = now();
        $reading_total = $item?->reading_total ?? null;

        if (!$reading_total && $item?->reading_from && $item?->reading_to) {
          $reading_total = (int) $item->reading_to - (int) $item->reading_from;
        }

        $data = [
          'bill_date' => Carbon::createFromFormat('Y-m', $item->bill_date)->startOfMonth(),

          'period_from' => Carbon::createFromFormat('Y-m-d', $item->period_from),
          'period_to' => Carbon::createFromFormat('Y-m-d', $item->period_to),

          'calculated_kwh_cost' => round($item->total_cost / $reading_total, 4),
          'avg_kwh' => $item->avg_kwh,
          'reading_from' => (int) $item->reading_from,
          'reading_to' => (int) $item->reading_to,
          'reading_total' => $reading_total,

          'total_cost' => $item->total_cost,
          'price_generation' => $item->price_generation,
          'price_distribution' => $item->price_distribution,
          'price_govt' => $item->price_govt,

          'kwh_price_generation_charge' => $item->kwh_price_generation_charge,
          'kwh_price_transmission_charge' => $item->kwh_price_transmission_charge,
          'kwh_price_distribution_charge' => $item->kwh_price_distribution_charge,
          'notes' => $item->notes ?? null,

          'created_at' => $curr_date,
          'updated_at' => $curr_date,
        ];

        array_push($import, $data);
      }
    }

    ElectricityBill::truncate();
    ElectricityBill::insert($import);
    ElectricityBill::refreshAutoIncrements();

    return count($import);
  }

  public static function calculateKwhCost() {
    $kwh_value = 0;

    $data_points = 3;
    $last_kwh_costs = ElectricityBill::query()
      ->select('calculated_kwh_cost')
      ->orderBy('bill_date', 'desc')
      ->take($data_points)
      ->get()
      ->pluck('calculated_kwh_cost')
      ->toArray();


    if (!count($last_kwh_costs)) return $kwh_value;

    $weights = match (count($last_kwh_costs)) {
      3 => [0.50, 0.30, 0.20],
      2 => [0.65, 0.35],
      1 => [1.00],
      default => []
    };

    foreach ($weights as $index => $weight) {
      $kwh_value += $last_kwh_costs[$index] * $weight;
    }

    $kwh_value = round($kwh_value, 4);

    return $kwh_value;
  }
}
