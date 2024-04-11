<?php

namespace App\Fourleaf\Models;

use Illuminate\Database\Eloquent\Model;

class Gas extends Model {

  protected $table = 'fourleaf_gas';
  public $timestamps = null;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'date',
    'from_bars',
    'to_bars',
    'odometer',
    'price_per_liter',
    'liters_filled',
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
}
