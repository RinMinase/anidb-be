<?php

namespace App\Repositories;

use App\Models\Electricity;

class ElectricityRepository {

  public function get_all() {
    return Electricity::orderBy('datetime', 'desc')->get()->toArray();
  }

  public function add(array $values) {
    Electricity::create($values);
  }

  public function edit(array $values, $id) {
    Electricity::where('id', $id)->firstOrFail()->update($values);
  }

  public function delete($id) {
    Electricity::where('id', $id)->firstOrFail()->delete();
  }

  public function get_per_week($year) {
    if (empty($year)) $year = now()->year;
  }

  public function get_per_month($year) {
    if (empty($year)) $year = now()->year;
  }

  public function get_per_year() {
  }
}
