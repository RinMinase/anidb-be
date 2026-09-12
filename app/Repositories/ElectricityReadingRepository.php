<?php

namespace App\Repositories;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Resources\Json\JsonResource;

use App\Models\AppSetting;
use App\Models\ElectricityReading;

class ElectricityReadingRepository {

  public function getAll(array $params) {
    // Ordering Parameters
    $order = $params['order'] ?? 'asc';

    // Pagination Parameters
    $limit = isset($params['limit']) ? intval($params['limit']) : 100;
    $page = isset($params['page']) ? intval($params['page']) : 1;
    $skip = ($page > 1) ? ($page * $limit - $limit) : 0;

    $data = ElectricityReading::orderBy('date', $order)
      ->orderBy('id', 'asc');

    $total = $data->count();
    $total_pages = ceil($total / $limit);
    $has_next = $page < $total_pages;

    $data = $data->skip($skip)->paginate($limit);

    return [
      'data' => JsonResource::collection($data),
      'meta' => [
        'page' => $page,
        'limit' => intval($limit),
        'results' => count($data),
        'total_results' => $total,
        'total_pages' => $total_pages,
        'has_next' => $has_next,
      ],
    ];
  }

  public function get(string $id) {
    return ElectricityReading::where('id', $id)->firstOrFail();
  }

  public function add(array $values) {
    return ElectricityReading::create($values);
  }

  public function edit(array $values, string $id) {
    return ElectricityReading::where('id', $id)
      ->firstOrFail()
      ->update($values);
  }

  public function delete(string $id) {
    return ElectricityReading::where('id', $id)
      ->firstOrFail()
      ->delete();
  }

  public function getMonthStats(string $year, string $month) {
    $date = Carbon::createFromDate($year, $month, 1)->startOfDay();

    $daily = $this->calculateDailyStats($date);
    $monthly = $this->calculateMonthStats($date);

    return [
      'daily' => $daily,
      'monthly' => $monthly,
    ];
  }

