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
 * )
 */
class GetGasResource extends JsonResource {

  public function toArray($request) {
    return [
      'stats' => [
        'averageEfficiency' => $this->stats->averageEfficiency,
        'lastEfficiency' => $this->stats->lastEfficiency,
        'mileage' => $this->stats->mileage,
        'age' => $this->stats->age,
        'kmPerMonth' => $this->stats->kmPerMonth,
      ],
      'graph' => [
        'efficiency' => $this->graph->efficiency,
        'gas' => $this->graph->gas,
      ],
      'maintenance' => [
        'km' => [
          'engineOil' => $this->maintenance->km->engineOil,
          'tires' => $this->maintenance->km->tires,
          'transmissionFluid' => $this->maintenance->km->transmissionFluid,
          'brakeFluid' => $this->maintenance->km->brakeFluid,
          'radiatorFluid' => $this->maintenance->km->radiatorFluid,
          'sparkPlugs' => $this->maintenance->km->sparkPlugs,
          'powerSteeringFluid' => $this->maintenance->km->powerSteeringFluid,
        ],
        'year' => [
          'engineOil' => $this->maintenance->year->engineOil,
          'transmissionFluid' => $this->maintenance->year->transmissionFluid,
          'brakeFluid' => $this->maintenance->year->brakeFluid,
          'battery' => $this->maintenance->year->battery,
          'radiatorFluid' => $this->maintenance->year->radiatorFluid,
          'acCoolant' => $this->maintenance->year->acCoolant,
          'powerSteeringFluid' => $this->maintenance->year->powerSteeringFluid,
          'tires' => $this->maintenance->year->tires,
        ],
      ],
    ];
  }
}
