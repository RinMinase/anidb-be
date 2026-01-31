<?php

namespace App\Resources\Entry;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
  properties: [
    new OA\Property(property: "id", type: "string", format: "uuid", example: "e9597119-8452-4f2b-96d8-f2b1b1d2f158"),
    new OA\Property(
      property: "quality",
      type: "string",
      enum: ["4K 2160", "FHD 1080p", "HD 720p", "HQ 480p", "LQ 360p"],
      example: "4K 2160p"
    ),
    new OA\Property(property: "title", type: "string", example: "Sample Title"),
    new OA\Property(property: "dateFinished", type: "string", example: "Mar 01, 2011"),
    new OA\Property(property: "rewatched", type: "boolean", example: false),
    new OA\Property(property: "filesize", type: "string", example: "10.25 GB"),
    new OA\Property(property: "episodes", type: "integer", format: "int32", example: 25),
    new OA\Property(property: "ovas", type: "integer", format: "int32", example: 1),
    new OA\Property(property: "specials", type: "integer", format: "int32", example: 1),
  ]
)]
class EntryBySequenceResource extends JsonResource {

  public function toArray($request) {

    return [
      'id' => $this->uuid,
      'quality' => $this->quality,
      'title' => $this->title,
      'dateFinished' => $this->calcDateFinished(),
      'filesize' => parse_filesize($this->filesize ?? 0),

      // should belong in the sequence date
      'rewatched' => (bool) $this->date_rewatched,

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
