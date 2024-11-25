<?php

namespace App\Resources\PC;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   @OA\Property(
 *     property="uuid",
 *     type="string",
 *     format="uuid",
 *     example="e9597119-8452-4f2b-96d8-f2b1b1d2f158",
 *   ),
 *   @OA\Property(property="owner", ref="#/components/schemas/PCOwner"),
 *   @OA\Property(property="label", type="string", example="Upcoming Setup"),
 *   @OA\Property(
 *     property="components",
 *     type="array",
 *     @OA\Items(ref="#/components/schemas/PCSetupSummaryResource"),
 *   ),
 *   @OA\Property(property="isActive", type="boolean", example=false),
 *   @OA\Property(property="isHidden", type="boolean", example=false),
 *   @OA\Property(property="createdAt", type="string", format="date", example="2020-10-01"),
 *   @OA\Property(property="updatedAt", type="string", format="date", example="2020-10-01"),
 *   @OA\Property(property="deletedAt", type="string", format="date", example="2020-10-01"),
 * )
 */
class PCInfoResource extends JsonResource {

  public function toArray($request) {
    return [
      'uuid' => $this->uuid,
      'owner' => $this->owner,
      'label' => $this->label,

      'isActive' => $this->is_active,
      'isHidden' => $this->is_hidden,

      'components' => $this->setups ? PCSetupSummaryResource::collection($this->setups) : [],

      'createdAt' => $this->created_at,
      'updatedAt' => $this->updated_at,
      'deletedAt' => $this->deleted_at,
    ];
  }
}
