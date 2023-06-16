<?php

namespace App\Resources\Entry;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   @OA\Property(property="dateLastEntry", type="string", example="Apr 01, 2015"),
 *   @OA\Property(property="daysLastEntry", type="integer", format="int32", example=2974),
 *   @OA\Property(property="dateOldestEntry", type="string", example="Jan 01, 2011"),
 *   @OA\Property(property="daysOldestEntry", type="integer", format="int32", example=4525),
 *   @OA\Property(property="totalEps", type="integer", format="int32", example=0),
 *   @OA\Property(property="totalTitles", type="integer", format="int32", example=7),
 *   @OA\Property(property="totalCours", type="integer", format="int32", example=0),
 *   @OA\Property(property="titlesPerWeek", type="number", example=0.01),
 *   @OA\Property(property="coursPerWeek", type="integer", format="int32", example=0),
 *   @OA\Property(property="epsPerWeek", type="integer", format="int32", example=0),
 *   @OA\Property(property="epsPerDay", type="integer", format="int32", example=0),
 * )
 */
class EntryLastStatsResource extends JsonResource {

  public function toArray($request) {

    return [
      'dateLastEntry' => $this['dateLastEntry'],
      'daysLastEntry' => $this['daysLastEntry'],
      'dateOldestEntry' => $this['dateOldestEntry'],
      'daysOldestEntry' => $this['daysOldestEntry'],
      'totalEps' => $this['totalEps'],
      'totalTitles' => $this['totalTitles'],
      'totalCours' => $this['totalCours'],
      'titlesPerWeek' => $this['titlesPerWeek'],
      'coursPerWeek' => $this['coursPerWeek'],
      'epsPerWeek' => $this['epsPerWeek'],
      'epsPerDay' => $this['epsPerDay'],
    ];
  }
}
