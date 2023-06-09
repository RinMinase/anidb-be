<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *   @OA\Property(property="id", type="integer", format="int32", example=1),
 *   @OA\Property(property="codec", type="string", example="AAC 2.0"),
 *   @OA\Property(
 *     property="order",
 *     type="integer",
 *     format="int32",
 *     nullable=true,
 *     example=null,
 *   ),
 * )
 */
class CodecAudio extends Model {

  protected $table = 'codec_audios';

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
