<?php

namespace App\Resources\Partial;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   example={{
 *     "id": "e9597119-8452-4f2b-96d8-f2b1b1d2f158",
 *     "title": "Sample Title",
 *     "priority": "High",
 *   }},
 *   type="array",
 *   @OA\Items(
 *     @OA\Property(property="id", type="string", format="uuid"),
 *     @OA\Property(property="title", type="string"),
 *     @OA\Property(property="priority", type="string", enum={"High", "Normal", "Low"}),
 *   ),
 * )
 */
class PartialCollection extends JsonResource {

  public function toArray($request) {

    return [
      'id' => $this->uuid,
      'title' => $this->title,
      'priority' => $this->priority->priority,
    ];
  }
}
