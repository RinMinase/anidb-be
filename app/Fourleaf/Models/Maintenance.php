<?php

namespace App\Fourleaf\Models;

use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model {

  protected $table = 'fourleaf_maintenance';
  public $timestamps = null;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'date',
    'description',
    'odometer',
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

  public function parts() {
    return $this->hasMany(MaintenancePart::class, 'id_fourleaf_maintenance');
  }
}
