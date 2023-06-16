<?php

namespace App\Resources\Entry;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   @OA\Property(
 *     property="letter",
 *     type="string",
 *     minLength=1,
 *     maxLength=1,
 *     example="A",
 *   ),
 *   @OA\Property(property="titles", type="integer", format="int32", example=12),
 *   @OA\Property(property="filesize", type="string", example="12.23 GB"),
 * )
 */
class EntryByNameResource extends JsonResource {

  public function toArray($request) {

    return [
      'letter' => $this['letter'],
      'titles' => $this['titles'],
      'filesize' => $this['filesize'],
    ];
  }
}
