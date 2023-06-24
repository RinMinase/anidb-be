<?php

namespace App\Resources\Log;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   @OA\Property(
 *     property="id",
 *     type="string",
 *     format="uuid",
 *     example="9ef81943-78f0-4d1c-a831-a59fb5af339c",
 *   ),
 *   @OA\Property(property="table_changed", type="string", example="marathon"),
 *   @OA\Property(property="id_changed", type="string", example=1),
 *   @OA\Property(
 *     property="description",
 *     type="string",
 *     example="title changed from 'old' to 'new'",
 *   ),
 *   @OA\Property(property="action", type="string", example="add"),
 *   @OA\Property(
 *     property="created_at",
 *     type="string",
 *     format="date-time",
 *     example="2020-01-01 00:00:00",
 *   ),
 * )
 */
class LogResource extends JsonResource {

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
