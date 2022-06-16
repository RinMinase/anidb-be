<?php

namespace App\Resources\Quality;

use Illuminate\Http\Resources\Json\JsonResource;

class QualityCollection extends JsonResource {

  public function toArray($request) {
    return $this->quality;
  }
}
