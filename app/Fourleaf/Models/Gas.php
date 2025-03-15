<?php

namespace App\Fourleaf\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Traits\RefreshableAutoIncrements;

/**
 * @OA\Schema(
 *   @OA\Property(property="id", type="integer", format="int32", example=1),
 *   @OA\Property(property="date", type="string", example="2020-10-20"),
 *   @OA\Property(property="fromBars", type="integer", format="int32", minimum=0, maximum=9, example=1),
 *   @OA\Property(property="toBars", type="integer", format="int32", minimum=0, maximum=9, example=1),
 *   @OA\Property(property="odometer", type="integer", format="int32", minimum=0, example=1000),
 *   @OA\Property(property="pricePerLiter", type="float", minimum=0, example=12.23),
 *   @OA\Property(property="litersFilled", type="float", minimum=0, example=12.23),
 * )
 */
class Gas extends Model {

  use RefreshableAutoIncrements;

  protected $table = 'fourleaf_gas';
  public $timestamps = null;

  protected $fillable = [
    'date',
    'from_bars',
    'to_bars',
    'odometer',
    'price_per_liter',
    'liters_filled',
  ];

  protected $hidden = [];
}
