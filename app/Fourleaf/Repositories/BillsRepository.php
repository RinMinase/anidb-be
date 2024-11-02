<?php

namespace App\Fourleaf\Repositories;

use Carbon\Carbon;

use App\Fourleaf\Models\BillsElectricity;

class BillsRepository {
  public function get(?int $year) {
    $year = $year ?? Carbon::now()->year;

    $data = BillsElectricity::select()
      ->where('uid', '<=', intval($year . '12'))
      ->where('uid', '>=', intval($year . '01'))
      ->orderBy('uid', 'desc')
      ->get();

    foreach ($data as $key => $value) {
      $year_value = intval(substr($value->uid, 0, 4));
      $month_value = intval(substr($value->uid, 5, 2));

      $data[$key]['date'] = Carbon::createFromDate($year_value, $month_value)->format('M Y');

      $data[$key]['cost'] = floatval($data[$key]['cost']);
      $cost_per_kwh = floatval($value['cost']) / $value['kwh'];
      $cost_per_kwh = round($cost_per_kwh, 2);

      $data[$key]['cost_per_kwh'] = $cost_per_kwh;

      unset($data[$key]['uid']);
    }

    return $data;
  }

  public function add(array $values) {
    return BillsElectricity::create($values);
  }

  public function edit(array $values, $id) {
    return BillsElectricity::where('id', $id)
      ->firstOrFail()
      ->update($values);
  }

  public function delete($id) {
    return BillsElectricity::where('id', $id)
      ->firstOrFail()
      ->delete();
  }

  /**
   * Calculation Functions
   */
}
