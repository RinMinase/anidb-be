<?php

namespace App\Resources\Catalog;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   @OA\Property(
 *     property="id",
 *     type="string",
 *     format="uuid",
 *     example="e9597119-8452-4f2b-96d8-f2b1b1d2f158",
 *   ),
 *   @OA\Property(property="year", type="integer", format="int32", example=2020),
 *   @OA\Property(
 *     property="season",
 *     type="string",
 *     enum={"Winter", "Spring", "Summer", "Fall"},
 *     example="Winter",
 *   ),
 * )
 */
class CatalogResource extends JsonResource {

  public function toArray($request) {

    return [
      'id' => $this->uuid,
      'year' => $this->year,
      'season' => $this->season,
    ];
  }
}
