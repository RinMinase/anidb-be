<?php

namespace App\Fourleaf\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   @OA\Property(
 *     property="stats",
 *     @OA\Property(property="averageEfficiency", type="float", minimum=0, example=12.23),
 *     @OA\Property(property="lastEfficiency", type="float", minimum=0, example=12.23),
 *     @OA\Property(property="mileage", type="integer", format="int32", minimum=0, example=1000),
 *     @OA\Property(property="age", type="string", example="1 year, 2 months"),
 *     @OA\Property(property="kmPerMonth", type="float", minimum=0, example=12.23),
 *   ),
 *
 *   @OA\Property(
 *     property="graph",
 *     @OA\Property(
 *       property="efficiency",
 *       @OA\Property(property="<date>", type="string", example="<value>"),
 *       @OA\Property(property="2020-10-20", type="float", example=12.23),
 *     ),
 *     @OA\Property(
 *       property="gas",
 *       @OA\Property(property="<date>", type="string", example="<value>"),
 *       @OA\Property(property="2020-10-20", type="float", example=12.23),
 *     ),
 *   ),
 *
 *   @OA\Property(
 *     property="maintenance",
 *     @OA\Property(
 *       property="km",
 *       @OA\Property(property="engineOil", type="string", example="normal"),
 *       @OA\Property(property="tires", type="string", example="normal"),
 *       @OA\Property(property="transmissionFluid", type="string", example="normal"),
 *       @OA\Property(property="brakeFluid", type="string", example="normal"),
 *       @OA\Property(property="radiatorFluid", type="string", example="normal"),
 *       @OA\Property(property="sparkPlugs", type="string", example="normal"),
 *       @OA\Property(property="powerSteeringFluid", type="string", example="normal"),
 *     ),
 *     @OA\Property(
 *       property="year",
 *       @OA\Property(property="engineOil", type="string", example="normal"),
 *       @OA\Property(property="transmissionFluid", type="string", example="normal"),
 *       @OA\Property(property="brakeFluid", type="string", example="normal"),
 *       @OA\Property(property="battery", type="string", example="normal"),
 *       @OA\Property(property="radiatorFluid", type="string", example="normal"),
 *       @OA\Property(property="acCoolant", type="string", example="normal"),
 *       @OA\Property(property="powerSteeringFluid", type="string", example="normal"),
 *       @OA\Property(property="tires", type="string", example="normal"),
 *     ),
 *   ),
 *
 *   @OA\Property(
 *     property="lastMaintenance",
 *     @OA\Property(
 *       property="km",
 *       @OA\Property(property="engineOil", type="string", example="2024-01-01"),
 *       @OA\Property(property="tires", type="string", example="2024-01-01"),
 *       @OA\Property(property="transmissionFluid", type="string", example="2024-01-01"),
 *       @OA\Property(property="brakeFluid", type="string", example="2024-01-01"),
 *       @OA\Property(property="radiatorFluid", type="string", example="2024-01-01"),
 *       @OA\Property(property="sparkPlugs", type="string", example="2024-01-01"),
 *       @OA\Property(property="powerSteeringFluid", type="string", example="2024-01-01"),
 *     ),
 *     @OA\Property(
 *       property="year",
 *       @OA\Property(
 *         property="acCoolant",
 *         @OA\Property(property="date", type="string", example="2024-01-01"),
 *         @OA\Property(property="odometer", type="number", minimum=0, maximum=100000, example=2000),
 *       ),
 *       @OA\Property(
 *         property="battery",
 *         @OA\Property(property="date", type="string", example="2024-01-01"),
 *         @OA\Property(property="odometer", type="number", minimum=0, maximum=100000, example=2000),
 *       ),
 *       @OA\Property(
 *         property="brakeFluid",
 *         @OA\Property(property="date", type="string", example="2024-01-01"),
 *         @OA\Property(property="odometer", type="number", minimum=0, maximum=100000, example=2000),
 *       ),
 *       @OA\Property(
 *         property="engineOil",
 *         @OA\Property(property="date", type="string", example="2024-01-01"),
 *         @OA\Property(property="odometer", type="number", minimum=0, maximum=100000, example=2000),
 *       ),
 *       @OA\Property(
 *         property="powerSteeringFluid",
 *         @OA\Property(property="date", type="string", example="2024-01-01"),
 *         @OA\Property(property="odometer", type="number", minimum=0, maximum=100000, example=2000),
 *       ),
 *       @OA\Property(
 *         property="radiatorFluid",
 *         @OA\Property(property="date", type="string", example="2024-01-01"),
 *         @OA\Property(property="odometer", type="number", minimum=0, maximum=100000, example=2000),
 *       ),
 *       @OA\Property(
 *         property="sparkPlugs",
 *         @OA\Property(property="date", type="string", example="2024-01-01"),
 *         @OA\Property(property="odometer", type="number", minimum=0, maximum=100000, example=2000),
 *       ),
 *       @OA\Property(
 *         property="tires",
 *         @OA\Property(property="date", type="string", example="2024-01-01"),
 *         @OA\Property(property="odometer", type="number", minimum=0, maximum=100000, example=2000),
 *       ),
 *       @OA\Property(
 *         property="transmissionFluid",
 *         @OA\Property(property="date", type="string", example="2024-01-01"),
 *         @OA\Property(property="odometer", type="number", minimum=0, maximum=100000, example=2000),
 *       ),
 *     ),
 *   ),
 * )
 */
