<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Traits\RefreshableAutoIncrements;

/**
 * @OA\Schema(
 *   @OA\Property(property="id", type="integer", format="int32", example=1),
 *   @OA\Property(property="title", type="string", example="Summer List"),
 *   @OA\Property(property="dateFrom", type="string", format="date", example="2020-01-01"),
 *   @OA\Property(property="dateTo", type="string", format="date", example="2020-02-01"),
 * )
 */
class Sequence extends Model {

  use RefreshableAutoIncrements;

  protected $fillable = [
    'title',
    'date_from',
    'date_to',
  ];

  protected $hidden = [
    'created_at',
    'updated_at',
  ];

  protected function casts(): array {
    return [
      'created_at' => 'datetime:Y-m-d H:i:s',
    ];
  }
}
