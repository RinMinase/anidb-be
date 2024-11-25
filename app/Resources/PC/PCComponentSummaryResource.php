<?php

namespace App\Resources\PC;

use Illuminate\Http\Resources\Json\JsonResource;

class PCComponentSummaryResource extends JsonResource {

  public function toArray($request) {
    return [
      'type' => $this->type,
      'name' => $this->name,
      'description' => $this->description,
    ];
  }
}
