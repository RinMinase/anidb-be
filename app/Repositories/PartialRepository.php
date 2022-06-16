<?php

namespace App\Repositories;

use App\Models\Partial;

class PartialRepository {

  public function add(array $values) {
    return Partial::create($values);
  }

  public function edit(array $values, $id) {
    return Partial::whereId($id)->update($values);
  }

  public function delete($id) {
    return Partial::findOrFail($id)->delete();
  }
}
