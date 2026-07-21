<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Traits\RefreshableAutoIncrements;

class ElectricityReading extends Model {

  use RefreshableAutoIncrements;

  protected $table = 'electricity_readings';
  public $timestamps = null;

  protected $fillable = [
    'date',
    'reading',
  ];

  protected $hidden = [];

  protected function casts(): array {
    return [
      'date' => 'datetime:Y-m-d H:i:s',
    ];
  }
}
