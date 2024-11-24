<?php

namespace App\Repositories;

use App\Models\PCComponent;

class PCComponentRepository {

  public function getAll() {
    return PCComponent::orderBy('id')->get()->toArray();
  }

  public function add(array $values) {
    return PCComponent::create([
      'id_type' => $values['id_type'],
      'name' => $values['name'],
      'description' => $values['description'],
      'price' => $values['price'],
      'purchase_date' => $values['purchase_date'],
      'purchase_location' => $values['purchase_location'],
      'purchase_notes' => $values['purchase_notes'],
      'is_onhand' => $values['is_onhand'],
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
