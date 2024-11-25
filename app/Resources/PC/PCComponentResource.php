<?php

namespace App\Resources\PC;

use Illuminate\Http\Resources\Json\JsonResource;

class PCComponentResource extends JsonResource {

  public function toArray($request) {
    return [
      'id' => $this->id,
      'type' => $this->type,

      'name' => $this->name,
      'description' => $this->description,

      'price' => $this->price,
      'purchaseDate' => $this->purchase_date,
      'purchaseLocation' => $this->purchase_location,
      'purchaseNotes' => $this->purchase_notes,
      'isOnhand' => $this->is_onhand,

      'createdAt' => $this->created_at,
      'updatedAt' => $this->updated_at,
      'deletedAt' => $this->deleted_at,
    ];
  }
}
