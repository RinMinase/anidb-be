<?php

namespace App\Fourleaf\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Traits\RefreshableAutoIncrements;

/**
 * @OA\Schema(
 *   @OA\Property(property="id", type="integer", format="int32", example=1),
 *   @OA\Property(property="type", type="string", example="engine_oil"),
 *   @OA\Property(property="label", type="string", example="Engine Oil"),
 *   @OA\Property(property="km", type="integer", format="int32", example=10000),
 *   @OA\Property(property="year", type="integer", format="int32", example=2000),
 * )
 */
class MaintenanceType extends Model {

  use RefreshableAutoIncrements;

  protected $table = 'fourleaf_maintenance_types';
  public $timestamps = null;

  protected $fillable = [
    'type',
    'label',
    'km',
    'year',
  ];

  protected $hidden = [];
}
