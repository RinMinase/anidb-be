<?php

namespace App\Fourleaf\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *   @OA\Property(property="date", type="string", example="2020-10-20"),
 *   @OA\Property(property="from_bars", type="integer", format="int32", minimum=0, maximum=9, example=1),
 *   @OA\Property(property="to_bars", type="integer", format="int32", minimum=0, maximum=9, example=1),
 *   @OA\Property(property="odometer", type="integer", format="int32", minimum=0, example=1000),
 *   @OA\Property(property="price_per_liter", type="float", minimum=0, example=12.23),
 *   @OA\Property(property="liters_filled", type="float", minimum=0, example=12.23),
 * )
 */
class Gas extends Model {

  protected $table = 'fourleaf_gas';
  public $timestamps = null;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'date',
    'from_bars',
    'to_bars',
    'odometer',
    'price_per_liter',
    'liters_filled',
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = [];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [];
}
