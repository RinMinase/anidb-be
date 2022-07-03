<?php

namespace App\Resources\Partial;

use Illuminate\Http\Resources\Json\JsonResource;

class PartialCollection extends JsonResource {

  public function toArray($request) {

    return [
      'id' => $this->uuid,
      'title' => $this->title,
      'priority' => $this->priority->priority,
    ];
  }
}
