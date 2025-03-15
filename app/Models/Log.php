<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Traits\RefreshableAutoIncrements;

/**
 * @OA\Schema(
 *   @OA\Property(
 *     property="uuid",
 *     type="string",
 *     format="uuid",
 *     example="9ef81943-78f0-4d1c-a831-a59fb5af339c",
 *   ),
 *   @OA\Property(property="tableChanged", type="string", example="marathon"),
 *   @OA\Property(property="idChanged", type="string", example=1),
 *   @OA\Property(
 *     property="description",
 *     type="string",
 *     example="title changed from 'old' to 'new'",
 *   ),
 *   @OA\Property(property="action", type="string", example="add"),
 *   @OA\Property(
 *     property="createdAt",
 *     type="string",
 *     format="date-time",
 *     example="2020-01-01 00:00:00",
 *   ),
 * )
 */
class Log extends Model {

  use RefreshableAutoIncrements;

  // disables updated_at timestamp
  public $timestamps = ["created_at"];
  const UPDATED_AT = null;

  protected $fillable = [
    'uuid',
    'table_changed',
    'id_changed',
    'description',
    'action',
    'created_at',
  ];

  protected $hidden = [
    'id',
  ];

  protected function casts(): array {
    return [
      'created_at' => 'datetime:Y-m-d H:i:s',
    ];
  }
}
