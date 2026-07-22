<?php

namespace App\Repositories;

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

  public function getYearStats(string $year) {
  }
}
