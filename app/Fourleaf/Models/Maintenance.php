<?php

namespace App\Fourleaf\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Traits\RefreshableAutoIncrements;

class Maintenance extends Model {

  use RefreshableAutoIncrements;

  protected $table = 'fourleaf_maintenance';
  public $timestamps = null;

  protected $fillable = [
    'date',
    'description',
    'odometer',
  ];

  protected $hidden = [];

  public function parts() {
    return $this->hasMany(MaintenancePart::class, 'id_fourleaf_maintenance');
  }
}
