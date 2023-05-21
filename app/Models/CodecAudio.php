<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *   example={
 *     "id": 1,
 *     "codec": "AAC 2.0",
 *     "order": null,
 *   },
 *   @OA\Property(property="id", type="integer", format="int32"),
 *   @OA\Property(property="codec", type="string"),
 *   @OA\Property(property="order", type="integer", format="int32", nullable=true),
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
