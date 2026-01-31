<?php

namespace App\Resources\Anilist;

use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
  properties: [
    new OA\Property(property: "id", type: "string", example: "12345"),
    new OA\Property(property: "title", type: "string", example: "Sample Title"),
  ]
)]
class AnilistSearchResource extends JsonResource {

  public function toArray($request) {
    return [
      'id' => $this->resource['id'],
      'title' => $this->parseTitle(),
    ];
  }

  private function parseTitle() {
    $title = '';

    if (isset($this->resource['title']) && !empty($this->resource['title']['romaji'])) {
      $title = $this->resource['title']['romaji'];
    }

    return $title;
  }
}
