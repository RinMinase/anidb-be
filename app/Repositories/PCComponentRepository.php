<?php

namespace App\Repositories;

use App\Models\PCComponent;

class PCComponentRepository {

  public function getAll() {
    return PCComponent::orderBy('id')->get()->toArray();
  }

  public function add(array $values) {
    return PCComponent::create([
      'type' => $values['type'],
    ]);
  }

  public function edit(array $values, $id) {
    return PCComponent::where('id', $id)
      ->firstOrFail()
      ->update($values);
  }

  public function delete($id) {
    return PCComponent::where('id', $id)
      ->firstOrFail()
      ->delete();
  }
}
