<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Traits\RefreshableAutoIncrements;

class CarMaintenance extends Model {

  use RefreshableAutoIncrements;

  protected $table = 'car_maintenance';
  public $timestamps = null;

  protected $fillable = [
    'date',
    'description',
    'odometer',
  ];

  protected $hidden = [];

  public function parts() {
    return $this->hasMany(CarMaintenancePart::class, 'id_car_maintenance');
  }
}
