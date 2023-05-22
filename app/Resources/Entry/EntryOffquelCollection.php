<?php

namespace App\Resources\Entry;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   example={{
 *     "id": "89e3be00-9d4f-4c4f-a99f-c12cbfba04ab",
 *     "title": "Offquel Title"
 *   }},
 *   type="array",
 *   @OA\Items(
 *     @OA\Property(property="id", type="string", format="uuid"),
 *     @OA\Property(property="title", type="string"),
 *   ),
 * )
 */
class EntryOffquelCollection extends JsonResource {

  public function toArray($request) {

    return [
      'id' => $this->entry->uuid,
      'title' => $this->entry->title,
    ];
  }
}
