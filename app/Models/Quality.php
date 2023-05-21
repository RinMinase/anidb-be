<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *   example={
 *     "id": 1,
 *     "quality": "4K 2160p",
 *   },
 *   @OA\Property(property="id", type="integer", format="int32"),
 *   @OA\Property(
 *     property="quality",
 *     type="string",
 *     enum={"4K 2160", "FHD 1080p", "HD 720p", "HQ 480p", "LQ 360p"}
 *   ),
 * )
 */
class Quality extends Model {

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = [];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [];
}
