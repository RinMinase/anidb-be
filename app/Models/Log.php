<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *   example={
 *     "id": "9ef81943-78f0-4d1c-a831-a59fb5af339c",
 *     "table_changed": "marathon",
 *     "id_changed": 1,
 *     "description": "title changed from 'old' to 'new'",
 *     "action": "add",
 *     "created_at": "2020-01-01 00:00:00",
 *   },
 *   @OA\Property(property="id", type="string", format="uuid"),
 *   @OA\Property(property="table_changed", type="string"),
 *   @OA\Property(property="id_changed", type="string"),
 *   @OA\Property(property="description", type="string"),
 *   @OA\Property(property="action", type="string"),
 *   @OA\Property(property="created_at", type="string", format="date-time"),
 * )
 */
class Log extends Model {

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'uuid',
    'table_changed',
    'id_changed',
    'description',
    'action',
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = [
    'id',
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
