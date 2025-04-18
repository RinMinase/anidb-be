<?php

namespace App\Repositories;

use App\Models\PCComponentType;

class PCComponentTypeRepository {

  public function getAll() {
    return PCComponentType::orderBy('id')->get()->toArray();
  }

  public function add(array $values) {
    return PCComponentType::create([
      'type' => $values['type'],
      'name' => $values['name'],
      'is_peripheral' => $values['is_peripheral'] ?? false,
    ]);
  }

  public function edit(array $values, $id) {
    return PCComponentType::where('id', $id)
      ->firstOrFail()
      ->update($values);
  }

  public function delete($id) {
    return PCComponentType::where('id', $id)
      ->firstOrFail()
      ->delete();
  }
}
