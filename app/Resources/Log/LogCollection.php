<?php

namespace App\Resources\Log;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   example={{
 *     "id": "9ef81943-78f0-4d1c-a831-a59fb5af339c",
 *     "table_changed": "marathon",
 *     "id_changed": 1,
 *     "description": "title changed from 'old' to 'new'",
 *     "action": "add",
 *     "created_at": "2020-01-01 00:00:00",
 *   }},
 *   type="array",
 *   @OA\Items(
 *     @OA\Property(property="id", type="string", format="uuid"),
 *     @OA\Property(property="table_changed", type="string"),
 *     @OA\Property(property="id_changed", type="string"),
 *     @OA\Property(property="description", type="string"),
 *     @OA\Property(property="action", type="string"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *   ),
 * )
 */
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