  public function getYearStats(string $year) {
    $start_of_year = "{$year}-01-01 00:00:00";
    $end_of_year = "{$year}-12-31 23:59:59";

    $monthly_data = ElectricityReading::query()
      ->selectRaw('EXTRACT(MONTH FROM date)::INTEGER as month')
      ->selectRaw('(ARRAY_AGG(reading ORDER BY date ASC))[1] as min_reading')
      ->selectRaw('(ARRAY_AGG(reading ORDER BY date DESC))[1] as max_reading')
      ->selectRaw('
        (ARRAY_AGG(reading ORDER BY date DESC))[1] - (ARRAY_AGG(reading ORDER BY date ASC))[1]
        as total_kwh
      ')
      ->whereBetween('date', [$start_of_year, $end_of_year])
      ->groupBy(DB::raw("EXTRACT(MONTH FROM date)"))
      ->orderBy('month', 'asc')
      ->get();

    return $monthly_data;
  }

  public function import(array $values) {
    if (!is_array($values)) {
      return;
    }

    $import = [];

    foreach ($values as $item) {
      if (!empty($item)) {
        $data = [
          'date' => Carbon::createFromTimestampMs($item->date),
          'reading' => $item->reading,
        ];

        array_push($import, $data);
      }
    }

    ElectricityReading::truncate();
    ElectricityReading::insert($import);
    ElectricityReading::refreshAutoIncrements();

    return count($import);
  }

  /**
   * CALCULATION FUNCTIONS
   */
  private function calculateDailyStats(Carbon $date) {
    $start_of_month = $date->copy()->startOfMonth()->toDateTimeString();
    $end_of_month = $date->copy()->endOfMonth()->toDateTimeString();

    $results = ElectricityReading::query()
      ->withExpression(
        'CustomTable',
        DB::table('electricity_readings')
          ->selectRaw('DATE(date) as day')
          ->selectRaw('MAX(reading) as day_max_reading')

          // Handle difference in reading compared to previous row
          ->selectRaw('
            MAX(reading) - LAG(MAX(reading), 1)
            OVER (ORDER BY MAX(reading)) as kwh_diff
          ')

          // Handle difference in hours compared to previous row
          ->selectRaw('
            EXTRACT(EPOCH FROM (
                MAX(date) - LAG(MAX(date), 1) OVER (ORDER BY DATE(date))
            ))::double precision / 3600.0 AS hour_diff
          ')

          // Handle flag for estimated avg
          ->selectRaw('
            CASE
              WHEN ROW_NUMBER() OVER (ORDER BY DATE(date)) = 1 THEN true
              ELSE false
            END as estimated_avg
          ')
          ->whereBetween('date', [$start_of_month, $end_of_month])
          ->groupByRaw('DATE(date)')
      )
      ->from('CustomTable')
      ->selectRaw('day')
      ->selectRaw('day_max_reading')
      ->selectRaw('kwh_diff')
      ->selectRaw('hour_diff')
      ->selectRaw('estimated_avg')
      ->selectRaw('ROUND((kwh_diff / NULLIF(hour_diff, 0))::numeric, 4) AS avg_per_hr')
      ->selectRaw('
        ROUND(((kwh_diff / NULLIF(hour_diff, 0)) * 24)::numeric, 2)
        AS extrapolated_per_day
      ')
      ->orderBy('day')
      ->get();

    if (!$results->count()) return [];

    $keyed_results = $results->keyBy(function ($item) {
      return Carbon::parse($item->day)->format('Y-m-d');
    });

    $first_date = Carbon::parse($results->first()->day);
    $last_record_date = Carbon::parse($results->last()->day)->startOfDay();
    $month_end = $first_date->copy()->endOfMonth()->startOfDay();
    $today_date = Carbon::now()->startOfDay();

    // IF the requested month is completely in the past relative to today:
    //    Fill up to the end of that month (1..31)
    // ELSE (Current month or future month):
    //    Cap at the last existing DB record date (fills leading & middle only)
    $end_date = ($month_end->lt($today_date)) ? $month_end : $last_record_date;

    $period = CarbonPeriod::create($first_date->copy()->startOfMonth(), $end_date);

    $last_valid_avg = null;
    $last_valid_ext = null;

    $filled_results = collect($period)
      ->map(function (CarbonInterface $date) use ($keyed_results, &$last_valid_avg, &$last_valid_ext) {
        $formattedDate = $date->format('Y-m-d');
        $formattedDateShort = $date->format('m-d');

        // record exists
        if ($keyed_results->has($formattedDate)) {
          $row = $keyed_results->get($formattedDate);

          $last_valid_avg = (float) $row->avg_per_hr;
          $last_valid_ext = (float) $row->extrapolated_per_day;

          $row->day_short = $formattedDateShort;
          $row->avg_per_hr = (float) $row->avg_per_hr;
          $row->extrapolated_per_day = (float) $row->extrapolated_per_day;

          return $row;
        }

        // missing record
        return (object) [
          'day' => $formattedDate,
          'day_short' => $formattedDateShort,
          'day_max_reading' => null,
          'kwh_diff' => null,
          'hour_diff' => null,
          'estimated_avg' => true,
          'avg_per_hr' => (float) $last_valid_avg,
          'extrapolated_per_day' => (float) $last_valid_ext,
        ];
      });

    // fill leading days, if missing day 1
    $next_valid_avg = $filled_results->firstWhere('avg_per_hr', '!=', null)?->avg_per_hr;
    $next_valid_ext = $filled_results->firstWhere('extrapolated_per_day', '!=', null)?->extrapolated_per_day;

    $final_results = $filled_results->map(function ($row) use ($next_valid_avg, $next_valid_ext) {
      if (!$row->avg_per_hr) $row->avg_per_hr = $next_valid_avg;
      if (!$row->extrapolated_per_day) $row->extrapolated_per_day = $next_valid_ext;

      return $row;
    });

    return $final_results;
  }

  private function calculateMonthStats(Carbon $date) {
    $start_of_month = $date->copy()->startOfMonth();
    $end_of_month = $date->copy()->endOfMonth();

    // Get first and last data for the month
    $first_reading = ElectricityReading::query()
      ->select('date', 'reading')
      ->whereBetween('date', [$start_of_month, $end_of_month])
      ->oldest('date')
      ->limit(1);

    $min_max_readings = ElectricityReading::query()
      ->select('date', 'reading')
      ->whereBetween('date', [$start_of_month, $end_of_month])
      ->latest('date')
      ->limit(1)
      ->unionAll($first_reading)
      ->get();

    $readings = $min_max_readings->sortBy('date')->values();

    if (!$readings->count()) {
      return [
        'total_kwh' => null,
        'total_days_of_month' => null,
        'partial_days' => null,
        'avg_kwh_per_day' => null,
        'kwh_cost' => null,
        'monthly_est_cost' => null,
      ];
    }

    // Calculate partial amount of days
    $first_date = Carbon::parse($readings->first()->date)->startOfDay();
    $last_date = Carbon::parse($readings->last()->date)->startOfDay();
    $partial_days = $first_date->diffInDays($last_date) + 1;

    // Calculate Avg kWh per day
    $first_reading = $readings->first()->reading;
    $last_reading = $readings->last()->reading;
    $total_kwh_reading = $last_reading - $first_reading;
    $avg_kwh_per_day = $total_kwh_reading / $partial_days;

    // Fetch Cost per kWh
    $kwh_cost = ElectricityBillRepository::calculateKwhCost();
    $total_days_of_month = $date->daysInMonth;
    $monthly_est_cost = $kwh_cost * $avg_kwh_per_day * $total_days_of_month;

    return [
      'total_kwh' => $total_kwh_reading,
      'total_days_of_month' => $total_days_of_month,
      'partial_days' => $partial_days,
      'avg_kwh_per_day' => round($avg_kwh_per_day, 4),
      'kwh_cost' => $kwh_cost,
      'monthly_est_cost' => round($monthly_est_cost, 2),
    ];
  }
}
