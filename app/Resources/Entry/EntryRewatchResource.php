<?php

namespace App\Resources\Entry;

use Carbon\Carbon;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   @OA\Property(
 *     property="id",
 *     type="string",
 *     format="uuid",
 *     example="af846c35-a51e-4534-9559-c75114e61d84",
 *   ),
 *   @OA\Property(
 *     property="dateIso",
 *     type="string",
 *     format="date-time",
 *     example="2011-03-01T00:00:00.000000Z",
 *   ),
 *   @OA\Property(property="date", type="string", example="March 01, 2011"),
 * )
 */
class EntryRewatchResource extends JsonResource {

  public function toArray($request) {
    $date = Carbon::parse($this->date_rewatched);

    return [
      'id' => $this->uuid,
      'dateIso' => $date,
      'date' => $date->isoFormat('MMMM DD, Y'),
    ];
  }
}
