<?php

namespace App\Repositories;

use Carbon\Carbon;

use App\Models\ElectricityAppliance;

class ElectricityApplianceRepository {

  public function get_all() {
    return ElectricityAppliance::orderBy('date', 'desc')->get();
  }

  public function add(array $values) {
    ElectricityAppliance::create($values);
  }

  public function edit(array $values, $id) {
    ElectricityAppliance::where('id', $id)->firstOrFail()->update($values);
  }

  public function delete($id) {
    ElectricityAppliance::where('id', $id)->firstOrFail()->delete();
  }

  public function get_per_month($year) {
    if (empty($year)) $year = now()->year;

    $data = ElectricityAppliance::whereYear('date', $year)->orderBy('date')->get();

    $grouped = array_fill(0, 12, []);

    foreach ($data as $item) {
      $monthIndex = Carbon::parse($item->date)->month - 1;
      $grouped[$monthIndex][] = $item->name;
    }

    $output = [];
    foreach ($grouped as $array_id => $names) {
      $output[] = [
        'id_month' => $array_id + 1,
        'month' => Carbon::create()->month($array_id + 1)->format('F'),
        'month_short' => Carbon::create()->month($array_id + 1)->format('M'),
        'names' => $names,
      ];
    }

    return $output;
  }
}