class GetGasResource extends JsonResource {

  public function toArray($request) {
    return [
      'stats' => [
        'averageEfficiency' => $this['stats']['average_efficiency'],
        'lastEfficiency' => $this['stats']['last_efficiency'],
        'mileage' => $this['stats']['mileage'],
        'age' => $this['stats']['age'],
        'kmPerMonth' => $this['stats']['km_per_month'],
      ],
      'graph' => [
        'efficiency' => $this['graph']['efficiency'],
        'gas' => $this['graph']['gas'],
      ],
      'maintenance' => [
        'km' => [
          'engineOil' => $this['maintenance']['km']['engine_oil'],
          'tires' => $this['maintenance']['km']['tires'],
          'transmissionFluid' => $this['maintenance']['km']['transmission_fluid'],
          'brakeFluid' => $this['maintenance']['km']['brake_fluid'],
          'radiatorFluid' => $this['maintenance']['km']['radiator_fluid'],
          'sparkPlugs' => $this['maintenance']['km']['spark_plugs'],
          'powerSteeringFluid' => $this['maintenance']['km']['power_steering_fluid'],
        ],
        'year' => [
          'engineOil' => $this['maintenance']['year']['engine_oil'],
          'transmissionFluid' => $this['maintenance']['year']['transmission_fluid'],
          'brakeFluid' => $this['maintenance']['year']['brake_fluid'],
          'battery' => $this['maintenance']['year']['battery'],
          'radiatorFluid' => $this['maintenance']['year']['radiator_fluid'],
          'acCoolant' => $this['maintenance']['year']['ac_coolant'],
          'powerSteeringFluid' => $this['maintenance']['year']['power_steering_fluid'],
          'tires' => $this['maintenance']['year']['tires'],
        ],
      ],
      'lastMaintenance' => [
        'acCoolant' => [
          'date' => $this['last_maintenance']['ac_coolant']['date'],
          'odometer' => $this['last_maintenance']['ac_coolant']['odometer'],
        ],
        'battery' => [
          'date' => $this['last_maintenance']['battery']['date'],
          'odometer' => $this['last_maintenance']['battery']['odometer'],
        ],
        'brakeFluid' => [
          'date' => $this['last_maintenance']['brake_fluid']['date'],
          'odometer' => $this['last_maintenance']['brake_fluid']['odometer'],
        ],
        'engineOil' => [
          'date' => $this['last_maintenance']['engine_oil']['date'],
          'odometer' => $this['last_maintenance']['engine_oil']['odometer'],
        ],
        'powerSteeringFluid' => [
          'date' => $this['last_maintenance']['power_steering_fluid']['date'],
          'odometer' => $this['last_maintenance']['power_steering_fluid']['odometer'],
        ],
        'radiatorFluid' => [
          'date' => $this['last_maintenance']['radiator_fluid']['date'],
          'odometer' => $this['last_maintenance']['radiator_fluid']['odometer'],
        ],
        'sparkPlugs' => [
          'date' => $this['last_maintenance']['spark_plugs']['date'],
          'odometer' => $this['last_maintenance']['spark_plugs']['odometer'],
        ],
        'tires' => [
          'date' => $this['last_maintenance']['tires']['date'],
          'odometer' => $this['last_maintenance']['tires']['odometer'],
        ],
        'transmissionFluid' => [
          'date' => $this['last_maintenance']['transmission_fluid']['date'],
          'odometer' => $this['last_maintenance']['transmission_fluid']['odometer'],
        ],
      ]
    ];
  }
}
