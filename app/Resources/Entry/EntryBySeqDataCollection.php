<?php

namespace App\Resources\Entry;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class EntryBySeqDataCollection extends JsonResource {

  public function toArray($request) {

    return [
      'id' => $this->uuid,
      'id_quality' => $this->id_quality,
      'title' => $this->title,
      'dateFinished' => $this->calcDateFinished(),
      'rewatched' => (bool) $this->date_rewatched,
      'filesize' => parse_filesize($this->filesize ?? 0),

      'episodes' => $this->episodes ?? 0,
      'ovas' => $this->ovas ?? 0,
      'specials' => $this->specials ?? 0,
    ];
  }

  private function calcDateFinished() {
    $last_date_finished = '';

    if ($this->date_lookup) {
      $last_date_finished = Carbon::parse($this->date_lookup)
        ->format('M d, Y');
    }

    return $last_date_finished;
  }
}
