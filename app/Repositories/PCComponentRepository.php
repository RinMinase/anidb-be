<?php

namespace App\Repositories;

use Carbon\Carbon;

use App\Models\PCComponent;
use App\Models\PCComponentType;

class PCComponentRepository {

  public function getAll(array $values) {
    $id_type = $values['id_type'] ?? null;

    $data = PCComponent::orderBy('id');

    if ($id_type) {
      $is_valid_type_id = PCComponentType::where('id', $id_type)->first();

      if ($is_valid_type_id) {
        $data = $data->where('id_type', $id_type);
      }
    }

    return $data->limit(20)->get();
  }

  public function get($id) {
    return PCComponent::where('id', $id)->firstOrFail();
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

  public function import(array $contents) {
    $import = [];

    $types = PCComponentType::select('id', 'type')->get();

    foreach ($contents as $item) {
      if (!empty($item)) {
        if (!$item->type) continue;
        if (!$item->name) continue;

        $actual_type = $types->first(fn($type) => $type->type == $item->type);
        if (!$actual_type) continue;

        $actual_date = '';
        if ($item->purchase_date) {
          $parts = explode('/', $item->purchase_date);
          $parts[2] = '20' . $parts[2];
          $date = implode('/', $parts);

          $actual_date = Carbon::createFromFormat('m/d/Y', $date)->format('Y-m-d');
        }

        $data = [
          'id_type' => $actual_type->id,
          'name' => $item->name,
          'description' => $item->description ?: null,
          'price' => $item->price ?: null,
          'price_estimate' => $item->price_estimate ?: null,
          'purchase_date' => $actual_date ?: null,
          'purchase_location' => $item->purchase_location ?: null,
          'purchase_notes' => $item->purchase_notes ?: null,
          'is_onhand' => to_boolean($item->is_onhand, true),
          'is_purchased' => to_boolean($item->is_purchased, true),

          'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
          'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ];

        array_push($import, $data);
      }
    }

    PCComponent::refreshAutoIncrements();
    PCComponent::insert($import);
    PCComponent::refreshAutoIncrements();

    return count($import);
  }
}
