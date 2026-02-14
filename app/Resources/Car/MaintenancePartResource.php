<?php

namespace App\Resources\Car;

use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
  properties: [
    new OA\Property(property: "type", type: "string", example: "engine_oil"),
    new OA\Property(property: "label", type: "string", example: "Engine Oil"),
  ]
)]
class MaintenancePartResource extends JsonResource {

  public function toArray($request) {

    return [
      'type' => $this->type->type,
      'label' => $this->type->label,
    ];
  }
}
