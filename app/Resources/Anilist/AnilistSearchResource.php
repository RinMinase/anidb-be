<?php

namespace App\Resources\Anilist;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   @OA\Property(property="id", type="string", example="12345"),
 *   @OA\Property(property="title", type="string", example="Sample Title"),
 * )
 */
class AnilistSearchResource extends JsonResource {

  public function toArray($request) {
    return [
      'id' => $this->resource['id'],
      'title' => $this->resource['title']['romaji'],
    ];
  }
}
