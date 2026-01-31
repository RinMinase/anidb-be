<?php

namespace App\Fourleaf\Models;

use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OA;

use App\Models\Traits\RefreshableAutoIncrements;

#[OA\Schema(
  properties: [
    new OA\Property(property: "id", type: "integer", format: "int32", example: 1),
    new OA\Property(property: "type", type: "string", example: "engine_oil"),
    new OA\Property(property: "label", type: "string", example: "Engine Oil"),
    new OA\Property(property: "km", type: "integer", format: "int32", example: 10000),
    new OA\Property(property: "year", type: "integer", format: "int32", example: 2000),
  ]
)]
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
