<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\RefreshableAutoIncrements;

/**
 * @OA\Schema(
 *   @OA\Property(
 *     property="id",
 *     type="string",
 *     format="uuid",
 *     example="9ef81943-78f0-4d1c-a831-a59fb5af339c",
 *   ),
 *   @OA\Property(property="type", type="string", example="json"),
 *   @OA\Property(
 *     property="expires",
 *     type="string",
 *     format="date-time",
 *     example="2020-01-01 00:00:00"
 *   ),
 *   @OA\Property(property="isFinished", type="boolean", example=false),
 *   @OA\Property(property="isAutomated", type="boolean", example=true),
 *   @OA\Property(
 *     property="createdAt",
 *     type="string",
 *     format="date-time",
 *     example="2020-01-01 00:00:00",
 *   ),
 * )
 */
class Export extends Model {

  use RefreshableAutoIncrements;

  protected $primaryKey = 'id';
  public $incrementing = false;
  protected $keyType = 'string';

  // disables updated_at timestamp
  public $timestamps = ["created_at"];
  const UPDATED_AT = null;

  protected $fillable = [
    'type',
    'expires',
    'is_finished',
    'is_automated',
  ];

  protected $hidden = [];

  protected $casts = [
    'created_at' => 'datetime:Y-m-d H:i:s',
  ];
}
