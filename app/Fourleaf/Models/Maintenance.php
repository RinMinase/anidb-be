<?php

namespace App\Fourleaf\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *   @OA\Property(property="date", type="string", example="2020-10-20"),
 *   @OA\Property(property="part", type="string", example="Engine Oil Change"),
 *   @OA\Property(property="odometer", type="integer", format="int32", minimum=0, example=1000),
 * )
 */
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
    'part',
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
}
