<?php

namespace App\Resources\Entry;

use Illuminate\Http\Resources\Json\JsonResource;

class EntryRewatchCollection extends JsonResource {

  public function toArray($request) {
    return $this->date_rewatched;
  }
}
