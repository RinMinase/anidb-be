<?php

namespace App\Resources\Car;

use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
  properties: [
    new OA\Property(property: "id", type: "integer", format: "int64", example: 1),
    new OA\Property(property: "date", type: "string", example: "2020-10-20"),
    new OA\Property(property: "description", type: "string", example: "Engine Oil Change"),
    new OA\Property(property: "odometer", type: "integer", format: "int32", minimum: 0, example: 1000),
    new OA\Property(
      property: "parts",
      type: "array",
      description: "Array of Parts",
      items: new OA\Items(ref: '#/components/schemas/MaintenancePartResource')
    ),
    new OA\Property(
      property: "partSummaryKeys",
      type: "array",
      description: "Array of Part types",
      items: new OA\Items(
        type: "string",
        example: "engine_oil",
      )
    ),
    new OA\Property(
      property: "partSummaryLabels",
      type: "array",
      description: "Array of Part labels",
      items: new OA\Items(
        type: "string",
        example: "Engine Oil",
      )
    ),
  ]
)]
class MaintenanceResource extends JsonResource {

  public function toArray($request) {

    return array_replace_recursive($this->resource->toArray(), [
      'parts' => MaintenancePartResource::collection($this->parts),
      'part_summary_keys' => $this->parts->pluck('type.type')->unique()->values(),
      'part_summary_labels' => $this->parts->pluck('type.label')->unique()->values(),
    ]);
  }
}
