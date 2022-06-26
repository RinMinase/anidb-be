<?php

namespace App\Resources\Entry;

use Illuminate\Http\Resources\Json\JsonResource;

class EntryBySeqDataCollection extends JsonResource {

  public function toArray($request) {

    return [
      'id' => $this->uuid,
      'id_quality' => $this->id_quality,
      'title' => $this->title,
      'dateFinished' => $this->date_lookup,
      'rewatched' => (bool) $this->date_rewatched,
      'filesize' => parse_filesize($this->filesize ?? 0),

      'episodes' => $this->episodes ?? 0,
      'ovas' => $this->ovas ?? 0,
      'specials' => $this->specials ?? 0,
    ];
  }
}
