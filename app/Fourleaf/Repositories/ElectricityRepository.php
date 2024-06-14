<?php

namespace App\Fourleaf\Repositories;

use App\Fourleaf\Models\Electricity;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ElectricityRepository {
  public function get(int $year, int $month) {
    $date_from = Carbon::createMidnightDate($year, $month, 1);

    if ($month === 12) {
      $year++;
      $month = 1;
    } else {
      $month++;
    }

    $date_to = Carbon::createMidnightDate($year, $month, 1);

    $data = Electricity::where('datetime', '>=', $date_from)
      ->where('datetime', '<', $date_to)
      ->orderBy('datetime')
      ->get()
      ->toArray();

    $daily_values = $this->calculateDailyValues($data);
    $weekly_values = $this->calculateWeeklyValues($data);

    return [
      'daily' => $daily_values,
      'weekly' => $weekly_values,
    ];
  }

  public function add(array $values) {
    return Electricity::create($values);
  }

  public function edit(array $values, $id) {
    return Electricity::where('id', $id)
      ->firstOrFail()
      ->update($values);
  }

  public function delete($id) {
    return Electricity::where('id', $id)
      ->firstOrFail()
      ->delete();
  }

  /**
   * Calculation Functions
   */
  private function calculateDailyValues($data): array {
    $kw_per_hour_values = [];
    $kw_per_day_values = [];
    $alarms_per_day_values = [];

    foreach ($data as $index => $value) {
      if ($index === 0) continue;

      $past_datetime = Carbon::parse($data[$index - 1]['datetime']);
      $curr_datetime = Carbon::parse($value['datetime']);
      $past_reading = $data[$index - 1]['reading'];
      $curr_reading = $value['reading'];

      $kw_per_hour = ($curr_reading - $past_reading) / $past_datetime->diffInHours($curr_datetime);

      array_push($kw_per_hour_values, $kw_per_hour);
      array_push($kw_per_day_values, $kw_per_hour * 24);
    }

    $avg_kw_per_day = 0;
    if (count($kw_per_day_values)) {
      $avg_kw_per_day = array_sum($kw_per_day_values) / count($kw_per_day_values);
    }

    foreach ($kw_per_day_values as $value) {
      $state = 'normal';
      $percentage_to_avg = $value / $avg_kw_per_day;

      if ($percentage_to_avg <= 0.75) $state = 'low';
      else if ($percentage_to_avg >= 1.25) $state = 'high';

      array_push($alarms_per_day_values, $state);
    }

    $daily_values = [];

    array_unshift($kw_per_hour_values, 0);
    array_unshift($kw_per_day_values, 0);

    foreach ($data as $index => $value) {
      $datetime = Carbon::parse($value['datetime']);
      $date_number = $datetime->dayOfMonth;
      $day = $datetime->englishDayOfWeek;
      $date = $datetime->format('Y-m-d');
      $reading_time = $datetime->format('H:i');

      array_push($daily_values, [
        'id' => Str::uuid()->toString(),
        'date_number' => $date_number,
        'day' => $day,
        'date' => $date,
        'kw_per_hour' => round($kw_per_hour_values[$index] ?? 0, 2),
        'kw_per_day' => round($kw_per_day_values[$index] ?? 0, 2),
        'reading_value' => $value['reading'],
        'reading_time' => $reading_time
      ]);
    }

    return $daily_values;
  }

  private function calculateWeeklyValues($data): array {
    $data_by_wk = [];

    // Temporary Higher-scoped Variables
    $data_by_days_wk = [];
    $day_of_wk = null;
    $last_datetime = null;

    foreach ($data as $value) {
      $datetime = Carbon::parse($value['datetime'])->locale('en_US');
      // $curr_day_of_wk = $datetime->dayOfWeek();
      $week_no = $datetime->week();
      $last_datetime = $value['datetime'];

      if ($day_of_wk !== $week_no) {
        $prev_day_of_wk = $day_of_wk;
        $day_of_wk = $week_no;

        if ($prev_day_of_wk !== null) {
          // push to main array
          array_push($data_by_wk, [
            'week_no' => $this->calculateWeeks($data_by_days_wk[0]['datetime']),
            'actual_week_year_no' => $prev_day_of_wk,
            'data' => $data_by_days_wk,
          ]);

          $data_by_days_wk = array();
        }
      }

      array_push($data_by_days_wk, $value);
    }

    // push remaining to main array
    array_push($data_by_wk, [
      'week_no' => $this->calculateWeeks($last_datetime),
      'actual_week_year_no' => $day_of_wk,
      'data' => $data_by_days_wk,
    ]);

    // process subdata per week
    $last_kw_reading = 0;
    foreach ($data_by_wk as $index => $value) {
      $subdata = $value['data'];

      $first_reading = $subdata[0]['reading'];
      $last_reading = $subdata[count($subdata) - 1]['reading'];
      $total_wk_reading = $last_reading - $first_reading;

      if (count($subdata) === 1) {
        $total_wk_reading = $subdata[0]['reading'] - $last_kw_reading;
      }

      $days_in_wk = $this->calculateDaysInCurrentWeek($subdata[0]['datetime']);

      $avg_kw_per_day_values = [];
      foreach ($subdata as $subindex => $subvalues) {
        // contains id, datetime and reading
        if ($subindex === 0) continue;

        $past_datetime = Carbon::parse($subdata[$subindex - 1]['datetime']);
        $curr_datetime = Carbon::parse($subvalues['datetime']);
        $past_reading = $subdata[$subindex - 1]['reading'];
        $curr_reading = $subvalues['reading'];

        $kw_per_hour = ($curr_reading - $past_reading) / $past_datetime->diffInDays($curr_datetime);

        array_push($avg_kw_per_day_values, $kw_per_hour);
      }

      $avg_kw_per_day = -1;
      $total_est_kwh = 0;

      if (count($subdata) > 1) {
        $avg_kw_per_day = round(array_sum($avg_kw_per_day_values) / count($avg_kw_per_day_values), 2);
        $total_est_kwh = $avg_kw_per_day * $days_in_wk;
      }

      $last_kw_reading = $last_reading;

      $data_by_wk[$index]['id'] = Str::uuid()->toString();
      $data_by_wk[$index]['days_with_record'] = count($subdata);
      $data_by_wk[$index]['days_with_no_record'] = $days_in_wk - count($subdata);
      $data_by_wk[$index]['days_in_week'] = $days_in_wk;
      $data_by_wk[$index]['total_recorded_kwh'] = $total_wk_reading;
      $data_by_wk[$index]['total_estimated_kwh'] = $total_est_kwh;
      $data_by_wk[$index]['est_total_kwh'] = $total_wk_reading + $total_est_kwh;
      $data_by_wk[$index]['avg_daily_kwh'] = $avg_kw_per_day;

      unset($data_by_wk[$index]['data']);
    }

    return $data_by_wk;
  }

  private function calculateWeeks(string $date): int {
    $date = Carbon::parse($date);
    $start_of_month = Carbon::parse($date)->locale('en_US')->startOfMonth()->startOfWeek()->subMicrosecond(1);
    $weeks = ceil($start_of_month->diffInWeeks($date));

    return $weeks;
  }

  private function calculateDaysInCurrentWeek(string $date): int {
    $start_of_wk = Carbon::parse($date)->locale('en_US')->startOfWeek();
    $start_of_month = Carbon::parse($date)->locale('en_US')->startOfMonth();

    $last_of_wk = Carbon::parse($date)->locale('en_US')->endOfWeek();
    $last_of_month = Carbon::parse($date)->locale('en_US')->lastOfMonth();

    $start_count_date = $start_of_month > $start_of_wk ? $start_of_month : $start_of_wk;
    $last_count_date = $last_of_month > $last_of_wk ? $last_of_wk : $last_of_month;

    $days = ceil($start_count_date->diffInDays($last_count_date));

    return $days;
  }
}
