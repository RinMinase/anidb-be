<?php

namespace App\Resources\Catalog;

use Illuminate\Http\Resources\Json\JsonResource;

class CatalogCollection extends JsonResource {

  public function toArray($request) {

    return [
      'id' => $this->uuid,
      'year' => $this->year,
      'season' => $this->season,
    ];
  }
}
