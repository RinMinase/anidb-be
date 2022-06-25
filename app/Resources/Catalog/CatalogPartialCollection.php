<?php

namespace App\Resources\Catalog;

use Illuminate\Http\Resources\Json\JsonResource;

class CatalogPartialCollection extends JsonResource {

  public function toArray($request) {

    return [
      'id' => $this->uuid,
      'title' => $this->title,
      'priority' => $this->priority->priority,
    ];
  }
}
