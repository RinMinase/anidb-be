<?php

namespace App\Resources\Entry;

use Carbon\Carbon;

use Illuminate\Http\Resources\Json\JsonResource;

class EntryRewatchCollection extends JsonResource {

  public function toArray($request) {
    $date = Carbon::parse($this->date_rewatched);

    return [
      'id' => $this->uuid,
      'dateIso' => $date,
      'date' => $date->isoFormat('MMMM DD, Y'),
    ];
  }
}
