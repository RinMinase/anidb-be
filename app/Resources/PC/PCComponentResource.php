<?php

namespace App\Resources\PC;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   @OA\Property(property="id", type="integer", format="int32", example=1),
 *   @OA\Property(property="idType", type="integer", format="int32", example=1),
 *   @OA\Property(property="name", type="string", example="Sample Component Name"),
 *   @OA\Property(property="description", type="string", example="Sample Component Description"),
 *   @OA\Property(property="price", type="integer", format="int32", example=10000),
 *   @OA\Property(property="priceEstimate", type="integer", format="int32", example=15000),
 *   @OA\Property(property="purchaseDate", type="string", example="2020-10-01"),
 *   @OA\Property(property="purchaseDateFormatted", type="string", example="Oct 01, 2020"),
 *   @OA\Property(property="purchaseLocation", type="string", example="Store Name"),
 *   @OA\Property(property="purchaseNotes", type="string", example="Some notes"),
 *   @OA\Property(property="isOnhand", type="boolean", example=true),
 *   @OA\Property(property="isPurchased", type="boolean", example=true),
 *   @OA\Property(property="descriptiveName", type="string", example="Sample Component Name (10,000)", description="{name} ({price})"),
 * ),
 */
class PCComponentResource extends JsonResource {

  public function toArray($request) {
    return [
      'id' => $this->id,
      'idType' => $this->type->id,
      'type' => $this->type,

      'name' => $this->name,
      'description' => $this->description,

      // On collection only
      // If names contains duplicate, this shows {name} ({price})
      'descriptiveName' => $this->name . ' (' . number_format($this->price ?? 0) . ')',

      'price' => $this->price,
      'purchaseDate' => $this->purchase_date,
      'purchaseDateFormatted' => $this->format_purnchase_date(),
      'purchaseLocation' => $this->purchase_location,
      'purchaseNotes' => $this->purchase_notes,
      'isOnhand' => $this->is_onhand,

      'createdAt' => $this->created_at,
      'updatedAt' => $this->updated_at,
      'deletedAt' => $this->deleted_at,
    ];
  }

  private function format_purnchase_date() {
    $date = $this->purchase_date ?? '';

    return (!$date) ? '' :  Carbon::parse($this->purchase_date)->format('M d, Y');
  }
}
