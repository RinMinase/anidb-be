<?php

namespace App\Resources\Genre;

use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
  properties: [
    new OA\Property(property: "id", type: "integer", format: "int32", example: 1),
    new OA\Property(property: "genre", type: "string", example: "Genre Name"),
  ]
)]
class GenreResource extends JsonResource {

  public function toArray($request) {

    return [
      'id' => $this->genre->id,
      'genre' => $this->genre->genre,
    ];
  }
}
