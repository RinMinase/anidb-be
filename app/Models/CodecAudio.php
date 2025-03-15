<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Traits\RefreshableAutoIncrements;

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

  use RefreshableAutoIncrements;

  protected $table = 'codec_audios';

  protected $fillable = [
    'codec',
    'order',
  ];

  protected $hidden = [
    'created_at',
    'updated_at',
  ];
}
