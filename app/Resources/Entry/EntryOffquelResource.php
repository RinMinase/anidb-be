<?php

namespace App\Resources\Entry;

use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
  properties: [
    new OA\Property(property: 'id', type: 'string', format: 'uuid', example: '89e3be00-9d4f-4c4f-a99f-c12cbfba04ab'),
    new OA\Property(property: 'title', type: 'string', example: 'Offquel Title'),
  ]
)]
class EntryOffquelResource extends JsonResource {

  public function toArray($request) {

    return [
      'id' => $this->entry->uuid,
      'title' => $this->entry->title,
    ];
  }
}
