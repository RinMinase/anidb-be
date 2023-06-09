<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'codec',
    'order',
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
  protected $casts = [];
}
