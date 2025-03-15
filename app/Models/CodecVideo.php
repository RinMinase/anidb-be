<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Traits\RefreshableAutoIncrements;

/**
 * @OA\Schema(
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="codec", type="string", example="x264 8bit"),
 *   @OA\Property(
 *     property="order",
 *     type="integer",
 *     format="int32",
 *     nullable=true,
 *     example=null,
 *   ),
 * )
 */
class CodecVideo extends Model {

  use RefreshableAutoIncrements;

  protected $fillable = [
    'codec',
    'order',
  ];

  protected $hidden = [
    'created_at',
    'updated_at',
  ];
}
