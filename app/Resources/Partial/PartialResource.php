<?php

namespace App\Resources\Partial;

use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
  properties: [
    new OA\Property(property: "uuid", type: "string", format: "uuid", example: "e9597119-8452-4f2b-96d8-f2b1b1d2f158"),
    new OA\Property(property: "title", type: "string", example: "Sample Title"),
    new OA\Property(property: "priority", type: "string", enum: ["High", "Normal", "Low"], example: "High"),
  ]
)]
class PartialResource extends JsonResource {

  public function toArray($request) {

    return [
      'uuid' => $this->uuid,
      'title' => $this->title,
      'priority' => $this->priority->priority,
    ];
  }
}
