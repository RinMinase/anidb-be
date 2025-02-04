<?php

namespace App\Fourleaf\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\RefreshableAutoIncrements;

/**
 * @OA\Schema(
 *   @OA\Property(property="id", type="integer", format="int32", example=1),
 *   @OA\Property(property="type", type="string", example="engine_oil"),
 *   @OA\Property(property="label", type="string", example="Engine Oil"),
 * )
 */
class MaintenanceType extends Model {

  use RefreshableAutoIncrements;

  protected $table = 'fourleaf_maintenance_types';
  public $timestamps = null;

  protected $fillable = [
    'type',
    'label',
  ];

  protected $hidden = [];

  protected $casts = [];
}
