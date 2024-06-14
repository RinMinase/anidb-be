<?php

namespace App\Fourleaf\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *   schema="FourleafSettings",
 *   @OA\Property(property="key", type="string", example="some key"),
 *   @OA\Property(property="value", type="string", example="some value"),
 * )
 */
class Settings extends Model {

  protected $table = 'fourleaf_settings';
  public $timestamps = null;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'key',
    'value',
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
