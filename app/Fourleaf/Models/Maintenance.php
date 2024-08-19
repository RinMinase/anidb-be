<?php

namespace App\Fourleaf\Models;

use App\Models\BaseModel;

class Maintenance extends BaseModel {

  protected $table = 'fourleaf_maintenance';
  public $timestamps = null;

  protected $fillable = [
    'date',
    'description',
    'odometer',
  ];

  protected $hidden = [];

  protected $casts = [];

  public function parts() {
    return $this->hasMany(MaintenancePart::class, 'id_fourleaf_maintenance');
  }
}
