<?php

namespace App\Resources\Entry;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   @OA\Property(
 *     property="Winter",
 *     type="array",
 *     @OA\Items(ref="#/components/schemas/EntrySummaryResource")
 *   ),
 *   @OA\Property(
 *     property="Spring",
 *     type="array",
 *     @OA\Items(ref="#/components/schemas/EntrySummaryResource")
 *   ),
 *   @OA\Property(
 *     property="Summer",
 *     type="array",
 *     @OA\Items(ref="#/components/schemas/EntrySummaryResource")
 *   ),
 *   @OA\Property(
 *     property="Fall",
 *     type="array",
 *     @OA\Items(ref="#/components/schemas/EntrySummaryResource")
 *   ),
 *   @OA\Property(
 *     property="Uncategorized",
 *     type="array",
 *     @OA\Items(ref="#/components/schemas/EntrySummaryResource")
 *   ),
 * )
 */
class EntryByYearResource extends JsonResource {

  public function toArray($request) {

    return [
      'Winter' => $this['Winter'] ?
        EntrySummaryResource::collection($this['Winter']) : [],

      'Spring' => $this['Spring'] ?
        EntrySummaryResource::collection($this['Spring']) : [],

      'Summer' => $this['Summer'] ?
        EntrySummaryResource::collection($this['Summer']) : [],

      'Fall' => $this['Fall'] ?
        EntrySummaryResource::collection($this['Fall']) : [],

      'Uncategorized' => $this['Uncategorized'] ?
        EntrySummaryResource::collection($this['Uncategorized']) : [],
    ];
  }
}
