<?php

namespace App\Resources\Log;

use Illuminate\Http\Resources\Json\JsonResource;

class LogCollection extends JsonResource {

  public function toArray($request) {

    return [
      'id' => $this->uuid,
      'table_changed' => $this->table_changed,
      'id_changed' => $this->id_changed,
      'description' => $this->description,
      'action' => $this->action,
      'created_at' => $this->created_at,
    ];
  }
}
