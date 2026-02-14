<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

use App\Models\CarMaintenance;
use App\Models\CarMaintenancePart;
use App\Models\CarMaintenanceType;

class CarMaintenanceRepository {
  /**
   * Maintenance Functions
   */

  public function getMaintenanceList() {
    return CarMaintenance::with('parts')->get();
  }

  public function getMaintenance($id) {
    return CarMaintenance::where('id', $id)->with('parts')->firstOrFail();
  }

  public function addMaintenance(array $values) {
    $parts = $values['parts'] ?? [];
    unset($values['parts']);

    $maintenance = CarMaintenance::create($values);
    $part_ids = CarMaintenanceType::whereIn('type', $parts)->pluck('id');

    $partsToSave = $part_ids->map(function ($id) {
      return ['id_car_maintenance_type' => $id];
    })->toArray();

    $maintenance->parts()->createMany($partsToSave);
  }

  public function editMaintenance(array $values, $id) {
    $item = CarMaintenance::where('id', $id)->firstOrFail();

    $parts = $values['parts'] ?? [];
    unset($values['parts']);

    $item->update($values);
    $item->parts()->delete();

    $part_ids = CarMaintenanceType::whereIn('type', $parts)->pluck('id');

    $partsToSave = $part_ids->map(function ($id) {
      return ['id_car_maintenance_type' => $id];
    })->toArray();

    $item->parts()->createMany($partsToSave);
  }

  public function deleteMaintenance($id) {
    return CarMaintenance::where('id', $id)
      ->firstOrFail()
      ->delete();
  }

  public function getMaintenanceParts() {
    return CarMaintenanceType::select('type', 'label')->get()->toArray();
  }

  /**
   * Calculation Functions
   */

  private function fetchLastMaintenanceDates(): array {
    $last_maintenance = CarMaintenancePart::select('part')
      ->addselect(DB::raw('max(odometer) as odometer'))
      ->addselect(DB::raw('max(date) as date'))
      ->leftJoin('car_maintenance', function ($join) {
        $join->on(
          'car_maintenance_parts.id_car_maintenance',
          '=',
          'car_maintenance.id',
        );
      })
      ->groupBy('part')
      ->orderBy('part', 'asc')
      ->get()
      ->keyBy('part');

    return [
      'ac_coolant' => [
        'date' => $last_maintenance->get('ac_coolant') ? $last_maintenance->get('ac_coolant')->date : null,
        'odometer' => $last_maintenance->get('ac_coolant') ? $last_maintenance->get('ac_coolant')->odometer : null,
      ],
      'battery' => [
        'date' => $last_maintenance->get('battery') ? $last_maintenance->get('battery')->date : null,
        'odometer' => $last_maintenance->get('battery') ? $last_maintenance->get('battery')->odometer : null,
      ],
      'brake_fluid' => [
        'date' => $last_maintenance->get('brake_fluid') ? $last_maintenance->get('brake_fluid')->date : null,
        'odometer' => $last_maintenance->get('brake_fluid') ? $last_maintenance->get('brake_fluid')->odometer : null,
      ],
      'engine_oil' => [
        'date' => $last_maintenance->get('engine_oil') ? $last_maintenance->get('engine_oil')->date : null,
        'odometer' => $last_maintenance->get('engine_oil') ? $last_maintenance->get('engine_oil')->odometer : null,
      ],
      'power_steering_fluid' => [
        'date' => $last_maintenance->get('power_steering_fluid') ? $last_maintenance->get('power_steering_fluid')->date : null,
        'odometer' => $last_maintenance->get('power_steering_fluid') ? $last_maintenance->get('power_steering_fluid')->odometer : null,
      ],
      'radiator_fluid' => [
        'date' => $last_maintenance->get('radiator_fluid') ? $last_maintenance->get('radiator_fluid')->date : null,
        'odometer' => $last_maintenance->get('radiator_fluid') ? $last_maintenance->get('radiator_fluid')->odometer : null,
      ],
      'spark_plugs' => [
        'date' => $last_maintenance->get('spark_plugs') ? $last_maintenance->get('spark_plugs')->date : null,
        'odometer' => $last_maintenance->get('spark_plugs') ? $last_maintenance->get('spark_plugs')->odometer : null,
      ],
      'tires' => [
        'date' => $last_maintenance->get('tires') ? $last_maintenance->get('tires')->date : null,
        'odometer' => $last_maintenance->get('tires') ? $last_maintenance->get('tires')->odometer : null,
      ],
      'transmission_fluid' => [
        'date' => $last_maintenance->get('transmission_fluid') ? $last_maintenance->get('transmission_fluid')->date : null,
        'odometer' => $last_maintenance->get('transmission_fluid') ? $last_maintenance->get('transmission_fluid')->odometer : null,
      ],
    ];
  }
}
