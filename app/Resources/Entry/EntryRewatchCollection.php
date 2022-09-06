<?php

namespace App\Resources\Entry;

use Carbon\Carbon;

use Illuminate\Http\Resources\Json\JsonResource;

class EntryRewatchCollection extends JsonResource {

  public function toArray($request) {
    return Carbon::parse($this->date_rewatched)
      ->isoFormat('MMMM DD, Y');
  }
}
