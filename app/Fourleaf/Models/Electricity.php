<?php

namespace App\Fourleaf\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *   @OA\Property(property="date", type="string", example="2020-10-20 13:00"),
 *   @OA\Property(property="reading", type="integer", format="int32", minimum=0, example=1234),
 * )
 */
class Electricity extends Model {

  protected $table = 'fourleaf_electricity';
  public $timestamps = null;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'datetime',
    'reading',
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
  protected $casts = [
    'datetime' => 'datetime:Y-m-d H:i',
  ];
}
