<?php

namespace App\Resources\Entry;

use Illuminate\Http\Resources\Json\JsonResource;

class EntryOffquelCollection extends JsonResource {

  public function toArray($request) {

    return [
      'id' => $this->entry->uuid,
      'title' => $this->entry->title,
    ];
  }
}
