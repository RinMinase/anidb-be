<?php

namespace App\Resources\Entry;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

/**
 * @OA\Schema(
 *   @OA\Property(
 *     property="id",
 *     type="string",
 *     format="uuid",
 *     example="e9597119-8452-4f2b-96d8-f2b1b1d2f158",
 *   ),
 *   @OA\Property(
 *     property="quality",
 *     type="string",
 *     enum={"4K 2160", "FHD 1080p", "HD 720p", "HQ 480p", "LQ 360p"},
 *     example="4K 2160p",
 *   ),
 *   @OA\Property(property="title", type="string", example="Sample Title"),
 *   @OA\Property(property="dateFinished", type="string", example="Mar 01, 2011"),
 *   @OA\Property(property="rewatched", type="boolean", example=false),
 *   @OA\Property(property="filesize", type="string", example="10.25 GB"),
 *   @OA\Property(property="episodes", type="integer", format="int32", example=25),
 *   @OA\Property(property="ovas", type="integer", format="int32", example=1),
 *   @OA\Property(property="specials", type="integer", format="int32", example=1),
 * ),
 */
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
