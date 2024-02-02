<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *   @OA\Property(property="id", type="integer", format="int32", example=1),
 *   @OA\Property(property="title", type="string", example="Summer List"),
 *   @OA\Property(property="date_from", type="string", format="date", example="2020-01-01"),
 *   @OA\Property(property="date_to", type="string", format="date", example="2020-02-01"),
 * )
 */
class Sequence extends Model {
  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'title',
    'date_from',
    'date_to',
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = [
    'created_at',
    'updated_at',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [
    'created_at' => 'datetime:Y-m-d H:i:s',
  ];
}
