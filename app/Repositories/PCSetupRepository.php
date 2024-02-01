<?php

namespace App\Repositories;

use App\Models\PCSetup;

class PCSetupRepository {

  public function getAll() {
    return PCSetup::all();
  }

  public function get(int $id) {
    return PCSetup::where('id', $id)->firstOrFail();
  }

  public function add(array $values) {
    PCSetup::create($values);
  }

  public function edit(array $values, int $id) {
    PCSetup::where('id', $id)->update($values);
  }

  public function delete(int $id) {
    PCSetup::where('id', $id)
      ->firstOrFail()
      ->delete();
  }
}
