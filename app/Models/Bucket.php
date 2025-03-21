<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Traits\RefreshableAutoIncrements;

/**
 * @OA\Schema(
 *   @OA\Property(property="id", type="integer", format="int32", example=1),
 *   @OA\Property(property="from", type="string", minLength=1, maxLength=1, example="a"),
 *   @OA\Property(property="to", type="string", minLength=1, maxLength=1, example="d"),
 *   @OA\Property(property="size", type="integer", format="int64", example=2000339066880),
 * )
 */
class Bucket extends Model {

  use RefreshableAutoIncrements;

  protected $fillable = [
    'from',
    'to',
    'size',
  ];

  protected $hidden = [];

  protected function casts(): array {
    return [
      'created_at' => 'datetime:Y-m-d H:i:s',
    ];
  }
}
