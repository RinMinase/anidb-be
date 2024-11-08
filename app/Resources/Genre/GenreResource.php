<?php

namespace App\Resources\Genre;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   @OA\Property(property="id", type="integer", format="int32", example=1),
 *   @OA\Property(property="genre", type="string", example="Genre Name"),
 * )
 */
class GenreResource extends JsonResource {

  public function toArray($request) {

    return [
      'id' => $this->genre->id,
      'genre' => $this->genre->genre,
    ];
  }
}
