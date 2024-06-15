<?php

namespace App\Fourleaf\Resources\Gas;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   schema="FourleafGasGetResource",
 *
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
class GetResource extends JsonResource {
}
