<?php

namespace App\Fourleaf\Models;

use App\Models\BaseModel;

/**
 * @OA\Schema(
 *   @OA\Property(property="date", type="string", example="2020-10-20"),
 *   @OA\Property(property="part", type="string", example="Engine Oil Change"),
 *   @OA\Property(property="odometer", type="integer", format="int32", minimum=0, example=1000),
 * )
 */
class MaintenancePart extends BaseModel {

  protected $table = 'fourleaf_maintenance_parts';
  public $timestamps = null;

  protected $fillable = [
    'id_fourleaf_maintenance',
    'part',
  ];

  protected $hidden = [];

  protected $casts = [];
}
