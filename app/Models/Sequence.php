<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *   example={
 *     "id": 1,
 *     "title": "Summer List",
 *     "date_from": "2020-01-01",
 *     "date_to": "2020-02-01",
 *     "created_at": "2020-01-01 00:00:00",
 *   },
 *   @OA\Property(property="id", type="integer", format="int32"),
 *   @OA\Property(property="title", type="string"),
 *   @OA\Property(property="date_from", type="string", format="date"),
 *   @OA\Property(property="date_to", type="string", format="date"),
 *   @OA\Property(property="created_at", type="string", format="date-time"),
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
    'created_at' => 'datetime:Y-m-d H:m:s',
  ];
}
