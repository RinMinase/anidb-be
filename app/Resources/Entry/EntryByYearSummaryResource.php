<?php

namespace App\Resources\Entry;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   @OA\Property(
 *     property="year",
 *     type="integer",
 *     format="int32",
 *     nullable=true,
 *     description="null value on uncategorized entries",
 *     example="2020",
 *   ),
 *   @OA\Property(
 *     property="count",
 *     type="integer",
 *     format="int32",
 *     nullable=true,
 *     description="null value whenever seasons is present; total count of 'null' year",
 *     example=null,
 *   ),
 *   @OA\Property(
 *     property="seasons",
 *     nullable=true,
 *     description="null value on uncategorized entries",
 *     @OA\Property(property="Winter", type="integer", format="int32", example=1),
 *     @OA\Property(property="Spring", type="integer", format="int32", example=2),
 *     @OA\Property(property="Summer", type="integer", format="int32", example=3),
 *     @OA\Property(property="Fall", type="integer", format="int32", example=4),
 *   ),
 * )
 */
class EntryByYearSummaryResource extends JsonResource {

  public function toArray($request) {

    return [
      'year' => $this['year'],
      'count' => $this['count'],
      'seasons' => $this['seasons'] ? [
        'Winter' => $this['seasons']['Winter'] ?? 0,
        'Spring' => $this['seasons']['Spring'] ?? 0,
        'Summer' => $this['seasons']['Summer'] ?? 0,
        'Fall' => $this['seasons']['Fall'] ?? 0,
      ] : null,
    ];
  }
}
