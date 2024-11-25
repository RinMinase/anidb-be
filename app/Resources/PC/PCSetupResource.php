<?php

namespace App\Resources\PC;

use Illuminate\Http\Resources\Json\JsonResource;

class PCSetupResource extends JsonResource {

  public function toArray($request) {
    return [
      'component' => $this->component ? new PCComponentSummaryResource($this->component) : [],
      'count' => $this->count,
      'isHidden' => $this->is_hidden,
      'createdAt' => $this->created_at,
      'updatedAt' => $this->updated_at,
      'deletedAt' => $this->deleted_at,
    ];
  }
}
