<?php

namespace App\Fourleaf\Models;

use App\Models\BaseModel;

/**
 * @OA\Schema(
 *   @OA\Property(property="date", type="string", example="2020-10-20 13:00"),
 *   @OA\Property(property="reading", type="integer", format="int32", minimum=0, example=1234),
 * )
 */
class Electricity extends BaseModel {

  protected $table = 'fourleaf_electricity';
  public $timestamps = null;

  protected $fillable = [
    'datetime',
    'reading',
  ];

  protected $hidden = [];

  protected $casts = [
    'datetime' => 'datetime:Y-m-d H:i',
  ];
}
