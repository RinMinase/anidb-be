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
 *   @OA\Property(property="price", type="integer", example=1234),
 *   @OA\Property(property="priceFormatted", type="string", example="1,234"),
 *   @OA\Property(property="priceEstimate", type="integer", example=1234),
 *   @OA\Property(property="priceEstimateFormatted", type="string", example="1,234"),
 *   @OA\Property(property="purhcaseDate", type="string", example="2020-10-10"),
 *   @OA\Property(property="purhcaseLocation", type="string", example="Location"),
 *   @OA\Property(property="purhcaseNotes", type="string", example="Notes"),
 *   @OA\Property(property="isOnhand", type="boolean", example=true),
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

      'price' => $this->component->price ?? null,
      'priceFormatted' => $this->component->price ?
        number_format($this->component->price) :
        null,

      'priceEstimate' => $this->component->price_estimate ?? null,
      'priceEstimateFormatted' => $this->component->price_estimate ?
        number_format($this->component->price_estimate) :
        null,


      'purchaseDate' => $this->component->purchase_date ?? '',
      'purchaseLocation' => $this->component->purchase_location ?? '',
      'purchaseNotes' => $this->component->purchase_notes ?? '',
      'isOnhand' => $this->component->is_onhand ?? '',
    ];
  }
}
