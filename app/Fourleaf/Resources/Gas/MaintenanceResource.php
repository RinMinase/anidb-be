<?php

namespace App\Fourleaf\Resources\Gas;

use Illuminate\Http\Resources\Json\JsonResource;

use Carbon\Carbon;

/**
 * @OA\Schema(
 *   @OA\Property(property="date", type="string", example="2020-10-20"),
 *   @OA\Property(property="description", type="string", example="Engine Oil Change"),
 *   @OA\Property(
 *     property="parts",
 *     type="array",
 *     @OA\Items(
 *       type="string",
 *       example="engine_oil",
 *       enum={
 *         "others",
 *         "ac_coolant",
 *         "battery",
 *         "brake_fluid",
 *         "engine_oil",
 *         "power_steering_fluid",
 *         "radiator_fluid",
 *         "spark_plugs",
 *         "tires",
 *         "transmission",
 *       }
 *     ),
 *   ),
 *   @OA\Property(property="odometer", type="integer", format="int32", minimum=0, example=1000),
 * )
 */
class MaintenanceResource extends JsonResource {

  public function toArray($request) {

    return array_replace_recursive($this->resource->toArray(), [
      'date' => Carbon::parse($this->date)->format('M d, Y'),
      'parts' => $this->parts->pluck('part'),
    ]);
  }
}
