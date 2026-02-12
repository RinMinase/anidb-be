<?php

namespace App\Fourleaf\Models;

use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OA;

use App\Models\Traits\RefreshableAutoIncrements;

#[OA\Schema(
  properties: [
    new OA\Property(property: "date", type: "string", example: "2020-10-20"),
    new OA\Property(property: "part", type: "string", example: "Engine Oil Change"),
    new OA\Property(property: "odometer", type: "integer", format: "int32", minimum: 0, example: 1000),
  ]
)]
class MaintenancePart extends Model {

  use RefreshableAutoIncrements;

  protected $table = 'fourleaf_maintenance_parts';
  public $timestamps = null;

  protected $fillable = [
    'id_fourleaf_maintenance',
    'id_fourleaf_maintenance_type',
  ];

  protected $hidden = [];

  public function maintenance() {
    return $this->belongsTo(Maintenance::class, 'id_fourleaf_maintenance');
  }

  public function type() {
    return $this->belongsTo(MaintenanceType::class, 'id_fourleaf_maintenance_type');
  }
}
