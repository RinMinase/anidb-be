<?php

namespace App\Resources\PC;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   @OA\Property(property="id", type="integer", format="int32", example=1),
 *   @OA\Property(property="type", type="string", example="CPU"),
 *   @OA\Property(property="name", type="string", example="Component Name"),
 *   @OA\Property(property="description", type="string", example="Component Description"),
 *   @OA\Property(property="count", type="integer", example=2),
 *   @OA\Property(property="isHidden", type="boolean", example=false),
 * )
 */
class PCSetupSummaryResource extends JsonResource {

  public function toArray($request) {
    return [
      'id' => $this->id,
      'type' => $this->component->type->name ?? '',
      'name' => $this->component->name ?? '',
      'description' => $this->component->description ?? '',
      'count' => $this->count,
      'isHidden' => $this->is_hidden,
    ];
  }
}
