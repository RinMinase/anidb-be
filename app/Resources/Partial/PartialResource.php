<?php

namespace App\Resources\Partial;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   @OA\Property(
 *     property="id",
 *     type="string",
 *     format="uuid",
 *     example="e9597119-8452-4f2b-96d8-f2b1b1d2f158",
 *   ),
 *   @OA\Property(property="title", type="string", example="Sample Title"),
 *   @OA\Property(
 *     property="priority",
 *     type="string",
 *     enum={"High", "Normal", "Low"},
 *     example="High",
 *   ),
 * )
 */
class PartialResource extends JsonResource {

  public function toArray($request) {

    return [
      'id' => $this->uuid,
      'title' => $this->title,
      'priority' => $this->priority->priority,
    ];
  }
}
