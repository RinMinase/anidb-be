<?php

namespace App\Resources\Entry;

use Illuminate\Http\Resources\Json\JsonResource;

class EntryOffquelCollection extends JsonResource {

  public function toArray($request) {

    return [
      'id' => $this->id_entries_offquel,
      'title' => $this->entry->title,
    ];
  }
}
