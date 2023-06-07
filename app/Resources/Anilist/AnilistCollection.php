<?php

namespace App\Resources\Anilist;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   example={{
 *     "id": "12345",
 *     "title": "Sample Title",
 *   }},
 *   type="array",
 *   @OA\Items(
 *     @OA\Property(property="id", type="string"),
 *     @OA\Property(property="title", type="string"),
 *   ),
 * )
 */
class AnilistCollection extends JsonResource {

  public function toArray($request) {
    return [
      'id' => $this->resource['id'],
      'title' => $this->resource['title']['romaji'],
    ];
  }
}
