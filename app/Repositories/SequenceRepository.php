<?php

namespace App\Repositories;

use App\Models\Sequence;

class SequenceRepository {

  public function getAll() {
    return Sequence::all();
  }

  public function add(array $values) {
    return Sequence::create($values);
  }

  public function edit(array $values, $id) {
    return Sequence::where('id', $id)->update($values);
  }

  public function delete($id) {
    return Sequence::where('id', $id)
      ->firstOrFail()
      ->delete();
  }
}
