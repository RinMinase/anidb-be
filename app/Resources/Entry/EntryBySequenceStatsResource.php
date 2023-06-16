<?php

namespace App\Resources\Entry;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   @OA\Property(property="titles_per_day", type="number", example=1.23),
 *   @OA\Property(property="eps_per_day", type="number", example=2.34),
 *   @OA\Property(property="quality_2160", type="integer", format="int32", example=1),
 *   @OA\Property(property="quality_1080", type="integer", format="int32", example=2),
 *   @OA\Property(property="quality_720", type="integer", format="int32", example=3),
 *   @OA\Property(property="quality_480", type="integer", format="int32", example=4),
 *   @OA\Property(property="quality_360", type="integer", format="int32", example=5),
 *   @OA\Property(property="total_titles", type="integer", format="int32", example=12),
 *   @OA\Property(property="total_eps", type="integer", format="int32", example=123),
 *   @OA\Property(property="total_size", type="string", example="12.34 GB"),
 *   @OA\Property(property="total_days", type="integer", format="int32", example=123),
 *   @OA\Property(property="start_date", type="string", example="Jan 01, 2000"),
 *   @OA\Property(property="end_date", type="string", example="Feb 01, 2000"),
 * )
 */
class EntryBySequenceStatsResource extends JsonResource {

  public function toArray($request) {

    return [
      'titles_per_day' => $this['titles_per_day'],
      'eps_per_day' => $this['eps_per_day'],
      'quality_2160' => $this['quality_2160'],
      'quality_1080' => $this['quality_1080'],
      'quality_720' => $this['quality_720'],
      'quality_480' => $this['quality_480'],
      'quality_360' => $this['quality_360'],
      'total_titles' => $this['total_titles'],
      'total_eps' => $this['total_eps'],
      'total_size' => $this['total_size'],
      'total_days' => $this['total_days'],
      'start_date' => $this['start_date'],
      'end_date' => $this['end_date'],
    ];
  }
}
