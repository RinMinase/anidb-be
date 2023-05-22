<?php

namespace App\Resources\Catalog;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   example={{
 *     "id": "e9597119-8452-4f2b-96d8-f2b1b1d2f158",
 *     "year": 2020,
 *     "season": "Winter",
 *   }},
 *   type="array",
 *   @OA\Items(
 *     @OA\Property(property="id", type="string", format="uuid"),
 *     @OA\Property(property="year", type="integer", format="int32"),
 *     @OA\Property(
 *       property="season",
 *       type="string",
 *       enum={"Winter", "Spring", "Summer", "Fall"}
 *     ),
 *   ),
 * )
 */
class CatalogCollection extends JsonResource {

  public function toArray($request) {

    return [
      'id' => $this->uuid,
      'year' => $this->year,
      'season' => $this->season,
    ];
  }
}
